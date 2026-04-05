<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Response;
use Illuminate\View\View;

class SubscriberController extends Controller
{
    public function index(): View
    {
        $subscribers = Subscriber::latest()->get();

        return view('admin.subscribers.index', compact('subscribers'));
    }

    public function export(): Response
    {
        $subscribers = Subscriber::orderBy('email')->get();

        $csv = "Email,Subscribed At\n";
        foreach ($subscribers as $sub) {
            $csv .= '"' . addslashes($sub->email) . '",'
                  . '"' . $sub->created_at->format('Y-m-d H:i:s') . '"' . "\n";
        }

        return response($csv, 200, [
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="subscribers-' . now()->format('Y-m-d') . '.csv"',
        ]);
    }

    public function destroy(Subscriber $subscriber): \Illuminate\Http\RedirectResponse
    {
        $subscriber->delete();

        return back()->with('success', 'Subscriber removed.');
    }
}
