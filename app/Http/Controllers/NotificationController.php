<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $notifications = Notification::where('user_id', auth()->id())
            ->latest()
            ->take(15)
            ->get()
            ->map(function (Notification $notification) {
                return [
                    'id' => $notification->id,
                    'type' => $notification->type,
                    'title' => $notification->title,
                    'body' => $notification->body,
                    'booking_id' => $notification->booking_id,
                    'is_read' => $notification->read_at !== null,
                    'action_url' => $this->actionUrl($notification),
                    'time' => $notification->created_at->diffForHumans(),
                ];
            });

        return response()->json(['success' => true, 'notifications' => $notifications]);
    }

    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->count();

        return response()->json(['success' => true, 'count' => $count]);
    }

    public function read(Request $request, Notification $notification): JsonResponse
    {
        if ($notification->user_id !== auth()->id()) {
            abort(403);
        }

        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    public function readAll(): JsonResponse
    {
        Notification::where('user_id', auth()->id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return response()->json(['success' => true]);
    }

    private function actionUrl(Notification $notification): ?string
    {
        if (! $notification->booking_id) {
            return null;
        }

        return match (auth()->user()->role) {
            'admin' => route('admin.messages.index', $notification->booking_id),
            'vendor' => route('vendor.messages.index', $notification->booking_id),
            'customer' => route('customer.messages.index', $notification->booking_id),
            default => null,
        };
    }
}
