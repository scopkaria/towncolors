<?php

namespace App\Http\Controllers;

use App\Enums\UserRole;
use App\Models\Conversation;
use App\Models\ConversationParticipant;
use App\Models\FreelancerInvoice;
use App\Models\FreelancerPayment;
use App\Models\Invoice;
use App\Models\Lead;
use App\Models\Message;
use App\Models\Project;
use App\Models\User;
use App\Services\AiAssistant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function redirect(Request $request): RedirectResponse
    {
        return redirect($request->user()->dashboardPath());
    }

    public function index(Request $request): View
    {
        $user = $request->user();
        $role = $user->role;

        return view('dashboard.index', [
            'role'      => $role,
            'dashboard' => $this->dashboardContent($role, $user),
        ]);
    }

    public function section(Request $request, string $section): View
    {
        $user = $request->user();
        $role = $user->role;

        $sectionContent = Arr::get($this->sectionContent($role, $user), $section);

        abort_unless($sectionContent, 404);

        return view('dashboard.section', [
            'role'    => $role,
            'section' => $section,
            'content' => $sectionContent,
        ]);
    }

    private function dashboardContent(UserRole $role, User $user): array
    {
        return match ($role) {
            UserRole::ADMIN      => $this->adminDashboard($user),
            UserRole::CLIENT     => $this->clientDashboard($user),
            UserRole::FREELANCER => $this->freelancerDashboard($user),
        };
    }

    private function sectionContent(UserRole $role, User $user): array
    {
        return match ($role) {
            UserRole::ADMIN      => $this->adminSections($user),
            UserRole::CLIENT     => $this->clientSections($user),
            UserRole::FREELANCER => $this->freelancerSections($user),
        };
    }

    // ── Admin ────────────────────────────────────────────────────────────────

    private function adminDashboard(User $user): array
    {
        // ── Core counts ──────────────────────────────────────────────────────
        $totalProjects  = Project::count();
        $activeProjects = Project::whereIn('status', ['assigned', 'in_progress'])->count();
        $totalRevenue   = (float) Invoice::sum('paid_amount');
        $pendingCount   = Invoice::whereIn('status', ['unpaid', 'partial'])->count();

        // ── Revenue: this month vs last month ────────────────────────────────
        $revenueThisMonth  = (float) Invoice::whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->sum('paid_amount');
        $revenueLastMonth  = (float) Invoice::whereYear('updated_at', now()->subMonth()->year)
            ->whereMonth('updated_at', now()->subMonth()->month)
            ->sum('paid_amount');
        $revenueTrend      = $revenueLastMonth > 0
            ? round((($revenueThisMonth - $revenueLastMonth) / $revenueLastMonth) * 100, 1)
            : ($revenueThisMonth > 0 ? 100 : 0);
        $outstandingAmount = (float) Invoice::whereIn('status', ['unpaid', 'partial'])->sum('total_amount');

        // ── 6-month revenue sparkline ────────────────────────────────────────
        $sparklineMonths = collect(range(5, 0))->map(function (int $ago) {
            $d = now()->subMonths($ago);
            return [
                'label'  => $d->format('M'),
                'amount' => (float) Invoice::whereYear('updated_at', $d->year)
                    ->whereMonth('updated_at', $d->month)
                    ->sum('paid_amount'),
            ];
        })->values();

        // SVG polyline points (200×44 viewBox)
        $sparkMax    = max(1, $sparklineMonths->max('amount'));
        $sparkPoints = $sparklineMonths->values()->map(function ($m, $i) use ($sparkMax, $sparklineMonths) {
            $x = $i === 0 ? 0 : round(($i / ($sparklineMonths->count() - 1)) * 200, 2);
            $y = round(44 - ($m['amount'] / $sparkMax) * 44, 2);
            return "{$x},{$y}";
        })->implode(' ');

        // ── Active projects list ─────────────────────────────────────────────
        $activeProjectsList = Project::with(['client:id,name', 'freelancer:id,name'])
            ->whereIn('status', ['pending', 'assigned', 'in_progress'])
            ->latest()
            ->take(6)
            ->get()
            ->map(fn ($p) => [
                'id'         => $p->id,
                'title'      => $p->title,
                'status'     => $p->status,
                'client'     => $p->client?->name ?? '—',
                'freelancer' => $p->freelancer?->name ?? 'Unassigned',
                'updated'    => $p->updated_at->diffForHumans(),
            ]);

        // ── Top freelancers ──────────────────────────────────────────────────
        $freelancers       = User::where('role', UserRole::FREELANCER)->get(['id', 'name']);
        $freelancerIds     = $freelancers->pluck('id');
        $projectCounts     = Project::whereIn('freelancer_id', $freelancerIds)
            ->selectRaw('freelancer_id, COUNT(*) as project_count')
            ->groupBy('freelancer_id')
            ->pluck('project_count', 'freelancer_id');
        $completedCounts   = Project::whereIn('freelancer_id', $freelancerIds)
            ->where('status', 'completed')
            ->selectRaw('freelancer_id, COUNT(*) as cnt')
            ->groupBy('freelancer_id')
            ->pluck('cnt', 'freelancer_id');
        $freelancerEarnings = FreelancerPayment::whereIn('freelancer_id', $freelancerIds)
            ->selectRaw('freelancer_id, SUM(paid_amount) as total')
            ->groupBy('freelancer_id')
            ->pluck('total', 'freelancer_id');

        $topFreelancers = $freelancers
            ->map(fn ($f) => [
                'name'      => $f->name,
                'initials'  => collect(explode(' ', $f->name))
                    ->filter()
                    ->map(fn ($p) => strtoupper(substr($p, 0, 1)))
                    ->take(2)->implode(''),
                'total'     => (int) ($projectCounts[$f->id] ?? 0),
                'completed' => (int) ($completedCounts[$f->id] ?? 0),
                'earnings'  => (float) ($freelancerEarnings[$f->id] ?? 0),
            ])
            ->filter(fn ($f) => $f['total'] > 0)
            ->sortByDesc('total')
            ->take(5)
            ->values();

        $maxFreelancerProjects = max(1, $topFreelancers->max('total') ?? 1);

        // ── Leads summary ────────────────────────────────────────────────────
        $newLeadsCount       = Lead::where('status', 'new')->count();
        $convertedThisMonth  = Lead::where('status', 'converted')
            ->whereYear('updated_at', now()->year)
            ->whereMonth('updated_at', now()->month)
            ->count();

        // ── Rich activity feed ───────────────────────────────────────────────
        $activity = collect();

        Project::with(['client:id,name'])
            ->latest()->take(4)->get()
            ->each(fn ($p) => $activity->push([
                'type'  => 'project',
                'icon'  => 'folder',
                'title' => "\"{$p->title}\"",
                'sub'   => 'New project · ' . ($p->client?->name ?? 'Unknown client'),
                'time'  => $p->created_at->diffForHumans(),
                'ts'    => $p->created_at->timestamp,
            ]));

        Invoice::where('paid_amount', '>', 0)
            ->with(['project:id,title'])
            ->latest('updated_at')->take(3)->get()
            ->each(fn ($inv) => $activity->push([
                'type'  => 'invoice',
                'icon'  => 'cash',
                'title' => 'TZS ' . number_format((float) $inv->paid_amount, 0),
                'sub'   => 'Payment — "' . ($inv->project?->title ?? 'N/A') . '"',
                'time'  => $inv->updated_at->diffForHumans(),
                'ts'    => $inv->updated_at->timestamp,
            ]));

        Lead::latest()->take(3)->get()
            ->each(fn ($l) => $activity->push([
                'type'  => 'lead',
                'icon'  => 'user-plus',
                'title' => $l->name,
                'sub'   => 'New lead · ' . ($l->project_type
                    ? (Lead::projectTypes()[$l->project_type] ?? $l->project_type)
                    : 'General inquiry'),
                'time'  => $l->created_at->diffForHumans(),
                'ts'    => $l->created_at->timestamp,
            ]));

        $activityFeed = $activity->sortByDesc('ts')->take(8)->values()->all();

        // ── Legacy activity (shared view compatibility) ───────────────────────
        $legacyActivity = collect($activityFeed)->map(fn ($a) => [
            'title' => $a['title'] . ' — ' . $a['sub'],
            'time'  => $a['time'],
        ])->all();

        return [
            'eyebrow'     => 'Operational command center',
            'title'       => 'Run the platform with a calm, complete view.',
            'description' => 'Track project activity, approvals, and revenue from one place.',
            'stats' => [
                ['label' => 'Total revenue',    'value' => 'TZS ' . number_format($totalRevenue, 0),    'delta' => 'TZS ' . number_format($revenueThisMonth, 0) . ' this month'],
                ['label' => 'Active projects',  'value' => $activeProjects,                             'delta' => "{$totalProjects} total on platform"],
                ['label' => 'Outstanding',      'value' => 'TZS ' . number_format($outstandingAmount, 0), 'delta' => "{$pendingCount} invoice(s) pending"],
                ['label' => 'New leads',        'value' => $newLeadsCount,                              'delta' => "{$convertedThisMonth} converted this month"],
            ],
            'highlights' => [
                ['title' => 'Project pipeline',  'body' => "{$totalProjects} total project(s) on the platform — {$activeProjects} active right now."],
                ['title' => 'Revenue collected', 'body' => 'TZS ' . number_format($totalRevenue, 0) . ' received from client invoices to date.'],
                ['title' => 'Pending invoices',  'body' => "{$pendingCount} invoice(s) are awaiting payment. Review and follow up where needed."],
            ],
            'activity'             => $legacyActivity ?: [['title' => 'No recent activity yet', 'time' => 'just now']],
            'ai_insights'          => app(AiAssistant::class)->dailySummary(),
            // ── Admin-exclusive rich data ────────────────────────────────────
            'revenue_this_month'   => $revenueThisMonth,
            'revenue_last_month'   => $revenueLastMonth,
            'revenue_trend'        => $revenueTrend,
            'outstanding_amount'   => $outstandingAmount,
            'sparkline_points'     => $sparkPoints,
            'sparkline_months'     => $sparklineMonths->toArray(),
            'active_projects_list' => $activeProjectsList->toArray(),
            'top_freelancers'      => $topFreelancers->toArray(),
            'max_fl_projects'      => $maxFreelancerProjects,
            'leads_new'            => $newLeadsCount,
            'leads_converted'      => $convertedThisMonth,
            'activity_feed'        => $activityFeed,
        ];
    }

    private function adminSections(User $user): array
    {
        $totalProjects     = Project::count();
        $activeProjects    = Project::whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = Project::where('status', 'completed')->count();
        $totalRevenue      = (float) Invoice::sum('paid_amount');
        $pendingRevenue    = (float) Invoice::whereIn('status', ['unpaid', 'partial'])->sum('total_amount');
        $paidRevenue       = (float) Invoice::where('status', 'paid')->sum('paid_amount');
        $totalConvs        = Conversation::count();
        $messagesToday     = Message::where('created_at', '>=', now()->startOfDay())->count();

        return [
            'projects' => [
                'eyebrow'     => 'Projects',
                'title'       => 'Portfolio health across every active account.',
                'description' => 'See load, risk, and pacing across the full delivery organisation.',
                'cards'       => [
                    ['title' => 'Total projects',     'value' => $totalProjects,     'meta' => 'All time across all clients'],
                    ['title' => 'Active projects',    'value' => $activeProjects,    'meta' => 'Assigned or in progress'],
                    ['title' => 'Completed projects', 'value' => $completedProjects, 'meta' => 'Successfully delivered'],
                ],
            ],
            'messages' => [
                'eyebrow'     => 'Messages',
                'title'       => 'Conversations across the platform.',
                'description' => 'Monitor all project and direct message threads.',
                'cards'       => [
                    ['title' => 'Total conversations', 'value' => $totalConvs,    'meta' => 'Project & direct threads'],
                    ['title' => 'Messages today',      'value' => $messagesToday,  'meta' => 'Sent since midnight'],
                    ['title' => 'Active threads',      'value' => $totalConvs,     'meta' => 'Across all roles'],
                ],
            ],
            'invoices' => [
                'eyebrow'     => 'Invoices',
                'title'       => 'Cash flow and collections at a glance.',
                'description' => 'Track outstanding invoices, collections, and payout timing.',
                'cards'       => [
                    ['title' => 'Revenue collected',   'value' => 'TZS ' . number_format($totalRevenue, 0),   'meta' => 'Total paid amount all time'],
                    ['title' => 'Outstanding balance', 'value' => 'TZS ' . number_format($pendingRevenue, 0), 'meta' => 'Unpaid or partial invoices'],
                    ['title' => 'Fully cleared',       'value' => 'TZS ' . number_format($paidRevenue, 0),    'meta' => 'Paid-status invoices'],
                ],
            ],
        ];
    }

    // ── Client ───────────────────────────────────────────────────────────────

    private function clientDashboard(User $user): array
    {
        $myProjects    = Project::where('client_id', $user->id)->count();
        $activeCount   = Project::where('client_id', $user->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();
        $pendingAmount = (float) Invoice::whereHas(
            'project', fn ($q) => $q->where('client_id', $user->id)
        )->whereIn('status', ['unpaid', 'partial'])->sum('total_amount');
        $invoiceCount  = Invoice::whereHas(
            'project', fn ($q) => $q->where('client_id', $user->id)
        )->whereIn('status', ['unpaid', 'partial'])->count();
        $unread        = $this->unreadConversationCount($user);

        $recentProjects = Project::where('client_id', $user->id)->latest()->take(2)->get();
        $recentInvoice  = Invoice::whereHas('project', fn ($q) => $q->where('client_id', $user->id))
            ->latest()
            ->first();

        $activity = [];
        foreach ($recentProjects as $p) {
            $activity[] = [
                'title' => "Project \"{$p->title}\" — " . ucwords(str_replace('_', ' ', $p->status)),
                'time'  => $p->updated_at->diffForHumans(),
            ];
        }
        if ($recentInvoice) {
            $activity[] = [
                'title' => 'Invoice status: ' . $recentInvoice->status . ' — TZS ' . number_format((float) $recentInvoice->total_amount, 0),
                'time'  => $recentInvoice->updated_at->diffForHumans(),
            ];
        }

        return [
            'eyebrow'     => 'Delivery cockpit',
            'title'       => 'Stay close to every project without chasing updates.',
            'description' => 'Monitor milestones, unblock conversations, and keep invoices tidy from one clear workspace.',
            'stats'       => [
                ['label' => 'My projects',      'value' => $myProjects,                                  'delta' => "{$activeCount} currently active"],
                ['label' => 'Active projects',  'value' => $activeCount,                                 'delta' => 'Assigned or in progress'],
                ['label' => 'Pending invoices', 'value' => $invoiceCount,                                'delta' => 'TZS ' . number_format($pendingAmount, 0) . ' outstanding'],
                ['label' => 'Unread messages',  'value' => $unread,                                      'delta' => $unread > 0 ? 'New replies waiting' : 'All caught up'],
            ],
            'highlights' => [
                ['title' => 'Projects overview', 'body' => "You have {$myProjects} project(s) in total — {$activeCount} currently active."],
                ['title' => 'Billing status',    'body' => "{$invoiceCount} invoice(s) pending — TZS " . number_format($pendingAmount, 0) . ' outstanding.'],
                ['title' => 'Inbox snapshot',    'body' => $unread > 0 ? "You have {$unread} unread conversation(s). Reply to keep projects moving." : 'Your inbox is up to date. No unread messages.'],
            ],
            'activity' => $activity ?: [['title' => 'No recent activity yet', 'time' => 'just now']],
        ];
    }

    private function clientSections(User $user): array
    {
        $totalProjects     = Project::where('client_id', $user->id)->count();
        $activeProjects    = Project::where('client_id', $user->id)->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = Project::where('client_id', $user->id)->where('status', 'completed')->count();
        $pendingAmount     = (float) Invoice::whereHas('project', fn ($q) => $q->where('client_id', $user->id))
            ->whereIn('status', ['unpaid', 'partial'])->sum('total_amount');
        $paidAmount        = (float) Invoice::whereHas('project', fn ($q) => $q->where('client_id', $user->id))
            ->where('status', 'paid')->sum('paid_amount');
        $allInvoices       = Invoice::whereHas('project', fn ($q) => $q->where('client_id', $user->id))->count();
        $unread            = $this->unreadConversationCount($user);
        $totalConvs        = ConversationParticipant::where('user_id', $user->id)->count();

        return [
            'projects' => [
                'eyebrow'     => 'Projects',
                'title'       => 'Your delivery plan, milestone by milestone.',
                'description' => 'Stay on top of what shipped, what is under review, and what is coming next.',
                'cards'       => [
                    ['title' => 'Total projects',     'value' => $totalProjects,     'meta' => 'All projects you have created'],
                    ['title' => 'Active projects',    'value' => $activeProjects,    'meta' => 'Assigned or in progress'],
                    ['title' => 'Completed projects', 'value' => $completedProjects, 'meta' => 'Successfully delivered'],
                ],
            ],
            'messages' => [
                'eyebrow'     => 'Messages',
                'title'       => 'Your conversations and updates.',
                'description' => 'View project threads and direct messages in one place.',
                'cards'       => [
                    ['title' => 'Total conversations', 'value' => $totalConvs, 'meta' => 'Threads you participate in'],
                    ['title' => 'Unread messages',     'value' => $unread,     'meta' => $unread > 0 ? 'Waiting on your reply' : 'All read'],
                    ['title' => 'All threads',         'value' => $totalConvs, 'meta' => 'Across all your projects'],
                ],
            ],
            'invoices' => [
                'eyebrow'     => 'Invoices',
                'title'       => 'Billing that stays transparent and predictable.',
                'description' => 'See what is pending, what is paid, and what needs action.',
                'cards'       => [
                    ['title' => 'Total invoices',      'value' => $allInvoices,                            'meta' => 'All invoices across your projects'],
                    ['title' => 'Outstanding amount',  'value' => 'TZS ' . number_format($pendingAmount, 0), 'meta' => 'Unpaid or partial invoices'],
                    ['title' => 'Amount paid',         'value' => 'TZS ' . number_format($paidAmount, 0),   'meta' => 'Cleared to date'],
                ],
            ],
        ];
    }

    // ── Freelancer ───────────────────────────────────────────────────────────

    private function freelancerDashboard(User $user): array
    {
        $assignedCount  = Project::where('freelancer_id', $user->id)
            ->whereIn('status', ['assigned', 'in_progress'])
            ->count();
        $totalEarnings  = (float) FreelancerPayment::where('freelancer_id', $user->id)->sum('paid_amount');
        $pendingTotal   = FreelancerPayment::where('freelancer_id', $user->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->sum(fn ($p) => max(0, (float) $p->agreed_amount - (float) $p->paid_amount));
        $unread         = $this->unreadConversationCount($user);

        $recentProjects = Project::where('freelancer_id', $user->id)->latest()->take(2)->get();
        $recentInvoice  = FreelancerInvoice::where('freelancer_id', $user->id)
            ->with('project:id,title')
            ->latest()
            ->first();

        $activity = [];
        foreach ($recentProjects as $p) {
            $activity[] = [
                'title' => "Project \"{$p->title}\" — " . ucwords(str_replace('_', ' ', $p->status)),
                'time'  => $p->updated_at->diffForHumans(),
            ];
        }
        if ($recentInvoice) {
            $activity[] = [
                'title' => 'Invoice ' . $recentInvoice->statusLabel() . ' — ' . ($recentInvoice->project->title ?? 'N/A'),
                'time'  => $recentInvoice->updated_at->diffForHumans(),
            ];
        }

        return [
            'eyebrow'     => 'Independent workbench',
            'title'       => 'Deliver top-tier work with less context switching.',
            'description' => 'See what matters today, protect your bandwidth, and keep approvals moving.',
            'stats'       => [
                ['label' => 'Assigned projects', 'value' => $assignedCount,                            'delta' => 'Active assignments'],
                ['label' => 'Total earnings',    'value' => 'TZS ' . number_format($totalEarnings, 0), 'delta' => 'Paid to you to date'],
                ['label' => 'Pending payout',    'value' => 'TZS ' . number_format($pendingTotal, 0),  'delta' => 'Awaiting disbursement'],
                ['label' => 'Unread messages',   'value' => $unread,                                   'delta' => $unread > 0 ? 'New replies' : 'All caught up'],
            ],
            'highlights' => [
                ['title' => 'Active workload',   'body' => "{$assignedCount} active project(s) currently assigned to you."],
                ['title' => 'Earnings overview', 'body' => 'You have earned TZS ' . number_format($totalEarnings, 0) . ' in total payments to date.'],
                ['title' => 'Payout status',     'body' => 'TZS ' . number_format($pendingTotal, 0) . ' is pending disbursement for completed work.'],
            ],
            'activity' => $activity ?: [['title' => 'No recent activity yet', 'time' => 'just now']],
        ];
    }

    private function freelancerSections(User $user): array
    {
        $assignedProjects  = Project::where('freelancer_id', $user->id)->whereIn('status', ['assigned', 'in_progress'])->count();
        $completedProjects = Project::where('freelancer_id', $user->id)->where('status', 'completed')->count();
        $totalProjects     = Project::where('freelancer_id', $user->id)->count();
        $totalEarnings     = (float) FreelancerPayment::where('freelancer_id', $user->id)->sum('paid_amount');
        $pendingTotal      = FreelancerPayment::where('freelancer_id', $user->id)
            ->whereIn('status', ['unpaid', 'partial'])
            ->get()
            ->sum(fn ($p) => max(0, (float) $p->agreed_amount - (float) $p->paid_amount));
        $pendingInvoices   = FreelancerInvoice::where('freelancer_id', $user->id)->where('status', 'pending')->count();
        $approvedInvoices  = FreelancerInvoice::where('freelancer_id', $user->id)->where('status', 'approved')->count();
        $unread            = $this->unreadConversationCount($user);
        $totalConvs        = ConversationParticipant::where('user_id', $user->id)->count();

        return [
            'projects' => [
                'eyebrow'     => 'Projects',
                'title'       => 'Your active workload, neatly prioritised.',
                'description' => 'Balance deadlines, approvals, and handoffs without digging through threads.',
                'cards'       => [
                    ['title' => 'Assigned projects',  'value' => $assignedProjects,  'meta' => 'Active assignments'],
                    ['title' => 'Completed projects', 'value' => $completedProjects, 'meta' => 'Successfully delivered'],
                    ['title' => 'Total projects',     'value' => $totalProjects,      'meta' => 'All time'],
                ],
            ],
            'messages' => [
                'eyebrow'     => 'Messages',
                'title'       => 'Important briefs and feedback, ready to action.',
                'description' => 'See what changed, what needs a reply, and what is now approved.',
                'cards'       => [
                    ['title' => 'Total conversations', 'value' => $totalConvs, 'meta' => 'Threads you participate in'],
                    ['title' => 'Unread messages',     'value' => $unread,     'meta' => $unread > 0 ? 'Waiting on your reply' : 'All read'],
                    ['title' => 'Active threads',      'value' => $totalConvs, 'meta' => 'Across all projects'],
                ],
            ],
            'invoices' => [
                'eyebrow'     => 'Invoices',
                'title'       => 'Payout visibility you can plan around.',
                'description' => 'Track approved work, pending invoices, and the next money in.',
                'cards'       => [
                    ['title' => 'Total earnings',     'value' => 'TZS ' . number_format($totalEarnings, 0),                      'meta' => 'Total paid to you'],
                    ['title' => 'Pending payout',     'value' => 'TZS ' . number_format($pendingTotal, 0),                       'meta' => 'Awaiting disbursement'],
                    ['title' => 'Invoice status',     'value' => "{$approvedInvoices} approved / {$pendingInvoices} pending",   'meta' => 'Your freelancer invoices'],
                ],
            ],
        ];
    }

    // ── Helpers ──────────────────────────────────────────────────────────────

    private function unreadConversationCount(User $user): int
    {
        return ConversationParticipant::where('user_id', $user->id)
            ->with(['conversation' => fn ($q) => $q->with('latestMessage')])
            ->get()
            ->filter(function (ConversationParticipant $cp) use ($user) {
                $latest = $cp->conversation?->latestMessage;
                if (! $latest || $latest->sender_id === $user->id) {
                    return false;
                }
                return ! $cp->last_read_at || $cp->last_read_at < $latest->created_at;
            })
            ->count();
    }
}