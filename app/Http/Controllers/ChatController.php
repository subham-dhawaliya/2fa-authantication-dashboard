<?php

namespace App\Http\Controllers;

use App\Events\MessageSent;
use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ChatController extends Controller
{
    /**
     * Show chat page with user list
     */
    public function index()
    {
        $users = User::where('id', '!=', Auth::id())->get();
        return view('chat.index', compact('users'));
    }

    /**
     * Get messages between current user and selected user
     */
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

        // Mark messages as read
        Message::where('sender_id', $user->id)
               ->where('receiver_id', Auth::id())
               ->where('is_read', false)
               ->update(['is_read' => true]);

        return response()->json([
            'messages' => $messages->map(function ($msg) {
                return [
                    'id' => $msg->id,
                    'message' => $msg->message,
                    'sender_id' => $msg->sender_id,
                    'sender_name' => $msg->sender->name,
                    'sender_avatar' => $msg->sender->profile_picture 
                        ? asset('storage/' . $msg->sender->profile_picture) 
                        : ($msg->sender->avatar ?? null),
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

    /**
     * Send a message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
        ]);

        $message = Message::create([
            'sender_id' => Auth::id(),
            'receiver_id' => $request->receiver_id,
            'message' => $request->message,
        ]);

        // Broadcast the message
        broadcast(new MessageSent($message, Auth::user()))->toOthers();

        return response()->json([
            'success' => true,
            'message' => [
                'id' => $message->id,
                'message' => $message->message,
                'sender_id' => $message->sender_id,
                'created_at' => $message->created_at->timezone('Asia/Kolkata')->format('h:i A'),
            ]
        ]);
    }

    /**
     * Get unread message count
     */
    public function unreadCount()
    {
        $count = Message::where('receiver_id', Auth::id())
                        ->where('is_read', false)
                        ->count();
        
        return response()->json(['count' => $count]);
    }
}
