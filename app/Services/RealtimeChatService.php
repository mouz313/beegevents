<?php

namespace App\Services;

use App\Models\Message;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class RealtimeChatService
{
    public function publish(Message $message): bool
    {
        if (! config('firebase.enabled') || ! config('firebase.database_url')) {
            return false;
        }

        $url = rtrim(config('firebase.database_url'), '/')
            .'/messages/'.$message->booking_id.'/'.$message->id.'.json';

        $payload = [
            'id' => $message->id,
            'user_id' => $message->user_id,
            'name' => $message->user?->name ?? 'Unknown',
            'message' => $message->message,
            'created_at' => (int) $message->created_at->getTimestampMs(),
        ];

        try {
            Http::timeout(5)->put($url, $payload)->throw();
            return true;
        } catch (\Throwable $e) {
            Log::warning('Firebase realtime publish failed: '.$e->getMessage(), [
                'booking_id' => $message->booking_id,
                'message_id' => $message->id,
            ]);
            return false;
        }
    }
}
