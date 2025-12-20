<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ChatController extends Controller
{
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
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

        Message::where('sender_id', $user->id)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

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
                'created_at' => $message->created_at->timezone('Asia/Kolkata')->format('h:i A'),
            ]
        ]);
    }

    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
        
        return response()->json(['count' => $count]);
    }
}
