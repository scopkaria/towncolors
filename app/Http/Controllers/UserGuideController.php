<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserGuideController extends Controller
{
    public function show(): View
    {
        $user = auth()->user();
        $rawRole = $user?->role;
        $role = is_object($rawRole) && property_exists($rawRole, 'value')
            ? $rawRole->value
            : (string) $rawRole;

        $roleLabel = is_object($rawRole) && method_exists($rawRole, 'label')
            ? $rawRole->label()
            : ucfirst($role);

        $sections = match ($role) {
            'admin' => $this->adminSections(),
            'freelancer' => $this->freelancerSections(),
            default => $this->clientSections(),
        };

        return view('guide.show', [
            'sections' => $sections,
            'roleLabel' => $roleLabel,
        ]);
    }

    private function adminSections(): array
    {
        return [
            [
                'title' => 'Core Operations',
                'description' => 'Daily operations to run projects, handle communication, and keep delivery moving.',
                'items' => [
                    ['name' => 'Overview', 'what' => 'A quick operational summary of the platform health and key actions.'],
                    ['name' => 'Dashboard', 'what' => 'High-level business snapshot: projects, invoices, and activity.'],
                    ['name' => 'Projects', 'what' => 'Create, assign, and change project status.'],
                    ['name' => 'Messages', 'what' => 'Chat with clients and freelancers in one place.'],
                    ['name' => 'Live Chat', 'what' => 'Handle incoming visitor conversations from the website.'],
                    ['name' => 'Invoices', 'what' => 'Create invoices, update records, and log payments.'],
                    ['name' => 'Leads', 'what' => 'Review incoming leads and convert them into clients/projects.'],
                ],
            ],
            [
                'title' => 'Website Management',
                'description' => 'Control public content and marketing pages.',
                'items' => [
                    ['name' => 'Pages', 'what' => 'Create and update CMS pages and their sections.'],
                    ['name' => 'Blog', 'what' => 'Publish or edit blog posts for SEO and updates.'],
                    ['name' => 'Portfolio', 'what' => 'Review and approve showcased work.'],
                    ['name' => 'Categories', 'what' => 'Maintain service categories and pricing metadata.'],
                    ['name' => 'Subscribers', 'what' => 'Manage newsletter subscribers and exports.'],
                ],
            ],
            [
                'title' => 'System Controls',
                'description' => 'Configure internal operations, users, and assets.',
                'items' => [
                    ['name' => 'Media Library', 'what' => 'Upload and organize reusable files and images.'],
                    ['name' => 'Users', 'what' => 'Create client/freelancer users and send their credentials.'],
                    ['name' => 'Freelancers', 'what' => 'Review freelancer list and performance context.'],
                    ['name' => 'Freelancer Bills', 'what' => 'Approve/reject freelancer invoices and payouts.'],
                    ['name' => 'Settings', 'what' => 'Set branding, logos, and platform-wide preferences.'],
                ],
            ],
            [
                'title' => 'Subscriptions & Files',
                'description' => 'Monetization and client workspace controls.',
                'items' => [
                    ['name' => 'Subscriptions', 'what' => 'Manage active subscriptions and assignment.'],
                    ['name' => 'Sub Requests', 'what' => 'Approve or reject incoming subscription requests.'],
                    ['name' => 'Plans', 'what' => 'Define subscription packages and pricing.'],
                    ['name' => 'Client Files', 'what' => 'Review and manage all client uploaded files.'],
                ],
            ],
        ];
    }

    private function clientSections(): array
    {
        return [
            [
                'title' => 'Work Area',
                'description' => 'Your project workspace for day-to-day progress.',
                'items' => [
                    ['name' => 'Overview', 'what' => 'Your main control center for account status, tasks, and next actions.'],
                    ['name' => 'Dashboard', 'what' => 'Summary of project progress and key stats.'],
                    ['name' => 'Projects', 'what' => 'Create and track your submitted projects.'],
                    ['name' => 'Messages', 'what' => 'Chat directly with the team. Requires active subscription.'],
                    ['name' => 'My Files', 'what' => 'Upload and organize private project files. Requires active subscription.'],
                    ['name' => 'Invoices', 'what' => 'Review invoices and payment records.'],
                    ['name' => 'My Plan', 'what' => 'Manage your subscription and billing cycle.'],
                    ['name' => 'Checklist', 'what' => 'Track progress items set by admin as read-only milestones.'],
                ],
            ],
            [
                'title' => 'Plan & Progress',
                'description' => 'Subscription and onboarding progress visibility.',
                'items' => [
                    ['name' => 'My Plan', 'what' => 'View subscription status and request plan changes.'],
                    ['name' => 'Checklist', 'what' => 'See progress checklist set by admin.'],
                    ['name' => 'Profile Settings', 'what' => 'Update account details and password.'],
                ],
            ],
        ];
    }

    private function freelancerSections(): array
    {
        return [
            [
                'title' => 'Delivery Workspace',
                'description' => 'Everything needed to deliver assigned work.',
                'items' => [
                    ['name' => 'Overview', 'what' => 'See your current delivery context and outstanding priorities.'],
                    ['name' => 'Dashboard', 'what' => 'Performance overview and current workload.'],
                    ['name' => 'Projects', 'what' => 'View assigned projects and update statuses.'],
                    ['name' => 'Messages', 'what' => 'Coordinate with clients/admin via chat.'],
                    ['name' => 'Invoices', 'what' => 'Submit and track freelancer invoices.'],
                    ['name' => 'Checklist', 'what' => 'View assigned client checklist items in read-only mode.'],
                    ['name' => 'Portfolio', 'what' => 'Upload and manage work samples.'],
                    ['name' => 'Earnings', 'what' => 'Track agreed payments and paid amounts.'],
                ],
            ],
            [
                'title' => 'Reference & Account',
                'description' => 'Tools and account settings you may need quickly.',
                'items' => [
                    ['name' => 'Client Files Access', 'what' => 'Download shared client files for assigned projects.'],
                    ['name' => 'Profile Settings', 'what' => 'Update personal details and password.'],
                ],
            ],
        ];
    }
}
