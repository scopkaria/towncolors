<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Towncore') }}</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=manrope:400,500,600,700,800|sora:400,500,600,700&display=swap" rel="stylesheet" />

        {{-- Dark mode anti-flash --}}
        <script>
            (function(){try{var s=localStorage.getItem('darkMode');var sys=window.matchMedia('(prefers-color-scheme: dark)').matches;if(s==='true'||(s===null&&sys)){document.documentElement.classList.add('dark');}}catch(e){}})();
        </script>

        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @include('partials.theme-vars')
    </head>
    <body class="h-screen overflow-hidden font-sans antialiased transition-colors duration-300">
        @php
            $user = auth()->user();
            $role = $user?->role?->value ?? 'client';
            $hasActiveSubscription = $role !== 'client' || $user?->hasFullAccess();
            $guideRouteName = $role.'.guide';
            $hasGuideRoute = in_array($role, ['admin', 'client', 'freelancer'], true) && \Illuminate\Support\Facades\Route::has($guideRouteName);
            $guideUrl = $hasGuideRoute ? route($guideRouteName) : null;
            $tabHelp = [
                'Dashboard' => 'Overview of your activity and progress',
                'Projects' => 'Create and manage your work requests',
                'Messages' => 'Communicate with your assigned team',
                'My Files' => 'Upload and manage project files',
                'Invoices' => 'View your payments',
                'My Plan' => 'Manage your subscription',
                'Checklist' => 'Track setup and delivery progress',
                'User Guide' => 'Open the full help guide for your role',
            ];
            $initials = collect(explode(' ', $user?->name ?? 'TC'))
                ->filter()
                ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
                ->take(2)
                ->implode('');
            $recentNotifications = $user?->notifications()->latest()->take(5)->get() ?? collect();
            $unreadNotificationsCount = $user?->unreadNotifications()->count() ?? 0;

            // ── Heroicons outline paths (viewBox 0 0 24 24) ─────────────
            $ic = [
                'dashboard'   => 'M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z',
                'projects'    => 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z',
                'messages'    => 'M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.501 48.172 48.172 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z',
                'invoices'    => 'M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z',
                'pages'       => 'M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25Z',
                'blog'        => 'M16.862 4.487 18.549 2.8a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10',
                'portfolio'   => 'm2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909M13.5 12h.008v.008H13.5V12Zm-3 8.25h13.5a2.25 2.25 0 0 0 2.25-2.25V6.75a2.25 2.25 0 0 0-2.25-2.25H6.75a2.25 2.25 0 0 0-2.25 2.25v13.5c0 1.243 1.007 2.25 2.25 2.25Z',
                'categories'  => 'M9.568 3H5.25A2.25 2.25 0 0 0 3 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 0 0 5.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 0 0 9.568 3Z',
                'subscribers' => 'M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z',
                'media'       => 'M3.75 9.776c.112-.017.227-.026.344-.026h15.812c.117 0 .232.009.344.026m-16.5 0a2.25 2.25 0 0 0-1.883 2.542l.857 6a2.25 2.25 0 0 0 2.227 1.932H19.05a2.25 2.25 0 0 0 2.227-1.932l.857-6a2.25 2.25 0 0 0-1.883-2.542m-16.5 0V6A2.25 2.25 0 0 1 6 3.75h3.879a1.5 1.5 0 0 1 1.06.44l2.122 2.12a1.5 1.5 0 0 0 1.06.44H18A2.25 2.25 0 0 1 20.25 9v.776',
                'bills'       => 'M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z',
                'freelancers' => 'M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z',
                'settings'    => 'M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z',
                'earnings'    => 'M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z',
                'leads'       => 'M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM4 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 10.374 21c-2.331 0-4.512-.645-6.374-1.766Z',
                'guide'       => 'M9 12h6m-6 4h6M7.5 4.5h9A2.25 2.25 0 0 1 18.75 6.75v10.5A2.25 2.25 0 0 1 16.5 19.5h-9a2.25 2.25 0 0 1-2.25-2.25V6.75A2.25 2.25 0 0 1 7.5 4.5ZM9 8h6',
            ];

            // ── Flat nav items (client / freelancer) ────────────────────
            $items = [];
            if ($role === 'client') {
                $items = [
                    ['label' => 'Dashboard',     'route' => route('client.dashboard'),          'match' => 'client.dashboard',                             'icon' => $ic['dashboard']],
                    ['label' => 'Projects',      'route' => route('client.projects.index'),     'match' => 'client.projects.*',                            'icon' => $ic['projects']],
                ];

                if ($hasActiveSubscription) {
                    $items[] = ['label' => 'Messages', 'route' => route('client.messages'), 'match' => 'client.messages|chat.*', 'icon' => $ic['messages']];
                    $items[] = ['label' => 'My Files', 'route' => route('client.files.index'), 'match' => 'client.files.*', 'icon' => 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z'];
                }

                $items[] = ['label' => 'Invoices', 'route' => route('client.invoices'), 'match' => 'client.invoices|client.invoices.*', 'icon' => $ic['invoices']];
                $items[] = ['label' => 'My Plan', 'route' => route('client.subscription.show'), 'match' => 'client.subscription.*', 'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.745 3.745 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.745 3.745 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z'];
                $items[] = ['label' => 'User Guide', 'route' => route('client.guide'), 'match' => 'client.guide', 'icon' => $ic['guide']];
            }
            if ($role === 'freelancer') {
                $items = [
                    ['label' => 'Dashboard', 'route' => route('freelancer.dashboard'),          'match' => 'freelancer.dashboard',                              'icon' => $ic['dashboard']],
                    ['label' => 'Projects',  'route' => route('freelancer.projects.index'),     'match' => 'freelancer.projects.*',                             'icon' => $ic['projects']],
                    ['label' => 'Messages',  'route' => route('freelancer.messages'),           'match' => 'freelancer.messages|chat.*',                        'icon' => $ic['messages']],
                    ['label' => 'Invoices',  'route' => route('freelancer.invoices'),           'match' => 'freelancer.invoices|freelancer.freelancerInvoices.*','icon' => $ic['invoices']],
                    ['label' => 'Portfolio', 'route' => route('freelancer.portfolio.index'),    'match' => 'freelancer.portfolio.*',                            'icon' => $ic['portfolio']],
                    ['label' => 'Checklist', 'route' => route('freelancer.checklist.show'),     'match' => 'freelancer.checklist.*',                            'icon' => $ic['guide']],
                    ['label' => 'Earnings',  'route' => route('freelancer.earnings'),           'match' => 'freelancer.earnings',                               'icon' => $ic['earnings']],
                    ['label' => 'User Guide', 'route' => route('freelancer.guide'),             'match' => 'freelancer.guide',                                  'icon' => $ic['guide']],
                ];
            }

            // ── Grouped nav (admin only) ─────────────────────────────────
            $groups = [];
            if ($role === 'admin') {
                $groups = [
                    [
                        'label' => 'Core',
                        'items' => [
                            ['label' => 'Dashboard', 'route' => route('admin.dashboard'),       'match' => 'admin.dashboard',                  'icon' => $ic['dashboard']],
                            ['label' => 'Projects',  'route' => route('admin.projects.index'),  'match' => 'admin.projects.*',                 'icon' => $ic['projects']],
                            ['label' => 'Messages',  'route' => route('admin.messages'),        'match' => 'admin.messages|chat.*',            'icon' => $ic['messages']],
                            ['label' => 'Live Chat', 'route' => route('admin.liveChat.index'),  'match' => 'admin.liveChat.*',                 'icon' => $ic['messages']],
                            ['label' => 'Invoices',  'route' => route('admin.invoices'),        'match' => 'admin.invoices|admin.invoices.*',  'icon' => $ic['invoices']],
                            ['label' => 'Leads',     'route' => route('admin.leads.index'),     'match' => 'admin.leads.*',                    'icon' => $ic['leads']],
                        ],
                    ],
                    [
                        'label' => 'Website',
                        'items' => [
                            ['label' => 'Pages',       'route' => route('admin.pages.index'),       'match' => 'admin.pages.*',       'icon' => $ic['pages']],
                            ['label' => 'Blog',        'route' => route('admin.posts.index'),       'match' => 'admin.posts.*',       'icon' => $ic['blog']],
                            ['label' => 'Blog Comments','route' => route('admin.posts.comments.index'),'match' => 'admin.posts.comments.*','icon' => $ic['messages']],
                            ['label' => 'Portfolio',   'route' => route('admin.portfolio.index'),   'match' => 'admin.portfolio.*',   'icon' => $ic['portfolio']],
                            ['label' => 'Shop',        'route' => route('admin.shop.index'),        'match' => 'admin.shop.*',        'icon' => $ic['bills']],
                            ['label' => 'Shop Requests','route' => route('admin.shop.requests.index'),'match' => 'admin.shop.requests.*', 'icon' => $ic['messages']],
                            ['label' => 'Categories',  'route' => route('admin.categories.index'),  'match' => 'admin.categories.*',  'icon' => $ic['categories']],
                            ['label' => 'FAQ',         'route' => route('admin.faq.index'),         'match' => 'admin.faq.*',         'icon' => $ic['guide']],
                            ['label' => 'Subscribers', 'route' => route('admin.subscribers.index'), 'match' => 'admin.subscribers.*', 'icon' => $ic['subscribers']],
                        ],
                    ],
                    [
                        'label' => 'System',
                        'items' => [
                            ['label' => 'Media Library',    'route' => route('admin.media.index'),              'match' => 'admin.media.*',              'icon' => $ic['media']],
                            ['label' => 'Freelancers 🔥',   'route' => route('admin.freelancers.index'),        'match' => 'admin.freelancers.*',         'icon' => $ic['freelancers']],
                            ['label' => 'Users',            'route' => route('admin.users.index'),              'match' => 'admin.users.*|admin.checklists.*', 'icon' => $ic['subscribers']],
                            ['label' => 'User Guide',       'route' => route('admin.guide'),                    'match' => 'admin.guide',                'icon' => $ic['guide']],
                            ['label' => 'Freelancer Bills', 'route' => route('admin.freelancerInvoices.index'), 'match' => 'admin.freelancerInvoices.*',  'icon' => $ic['bills']],
                            ['label' => 'Settings',         'route' => route('admin.settings'),                 'match' => 'admin.settings|admin.settings.*', 'icon' => $ic['settings']],
                        ],
                    ],
                    [
                        'label' => 'Business',
                        'items' => [
                            ['label' => 'Subscriptions',  'route' => route('admin.subscriptions.index'),          'match' => 'admin.subscriptions.*',           'icon' => 'M9 12.75 11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 0 1-1.043 3.296 3.745 3.745 0 0 1-3.296 1.043A3.745 3.745 0 0 1 12 21c-1.268 0-2.39-.63-3.068-1.593a3.745 3.745 0 0 1-3.296-1.043 3.745 3.745 0 0 1-1.043-3.296A3.745 3.745 0 0 1 3 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 0 1 1.043-3.296 3.745 3.745 0 0 1 3.296-1.043A3.745 3.745 0 0 1 12 3c1.268 0 2.39.63 3.068 1.593a3.745 3.745 0 0 1 3.296 1.043 3.745 3.745 0 0 1 1.043 3.296A3.745 3.745 0 0 1 21 12Z'],
                            ['label' => 'Sub Requests',   'route' => route('admin.subscription-requests.index'), 'match' => 'admin.subscription-requests.*',    'icon' => 'M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2m-6 9 2 2 4-4'],
                            ['label' => 'Plans',          'route' => route('admin.subscription-plans.index'),    'match' => 'admin.subscription-plans.*',      'icon' => 'M3.75 6.75h16.5M3.75 12h16.5M12 17.25h8.25'],
                            ['label' => 'Client Files',   'route' => route('admin.client-files.index'),          'match' => 'admin.client-files.*|admin.clients.*','icon' => 'M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z'],
                        ],
                    ],
                ];
            }
        @endphp

        <div x-data="{
            sidebarOpen: false,
            isDark: document.documentElement.classList.contains('dark'),
            toggleDark() {
                this.isDark = !this.isDark;
                document.documentElement.classList.toggle('dark', this.isDark);
                localStorage.setItem('darkMode', this.isDark.toString());
            }
        }" class="flex h-screen">
            <div x-cloak x-show="sidebarOpen" class="fixed inset-0 z-40 bg-navy-900/45 backdrop-blur-sm lg:hidden" x-transition.opacity @click="sidebarOpen = false"></div>

            <aside class="glass-panel fixed inset-y-0 left-0 z-50 flex w-72 flex-col border-r border-warm-300/40 shadow-panel transition duration-300 dark:border-warm-400/[0.08] lg:translate-x-0" :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'">
                <div class="flex items-center justify-between px-5 pb-4 pt-5">
                    <a href="{{ route($role.'.dashboard') }}" class="flex items-center gap-4 opacity-100 transition duration-200 hover:opacity-75">
                        <x-site-logo
                            icon-wrap-class="flex h-12 w-12 items-center justify-center rounded-2xl bg-navy-800 text-warm-100 shadow-card"
                            icon-class="h-7 w-7"
                            name-class="block font-display text-xl text-brand-ink"
                            logo-class="h-12 w-auto object-contain"
                        >
                            <x-slot name="subtitle">
                                <span class="text-xs uppercase tracking-[0.28em] text-brand-muted">Modern SaaS workspace</span>
                            </x-slot>
                        </x-site-logo>
                    </a>
                    <button type="button" class="rounded-2xl border border-warm-300/50 p-2 text-brand-muted lg:hidden" @click="sidebarOpen = false">
                        <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                            <path d="M6 6l8 8M14 6l-8 8" stroke-linecap="round" />
                        </svg>
                    </button>
                </div>

                <div class="px-3">
                    <div class="rounded-2xl bg-navy-800 px-4 py-3 text-warm-100 shadow-card">
                        <p class="text-xs uppercase tracking-[0.3em] text-accent/70">Signed in as</p>
                        <p class="mt-2 font-display text-xl">{{ $user->role->label() }}</p>
                        <p class="mt-1 text-sm text-warm-400">{{ $user->email }}</p>
                    </div>
                </div>

                <nav class="mt-3 flex-1 overflow-y-auto px-3 pb-1">
                    @if ($role === 'admin')
                        {{-- ── Grouped admin navigation ─────────────────────── --}}
                        @foreach ($groups as $gi => $group)
                            @php
                                $groupHasActive = collect($group['items'])->contains(function ($item) {
                                    return collect(explode('|', $item['match']))
                                        ->contains(fn ($p) => request()->routeIs(trim($p)));
                                });
                            @endphp
                            <div x-data="{ open: {{ $groupHasActive ? 'true' : 'true' }} }"
                                 class="{{ $gi > 0 ? 'mt-3 border-t border-warm-300/30 pt-3 dark:border-white/[0.08]' : '' }}">

                                {{-- Group header / toggle --}}
                                <button type="button" @click="open = !open"
                                        class="flex w-full items-center justify-between rounded-xl px-2 py-1.5 text-left transition duration-150 hover:bg-warm-200/50 dark:hover:bg-warm-400/[0.05]">
                                    <span class="text-[10px] font-bold uppercase tracking-[0.3em] text-brand-muted/60 dark:text-warm-600">{{ $group['label'] }}</span>
                                    <svg class="h-3 w-3 shrink-0 text-brand-muted/50 transition-transform duration-200" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                    </svg>
                                </button>

                                {{-- Group items --}}
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-150"
                                     x-transition:enter-start="opacity-0 -translate-y-1"
                                     x-transition:enter-end="opacity-100 translate-y-0"
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100 translate-y-0"
                                     x-transition:leave-end="opacity-0 -translate-y-1"
                                     class="mt-0.5 space-y-0.5">
                                    @foreach ($group['items'] as $item)
                                        @php
                                            $isActive = collect(explode('|', $item['match']))
                                                ->contains(fn ($p) => request()->routeIs(trim($p)));
                                        @endphp
                                        <a href="{{ $item['route'] }}"
                                                         title="{{ $tabHelp[$item['label']] ?? ('Open ' . $item['label']) }}"
                                           class="group flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold transition duration-150
                                                  {{ $isActive
                                                      ? 'bg-navy-800 text-warm-100 shadow-card dark:bg-accent-light dark:text-brand-primary'
                                                      : 'text-brand-muted hover:bg-warm-200/50 hover:text-brand-ink dark:hover:bg-warm-400/[0.05] dark:hover:text-warm-100' }}">
                                            <svg class="h-4 w-4 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                            </svg>
                                            <span class="min-w-0 truncate">{{ $item['label'] }}</span>
                                            @if ($isActive)
                                                <span class="ml-auto h-1.5 w-1.5 shrink-0 rounded-full bg-accent dark:bg-brand-primary"></span>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    @else
                        {{-- ── Flat navigation (client / freelancer) ────────── --}}
                        <div class="mt-1 space-y-0.5">
                            @foreach ($items as $item)
                                @php
                                    $isActive = collect(explode('|', $item['match']))
                                        ->contains(fn ($p) => request()->routeIs(trim($p)));
                                @endphp
                                <a href="{{ $item['route'] }}"
                                              title="{{ $tabHelp[$item['label']] ?? ('Open ' . $item['label']) }}"
                                   class="group flex items-center gap-2.5 rounded-xl px-3 py-2 text-sm font-semibold transition duration-150
                                          {{ $isActive
                                              ? 'bg-navy-800 text-warm-100 shadow-card dark:bg-accent-light dark:text-brand-primary'
                                              : 'text-brand-muted hover:bg-warm-200/50 hover:text-brand-ink dark:hover:bg-warm-400/[0.05] dark:hover:text-warm-100' }}">
                                    <svg class="h-4 w-4 shrink-0 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $item['icon'] }}" />
                                    </svg>
                                    <span class="min-w-0 truncate">{{ $item['label'] }}</span>
                                    @if ($isActive)
                                        <span class="ml-auto h-1.5 w-1.5 shrink-0 rounded-full bg-accent dark:bg-brand-primary"></span>
                                    @endif
                                </a>
                            @endforeach
                        </div>
                    @endif
                </nav>

                <div class="px-3 pb-5 pt-2 space-y-2">
                    <a href="{{ route('profile.edit') }}" class="btn-secondary w-full">Profile settings</a>
                    <a href="{{ url('/') }}"
                       class="flex items-center justify-center gap-2 rounded-2xl border border-warm-400/30 bg-transparent px-4 py-2.5 text-sm font-semibold text-brand-muted transition duration-200 hover:border-accent hover:bg-accent-light hover:text-accent-hover dark:border-warm-400/[0.10] dark:hover:border-accent dark:hover:bg-accent-light dark:hover:text-brand-primary">
                        <svg class="h-4 w-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253M3.284 14.253A8.959 8.959 0 0 1 3 12c0-1.016.168-1.993.457-2.918" />
                        </svg>
                        Visit Website
                    </a>
                </div>
            </aside>

            <div class="flex flex-1 flex-col overflow-hidden lg:pl-72">
                <header class="shrink-0 z-30 px-4 py-2 sm:px-6 lg:px-8">
                    <div class="glass-panel flex items-center justify-between rounded-3xl px-5 py-4 shadow-card">
                        <div class="flex items-center gap-3">
                            <button type="button" class="rounded-2xl border border-warm-300/50 bg-warm-100 p-3 text-brand-muted lg:hidden" @click="sidebarOpen = true">
                                <svg class="h-5 w-5" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
                                    <path d="M3 5h14M3 10h14M3 15h14" stroke-linecap="round" />
                                </svg>
                            </button>
                            <div>
                                <p class="text-xs uppercase tracking-[0.28em] text-brand-muted">Workspace</p>
                                <p class="font-display text-xl text-brand-ink">{{ $user->name }}</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <div class="hidden text-right sm:block">
                                <p class="text-sm font-semibold text-brand-ink">{{ $user->email }}</p>
                                <p class="text-xs uppercase tracking-[0.28em] text-brand-muted">{{ $user->role->label() }}</p>
                            </div>
                            @if ($guideUrl)
                                <a href="{{ $guideUrl }}"
                                   class="relative inline-flex h-10 items-center gap-1.5 rounded-2xl border border-warm-400/30 bg-warm-100 px-3 text-brand-muted transition hover:border-accent hover:bg-accent-light hover:text-accent-hover dark:border-warm-400/[0.12] dark:bg-navy-800 dark:text-warm-600 dark:hover:border-accent dark:hover:bg-accent-light dark:hover:text-brand-primary"
                                   title="Open User Guide"
                                   aria-label="Open User Guide">
                                    <span class="font-display text-base font-bold">?</span>
                                    <span class="hidden text-[11px] font-semibold uppercase tracking-[0.18em] lg:inline">User Guide</span>
                                </a>
                            @endif
                            {{-- Dark mode toggle --}}
                            <button type="button" @click="toggleDark()"
                                    class="relative flex h-10 w-10 items-center justify-center rounded-2xl border border-warm-400/30 bg-warm-100 text-brand-muted transition hover:border-accent hover:bg-accent-light hover:text-accent-hover dark:border-warm-400/[0.12] dark:bg-navy-800 dark:text-warm-600 dark:hover:border-accent dark:hover:bg-accent-light dark:hover:text-brand-primary"
                                    :aria-label="isDark ? 'Switch to light mode' : 'Switch to dark mode'">
                                <svg x-show="isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364-.707-.707M6.343 6.343l-.707-.707m12.728 0-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                                </svg>
                                <svg x-show="!isDark" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                                </svg>
                            </button>
                            <div x-data="{ open: false }" class="relative">
                                <button type="button" class="relative rounded-2xl border border-warm-400/30 bg-warm-100 p-3 text-brand-muted transition hover:border-accent hover:text-brand-primary" @click="open = !open" @click.outside="open = false">
                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 1-2.857.168 23.847 23.847 0 0 1-2.857-.168m5.714 0a8.967 8.967 0 0 0 1.455-.455A2.25 2.25 0 0 0 18 14.345V11.25a6 6 0 1 0-12 0v3.095c0 .928.568 1.76 1.688 2.182.466.175.953.328 1.455.455m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" /></svg>
                                    @if ($unreadNotificationsCount > 0)
                                        <span class="absolute -right-1 -top-1 inline-flex h-5 min-w-[1.25rem] items-center justify-center rounded-full bg-brand-primary px-1 text-[10px] font-bold text-white">{{ $unreadNotificationsCount }}</span>
                                    @endif
                                </button>

                                <div x-show="open" x-cloak x-transition class="absolute right-0 z-50 mt-3 w-80 overflow-hidden rounded-3xl border border-warm-300/50 bg-warm-100 shadow-panel dark:border-warm-400/[0.10] dark:bg-navy-800">
                                    <div class="flex items-center justify-between border-b border-warm-300/40 px-5 py-4">
                                        <div>
                                            <p class="text-sm font-semibold text-brand-ink">Notifications</p>
                                            <p class="text-xs text-brand-muted">Latest account activity</p>
                                        </div>
                                        @if ($unreadNotificationsCount > 0)
                                            <form method="POST" action="{{ route('notifications.readAll') }}">
                                                @csrf
                                                <button type="submit" class="text-xs font-semibold text-brand-primary">Mark all read</button>
                                            </form>
                                        @endif
                                    </div>

                                    <div class="max-h-96 overflow-y-auto">
                                        @forelse ($recentNotifications as $notification)
                                            <form method="POST" action="{{ route('notifications.read', $notification->id) }}" class="border-b border-warm-300/40 last:border-b-0">
                                                @csrf
                                                <input type="hidden" name="redirect" value="{{ $notification->data['action_url'] ?? route('dashboard') }}">
                                                <button type="submit" class="block w-full px-5 py-4 text-left transition hover:bg-warm-200/50 {{ $notification->read_at ? 'bg-warm-100' : 'bg-accent/10' }}">
                                                    <p class="text-sm font-semibold text-brand-ink">{{ $notification->data['title'] ?? 'Notification' }}</p>
                                                    <p class="mt-1 text-xs leading-5 text-brand-muted">{{ $notification->data['message'] ?? 'New activity is available.' }}</p>
                                                    @if (!empty($notification->data['note']))
                                                        <p class="mt-2 rounded-2xl border border-warm-300/50 bg-warm-200/50 px-3 py-2 text-xs leading-5 text-brand-muted">{{ $notification->data['note'] }}</p>
                                                    @endif
                                                    <p class="mt-2 text-[11px] uppercase tracking-[0.2em] text-brand-muted">{{ $notification->created_at->diffForHumans() }}</p>
                                                </button>
                                            </form>
                                        @empty
                                            <div class="px-5 py-8 text-center">
                                                <p class="text-sm font-semibold text-brand-ink">No notifications yet</p>
                                                <p class="mt-1 text-xs text-brand-muted">Approval updates and other events will appear here.</p>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            @if ($user->profileImageUrl())
                                <img src="{{ $user->profileImageUrl() }}" alt="{{ $user->name }}" class="h-12 w-12 rounded-2xl object-cover shadow-sm">
                            @else
                                <div class="flex h-12 w-12 items-center justify-center rounded-2xl bg-accent-light font-display text-sm font-semibold text-brand-primary">
                                    {{ $initials }}
                                </div>
                            @endif
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="btn-primary">Logout</button>
                            </form>
                        </div>
                    </div>
                </header>

                <main class="flex-1 overflow-y-auto px-4 pb-8 pt-4 sm:px-6 lg:px-8">
                    <div class="page-fade space-y-6">
                        @if (isset($header))
                            <section class="rounded-3xl border border-warm-300/50 bg-warm-100/90 px-6 py-6 shadow-panel dark:border-warm-400/[0.08] dark:bg-navy-800">
                                {{ $header }}
                            </section>
                        @endif

                        {{ $slot }}
                    </div>
                </main>
            </div>
        </div>
        @if (auth()->check() && !auth()->user()->must_change_password && (!auth()->user()->onboarding_completed || session('show_onboarding')))
            <div x-data="{ open: true }" x-cloak x-show="open" class="fixed inset-0 z-[70] flex items-center justify-center bg-navy-900/70 p-4 backdrop-blur-sm">
                <div class="w-full max-w-2xl rounded-3xl border border-white/70 bg-warm-100 p-8 shadow-panel">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.28em] text-brand-primary">Quick Guide</p>
                            <h2 class="mt-2 font-display text-2xl text-brand-ink">Welcome to TownCore</h2>
                            <p class="mt-2 text-sm leading-7 text-brand-muted">Here is a quick overview of the workspace so you can get moving immediately.</p>
                        </div>
                    </div>
                    <div class="mt-6 grid gap-4 sm:grid-cols-2">
                        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4"><p class="font-semibold text-brand-ink">Dashboard</p><p class="mt-1 text-sm text-brand-muted">See your activity, billing, subscription state, and current workspace summary.</p></div>
                        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4"><p class="font-semibold text-brand-ink">Projects</p><p class="mt-1 text-sm text-brand-muted">Track delivery progress, milestones, and the latest project updates.</p></div>
                        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4"><p class="font-semibold text-brand-ink">Messages</p><p class="mt-1 text-sm text-brand-muted">Collaborate with your team and keep conversations attached to the work.</p></div>
                        <div class="rounded-2xl border border-warm-300/50 bg-warm-200/50 p-4"><p class="font-semibold text-brand-ink">Files</p><p class="mt-1 text-sm text-brand-muted">Review and manage private files shared for your projects.</p></div>
                    </div>
                    <div class="mt-6 flex flex-wrap gap-3">
                        <form method="POST" action="{{ route('onboarding.complete') }}">
                            @csrf
                            <button type="submit" class="btn-primary">Finish Tour</button>
                        </form>
                        <form method="POST" action="{{ route('onboarding.complete') }}">
                            @csrf
                            <button type="submit" class="btn-secondary">Skip for now</button>
                        </form>
                    </div>
                </div>
            </div>
        @endif
        @stack('scripts')
    </body>
</html>
