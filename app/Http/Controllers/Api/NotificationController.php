<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $category = $request->query('category');

        $notifications = $request->user()
            ->notifications()
            ->when(in_array($category, ['task', 'general'], true), function ($query) use ($category) {
                if ($category === 'task') {
                    $query->where('data->category', 'task');
                    return;
                }

                $query->where(function ($subQuery) {
                    $subQuery->whereNull('data->category')
                        ->orWhere('data->category', '!=', 'task');
                });
            })
            ->paginate(20);

        return response()->json($notifications);
    }

    public function unreadCount(Request $request)
    {
        return response()->json([
            'count' => $request->user()->unreadNotifications()->count(),
        ]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return response()->json(['message' => 'Marked as read']);
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json(['message' => 'All marked as read']);
    }
}
