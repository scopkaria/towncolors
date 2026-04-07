<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Project;
use App\Models\FreelancerPayment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        return match ($user->role->value) {
            'admin' => $this->adminDashboard(),
            'client' => $this->clientDashboard($user),
            'freelancer' => $this->freelancerDashboard($user),
        };
    }

    private function adminDashboard()
    {
        $totalProjects = Project::count();
        $pendingProjects = Project::where('status', 'pending')->count();
        $activeProjects = Project::whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = Project::where('status', 'completed')->count();

        $totalRevenue = Invoice::where('status', 'paid')->sum('total_amount');
        $pendingRevenue = Invoice::where('status', '!=', 'paid')->sum('total_amount');

        return response()->json([
            'role' => 'admin',
            'stats' => [
                'total_projects' => $totalProjects,
                'pending_projects' => $pendingProjects,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'total_revenue' => (float) $totalRevenue,
                'pending_revenue' => (float) $pendingRevenue,
            ],
            'recent_projects' => Project::with('client:id,name')->latest()->take(5)->get(),
        ]);
    }

    private function clientDashboard($user)
    {
        $projects = Project::where('client_id', $user->id);
        $totalProjects = $projects->count();
        $activeProjects = (clone $projects)->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = (clone $projects)->where('status', 'completed')->count();

        $totalInvoiced = Invoice::whereHas('project', fn($q) => $q->where('client_id', $user->id))->sum('total_amount');
        $totalPaid = Invoice::whereHas('project', fn($q) => $q->where('client_id', $user->id))->sum('paid_amount');

        $unreadMessages = $user->conversations()
            ->withCount(['messages as unread' => fn($q) => $q->where('messages.created_at', '>', \DB::raw('COALESCE(conversation_participants.last_read_at, "1970-01-01")'))])
            ->get()
            ->sum('unread');

        return response()->json([
            'role' => 'client',
            'stats' => [
                'total_projects' => $totalProjects,
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'total_invoiced' => (float) $totalInvoiced,
                'total_paid' => (float) $totalPaid,
                'unread_messages' => $unreadMessages,
            ],
            'recent_projects' => Project::where('client_id', $user->id)
                ->with('freelancer:id,name')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }

    private function freelancerDashboard($user)
    {
        $projects = Project::where('freelancer_id', $user->id);
        $activeProjects = (clone $projects)->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = (clone $projects)->where('status', 'completed')->count();

        $totalEarnings = FreelancerPayment::where('freelancer_id', $user->id)->sum('paid_amount');
        $pendingPayments = FreelancerPayment::where('freelancer_id', $user->id)
            ->selectRaw('SUM(agreed_amount - paid_amount) as pending')
            ->value('pending') ?? 0;

        return response()->json([
            'role' => 'freelancer',
            'stats' => [
                'active_projects' => $activeProjects,
                'completed_projects' => $completedProjects,
                'total_earnings' => (float) $totalEarnings,
                'pending_payments' => (float) $pendingPayments,
            ],
            'recent_projects' => Project::where('freelancer_id', $user->id)
                ->with('client:id,name')
                ->latest()
                ->take(5)
                ->get(),
        ]);
    }
}
