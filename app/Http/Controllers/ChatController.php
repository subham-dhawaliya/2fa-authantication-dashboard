<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Events\MessageRead;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $currentUserId = Auth::id();
        
        $users = User::where('id', '!=', $currentUserId)
            ->get()
            ->map(function ($user) use ($currentUserId) {
                // Get last message between current user and this user
                $lastMessage = Message::where(function ($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $currentUserId)
                          ->where('receiver_id', $user->id);
                })->orWhere(function ($query) use ($currentUserId, $user) {
                    $query->where('sender_id', $user->id)
                          ->where('receiver_id', $currentUserId);
                })
                ->orderBy('created_at', 'desc')
                ->first();
                
                // Get unread count from this user
                $unreadCount = Message::where('sender_id', $user->id)
                    ->where('receiver_id', $currentUserId)
                    ->where('is_read', false)
                    ->count();
                
                $user->last_message = $lastMessage ? ($lastMessage->attachment ? '📷 Photo' : $lastMessage->message) : null;
                $user->last_message_time = $lastMessage ? $lastMessage->created_at->timezone('Asia/Kolkata')->format('h:i A') : null;
                $user->last_message_is_mine = $lastMessage ? ($lastMessage->sender_id === $currentUserId) : false;
                $user->unread_count = $unreadCount;
                $user->last_message_at = $lastMessage ? $lastMessage->created_at : null;
                
                return $user;
            })
            ->sortByDesc('last_message_at')
            ->values();
        
        return view('chat.index', compact('users'));
    }

    public function getMessages(User $user)
    {
        $messages = Message::where(function ($query) use ($user) {
            $query->where('sender_id', Auth::id())
                  ->where('receiver_id', $user->id);
        })->orWhere(function ($query) use ($user) {
            $query->where('sender_id', $user->id)
                  ->where('receiver_id', Auth::id());
        })
        ->with('sender')
        ->orderBy('created_at', 'asc')
        ->get();

        // Mark messages as read and broadcast
        $unreadMessages = Message::where('sender_id', $user->id)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->get();
        
        if ($unreadMessages->count() > 0) {
            $messageIds = $unreadMessages->pluck('id')->toArray();
            Message::whereIn('id', $messageIds)->update(['is_read' => true]);
            
            // Broadcast read receipt to sender
            broadcast(new MessageRead($messageIds, Auth::id(), $user->id));
        }

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'attachment' => $msg->attachment ? asset('storage/' . $msg->attachment) : null,
                    'attachment_type' => $msg->attachment_type,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'is_mine' => $msg->sender_id === Auth::id(),
                    'is_read' => $msg->is_read,
                    'created_at' => $msg->created_at->timezone('Asia/Kolkata')->format('h:i A'),
                ];
            }),
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'avatar' => $user->profile_picture 
                    ? asset('storage/' . $user->profile_picture) 
                    : ($user->avatar ?? null),
            ]
        ]);
    }

    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'nullable|string|max:1000',
            'attachment' => 'nullable|file|max:10240', // 10MB max
        ]);

        $attachmentPath = null;
        $attachmentType = null;

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $attachmentPath = $file->store('chat-attachments', 'public');
            
            $mimeType = $file->getMimeType();
            if (str_starts_with($mimeType, 'image/')) {
                $attachmentType = 'image';
            } else {
                $attachmentType = 'file';
            }
        }

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message ?? '',
            'attachment' => $attachmentPath,
            'attachment_type' => $attachmentType,
            'is_read' => false,
        ]);

        broadcast(new MessageSent($message, Auth::user()))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'attachment' => $attachmentPath ? asset('storage/' . $attachmentPath) : null,
                'attachment_type' => $attachmentType,
                'sender_id' => $message->sender_id,
                'is_read' => false,
                'created_at' => $message->created_at->timezone('Asia/Kolkata')->format('h:i A'),
            ]
        ]);
    }

    public function markAsRead(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
            'sender_id' => 'required|exists:users,id',
        ]);

        Message::whereIn('id', $request->message_ids)
               ->where('receiver_id', Auth::id())
               ->update(['is_read' => true]);

        // Broadcast read receipt to sender
        broadcast(new MessageRead($request->message_ids, Auth::id(), $request->sender_id));

        return response()->json(['success' => true]);
    }

    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
        
        return response()->json(['count' => $count]);
    }
}
