<?php

namespace App\Http\Controllers;

use App\Events\CallSignal;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    public function signal(Request $request)
    {
        $request->validate([
            'receiver_id' => 'required|exists:users,id',
            'type' => 'required|string',
            'data' => 'nullable',
        ]);

        $caller = Auth::user();
        
        $signalData = [
            'type' => $request->type,
            'caller_id' => $caller->id,
            'caller_name' => $caller->name,
            'caller_avatar' => $caller->profile_picture 
                ? asset('storage/' . $caller->profile_picture) 
                : ($caller->avatar ?? null),
            'data' => $request->data,
            'call_type' => $request->call_type ?? 'audio',
        ];

        broadcast(new CallSignal($signalData, $request->receiver_id));

        return response()->json(['success' => true]);
    }
}
