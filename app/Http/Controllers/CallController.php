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
        // Handle sendBeacon requests (sent as blob with JSON)
        $data = $request->all();
        if (empty($data) || !isset($data['receiver_id'])) {
            $content = $request->getContent();
            if ($content) {
                $data = json_decode($content, true) ?? [];
            }
        }

        $receiverId = $data['receiver_id'] ?? null;
        $type = $data['type'] ?? null;
        $signalDataPayload = $data['data'] ?? null;
        $callType = $data['call_type'] ?? 'audio';

        if (!$receiverId || !$type) {
            return response()->json(['error' => 'Invalid request'], 400);
        }

        $caller = Auth::user();
        
        $signalData = [
            'type' => $type,
            'caller_id' => $caller->id,
            'caller_name' => $caller->name,
            'caller_avatar' => $caller->profile_picture 
                ? asset('storage/' . $caller->profile_picture) 
                : ($caller->avatar ?? null),
            'data' => $signalDataPayload,
            'call_type' => $callType,
        ];

        broadcast(new CallSignal($signalData, (int)$receiverId));

        return response()->json(['success' => true]);
    }
}
