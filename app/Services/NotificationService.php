<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NotificationService
{
    public function publish(Notification $notification): bool
    {
        if (! config('firebase.enabled') || ! config('firebase.database_url')) {
            return false;
        }

        $url = rtrim(config('firebase.database_url'), '/')
            .'/notifications/'.$notification->user_id.'/'.$notification->id.'.json';

        $payload = [
            'id' => $notification->id,
            'type' => $notification->type,
            'title' => $notification->title,
            'body' => $notification->body,
            'booking_id' => $notification->booking_id,
            'created_at' => (int) $notification->created_at->getTimestampMs(),
        ];

        try {
            Http::connectTimeout(1)->timeout(2)->put($url, $payload)->throw();
            return true;
        } catch (\Throwable $e) {
            Log::warning('Firebase notification publish failed: '.$e->getMessage(), [
                'notification_id' => $notification->id,
                'user_id' => $notification->user_id,
            ]);
            return false;
        }
    }

    /**
     * Notify every participant of a booking except the sender.
     */
    public function notifyParticipants(Booking $booking, int $senderId, string $title, string $body): void
    {
        $recipientIds = collect([$booking->customer_id])
            ->merge(User::where('role', 'admin')->pluck('id'))
            ->merge(
                $booking->bookingItems()
                    ->with('vendorProfile:id,user_id')
                    ->get()
                    ->pluck('vendorProfile.user_id')
            )
            ->filter(fn ($id) => $id !== null)
            ->unique()
            ->values()
            ->reject(fn ($id) => (int) $id === $senderId)
            ->values();

        foreach ($recipientIds as $userId) {
            $notification = Notification::create([
                'user_id' => $userId,
                'type' => 'message',
                'title' => $title,
                'body' => $body,
                'booking_id' => $booking->id,
                'sender_id' => $senderId,
            ]);

            $this->publish($notification);
        }
    }
}
