<?php

namespace App\Http\Controllers\Admin;

use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\View\View;

class FreelancerController extends Controller
{
    public function index(): View
    {
        $freelancers = User::where('role', UserRole::FREELANCER)
            ->withCount(['freelancerInvoices'])
            ->with(['freelancerInvoices' => fn ($q) => $q->latest()->limit(1)])
            ->orderBy('name')
            ->get()
            ->map(function (User $user) {
                $assignedProjects = \App\Models\Project::where('freelancer_id', $user->id)->count();
                $activeProjects   = \App\Models\Project::where('freelancer_id', $user->id)
                    ->whereNotIn('status', ['completed', 'cancelled'])
                    ->count();

                return (object) [
                    'id'               => $user->id,
                    'name'             => $user->name,
                    'email'            => $user->email,
                    'created_at'       => $user->created_at,
                    'assignedProjects' => $assignedProjects,
                    'activeProjects'   => $activeProjects,
                    'invoiceCount'     => $user->freelancer_invoices_count,
                ];
            });

        return view('admin.freelancers.index', compact('freelancers'));
    }
}
