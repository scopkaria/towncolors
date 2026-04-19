<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PageSectionController as AdminPageSectionController;
use App\Http\Controllers\Admin\FreelancerController as AdminFreelancerController;
use App\Http\Controllers\Admin\FreelancerInvoiceController as AdminFreelancerInvoiceController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\ShopController as AdminShopController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\Admin\SubscriptionPlanController as AdminSubscriptionPlanController;
use App\Http\Controllers\Admin\SubscriptionController as AdminSubscriptionController;
use App\Http\Controllers\Admin\ClientFileController as AdminClientFileController;
use App\Http\Controllers\Admin\SubscriptionRequestController as AdminSubscriptionRequestController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\Client\SubscriptionController as ClientSubscriptionController;
use App\Http\Controllers\Client\ClientFileController;
use App\Http\Controllers\Client\TrialController as ClientTrialController;
use App\Http\Controllers\Client\SubscriptionRequestController as ClientSubscriptionRequestController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Freelancer\PortfolioController as FreelancerPortfolioController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\PublicPortfolioController;
use App\Http\Controllers\ShopController;
use App\Http\Controllers\FaqController;
use App\Http\Controllers\BlogCommentController;
use App\Http\Controllers\Freelancer\FreelancerInvoiceController;
use App\Http\Controllers\Freelancer\ChecklistController as FreelancerChecklistController;
use App\Http\Controllers\Freelancer\ProjectController as FreelancerProjectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\FreelancerPaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\LiveChatController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserGuideController;
use App\Http\Controllers\Admin\ChecklistController as AdminChecklistController;
use App\Http\Controllers\Admin\UserController as AdminUserController;
use App\Http\Controllers\Admin\BlogCommentController as AdminBlogCommentController;
use App\Http\Controllers\Client\ChecklistController as ClientChecklistController;
use App\Models\FreelancerInvoice;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\FaqController as AdminFaqController;
use App\Http\Controllers\Admin\SoftwarePurchaseRequestController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', HomeController::class);

// Public portfolio page (no auth required)
Route::get('/portfolio', [PublicPortfolioController::class, 'index'])->name('portfolio.public');
Route::get('/shop', [ShopController::class, 'index'])->name('shop.index');
Route::get('/shop/product/{portfolio}', [ShopController::class, 'showProduct'])->name('shop.show');
Route::get('/shop/{portfolio}/checkout', [ShopController::class, 'checkout'])->name('shop.checkout');
Route::post('/shop/{portfolio}/checkout', [ShopController::class, 'storeCheckout'])->middleware('auth')->name('shop.checkout.store');

// Public service pages
Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
Route::get('/services/{category:slug}', [ServicesController::class, 'show'])->name('services.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');
Route::post('/blog/{post:slug}/comments', [BlogCommentController::class, 'store'])
    ->middleware('throttle:8,1')
    ->name('blog.comments.store');

// Experiences page
Route::get('/experiences', [ExperienceController::class, 'index'])->name('experiences.index');

// About page
Route::view('/about', 'about')->name('about');

// Premium company pages
Route::view('/cloud-services', 'site.cloud')->name('cloud.index');
Route::view('/products', 'site.products')->name('products.index');
Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');

// GET /logout — handles direct browser navigation / stale bookmarks
Route::get('/logout', function () {
    if (auth()->check()) {
        auth()->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }
    return redirect()->route('login');
})->middleware('web')->name('logout.get');

// Contact page
Route::get('/contact', [ContactController::class, 'show'])->name('contact.show');
Route::post('/contact', [ContactController::class, 'store'])->name('contact.store');

// Newsletter
Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])->name('newsletter.subscribe');

// Lead capture
Route::post('/leads', [LeadController::class, 'store'])->name('leads.store');

// ── Public Live Chat API (no auth — visitor endpoints) ──────────────
Route::prefix('live-chat')->as('live-chat.')->group(function () {
    Route::post('/start',    [LiveChatController::class, 'startSession'])->name('start');
    Route::post('/send',     [LiveChatController::class, 'visitorSend'])->name('send');
    Route::get('/messages',  [LiveChatController::class, 'visitorMessages'])->name('messages');
});

// Public CMS pages
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware('auth')
    ->name('dashboard');

foreach (['admin', 'client', 'freelancer'] as $role) {
    Route::prefix($role)
        ->middleware(['auth', 'password.change.required', 'role:'.$role])
        ->as($role.'.')
        ->group(function () use ($role) {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/guide', [UserGuideController::class, 'show'])->name('guide');
            Route::get('/messages', [ConversationController::class, 'index'])
                ->middleware($role === 'client' ? 'subscribed.client' : [])
                ->name('messages');
        });
}

foreach (['admin', 'client'] as $role) {
    Route::prefix($role)
        ->middleware(['auth', 'password.change.required', 'role:'.$role])
        ->as($role.'.')
        ->group(function () use ($role) {
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
        });
}

// Client project routes
Route::prefix('client')
    ->middleware(['auth', 'password.change.required', 'role:client'])
    ->as('client.')
    ->group(function () {
        Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ClientProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ClientProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');

        // Subscription
        Route::get('/subscription', [ClientSubscriptionController::class, 'show'])->name('subscription.show');
        Route::get('/subscription/status', [ClientSubscriptionController::class, 'status'])->name('subscription.status');
        Route::post('/subscription-requests', [ClientSubscriptionRequestController::class, 'store'])->name('subscription-requests.store');
        Route::post('/trial/start', [ClientTrialController::class, 'start'])->name('trial.start');

        // Checklist
        Route::get('/checklist', [ClientChecklistController::class, 'show'])->name('checklist.show');
        Route::get('/checklist/snapshot', [ClientChecklistController::class, 'snapshot'])->name('checklist.snapshot');

        // Private files
        Route::middleware('subscribed.client')->group(function () {
            Route::get('/files', [ClientFileController::class, 'index'])->name('files.index');
            Route::post('/files', [ClientFileController::class, 'store'])->name('files.store');
            Route::get('/files/folders/{clientFolder}', [ClientFileController::class, 'folder'])->name('files.folder');
            Route::get('/files/{clientFile}/preview', [ClientFileController::class, 'preview'])->name('files.preview');
            Route::get('/files/{clientFile}/download', [ClientFileController::class, 'download'])->name('files.download');
            Route::delete('/files/{clientFile}', [ClientFileController::class, 'destroy'])->name('files.destroy');

            // Folder management
            Route::post('/folders', [ClientFileController::class, 'storeFolder'])->name('folders.store');
            Route::patch('/folders/{clientFolder}', [ClientFileController::class, 'renameFolder'])->name('folders.rename');
            Route::delete('/folders/{clientFolder}', [ClientFileController::class, 'destroyFolder'])->name('folders.destroy');
        });
    });

// Admin project routes
Route::prefix('admin')
    ->middleware(['auth', 'password.change.required', 'role:admin'])
    ->as('admin.')
    ->group(function () {
        Route::get('/projects', [AdminProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [AdminProjectController::class, 'show'])->name('projects.show');
        Route::patch('/projects/{project}/assign', [AdminProjectController::class, 'assign'])->name('projects.assign');
        Route::patch('/projects/{project}/status', [AdminProjectController::class, 'updateStatus'])->name('projects.status');

        Route::get('/invoices/create', [InvoiceController::class, 'create'])->name('invoices.create');
        Route::post('/invoices', [InvoiceController::class, 'store'])->name('invoices.store');
        Route::get('/invoices/{invoice}/edit', [InvoiceController::class, 'edit'])->name('invoices.edit');
        Route::patch('/invoices/{invoice}', [InvoiceController::class, 'update'])->name('invoices.update');
        Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.addPayment');

        Route::get('/portfolio', [AdminPortfolioController::class, 'index'])->name('portfolio.index');
        Route::get('/portfolio/{portfolio}/edit', [AdminPortfolioController::class, 'edit'])->name('portfolio.edit');
        Route::patch('/portfolio/{portfolio}', [AdminPortfolioController::class, 'update'])->name('portfolio.update');
        Route::patch('/portfolio/{portfolio}/approve', [AdminPortfolioController::class, 'approve'])->name('portfolio.approve');
        Route::patch('/portfolio/{portfolio}/reject', [AdminPortfolioController::class, 'reject'])->name('portfolio.reject');

        Route::get('/shop', [AdminShopController::class, 'index'])->name('shop.index');
        Route::post('/shop', [AdminShopController::class, 'store'])->name('shop.store');
        Route::get('/shop/requests', [SoftwarePurchaseRequestController::class, 'index'])->name('shop.requests.index');
        Route::patch('/shop/requests/{softwareRequest}', [SoftwarePurchaseRequestController::class, 'update'])->name('shop.requests.update');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

        Route::get('/faq', [AdminFaqController::class, 'index'])->name('faq.index');
        Route::post('/faq', [AdminFaqController::class, 'store'])->name('faq.store');
        Route::patch('/faq/{faq}', [AdminFaqController::class, 'update'])->name('faq.update');
        Route::delete('/faq/{faq}', [AdminFaqController::class, 'destroy'])->name('faq.destroy');

        Route::get('/pages', [AdminPageController::class, 'index'])->name('pages.index');
        Route::get('/pages/create', [AdminPageController::class, 'create'])->name('pages.create');
        Route::post('/pages', [AdminPageController::class, 'store'])->name('pages.store');
        Route::get('/pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
        Route::patch('/pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');
        Route::delete('/pages/{page}', [AdminPageController::class, 'destroy'])->name('pages.destroy');

        // ── Sections Builder ───────────────────────────────────────────────────
        Route::get('/pages/{page}/sections', [AdminPageSectionController::class, 'index'])->name('pages.sections.index');
        Route::get('/pages/{page}/sections/create', [AdminPageSectionController::class, 'create'])->name('pages.sections.create');
        Route::post('/pages/{page}/sections', [AdminPageSectionController::class, 'store'])->name('pages.sections.store');
        Route::get('/pages/{page}/sections/{section}/edit', [AdminPageSectionController::class, 'edit'])->name('pages.sections.edit');
        Route::patch('/pages/{page}/sections/{section}', [AdminPageSectionController::class, 'update'])->name('pages.sections.update');
        Route::delete('/pages/{page}/sections/{section}', [AdminPageSectionController::class, 'destroy'])->name('pages.sections.destroy');
        Route::patch('/pages/{page}/sections/{section}/toggle', [AdminPageSectionController::class, 'toggle'])->name('pages.sections.toggle');
        Route::post('/pages/{page}/sections/reorder', [AdminPageSectionController::class, 'reorder'])->name('pages.sections.reorder');

        Route::get('/posts', [\App\Http\Controllers\Admin\PostController::class, 'index'])->name('posts.index');
        Route::get('/posts/create', [\App\Http\Controllers\Admin\PostController::class, 'create'])->name('posts.create');
        Route::post('/posts', [\App\Http\Controllers\Admin\PostController::class, 'store'])->name('posts.store');
        Route::get('/posts/{post}/edit', [\App\Http\Controllers\Admin\PostController::class, 'edit'])->name('posts.edit');
        Route::patch('/posts/{post}', [\App\Http\Controllers\Admin\PostController::class, 'update'])->name('posts.update');
        Route::delete('/posts/{post}', [\App\Http\Controllers\Admin\PostController::class, 'destroy'])->name('posts.destroy');
        Route::post('/posts/upload-image', [\App\Http\Controllers\Admin\PostController::class, 'uploadImage'])->name('posts.upload-image');
        Route::get('/posts/comments', [AdminBlogCommentController::class, 'index'])->name('posts.comments.index');
        Route::patch('/posts/comments/{comment}', [AdminBlogCommentController::class, 'update'])->name('posts.comments.update');
        Route::delete('/posts/comments/{comment}', [AdminBlogCommentController::class, 'destroy'])->name('posts.comments.destroy');

        Route::get('/subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('/subscribers/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');
        Route::delete('/subscribers/{subscriber}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');

        Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
        Route::get('/media/api', [\App\Http\Controllers\Admin\MediaController::class, 'api'])->name('media.api');
        Route::post('/media/api/upload', [\App\Http\Controllers\Admin\MediaController::class, 'apiUpload'])->name('media.api.upload');
        Route::post('/media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{medium}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

        Route::post('/projects/{project}/freelancer-payment', [FreelancerPaymentController::class, 'setAgreed'])->name('projects.freelancerPayment.set');
        Route::post('/freelancer-payments/{freelancerPayment}/log', [FreelancerPaymentController::class, 'addPayment'])->name('freelancerPayments.addPayment');

        Route::get('/freelancer-invoices', [AdminFreelancerInvoiceController::class, 'index'])->name('freelancerInvoices.index');
        Route::get('/freelancer-invoices/{freelancerInvoice}', [AdminFreelancerInvoiceController::class, 'show'])->name('freelancerInvoices.show');
        Route::patch('/freelancer-invoices/{freelancerInvoice}/approve', [AdminFreelancerInvoiceController::class, 'approve'])->name('freelancerInvoices.approve');
        Route::patch('/freelancer-invoices/{freelancerInvoice}/reject', [AdminFreelancerInvoiceController::class, 'reject'])->name('freelancerInvoices.reject');

        Route::get('/freelancers', [AdminFreelancerController::class, 'index'])->name('freelancers.index');

        Route::get('/users', [AdminUserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [AdminUserController::class, 'create'])->name('users.create');
        Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');

        Route::get('/clients/{user}/checklist', [AdminChecklistController::class, 'show'])->name('checklists.show');
        Route::post('/clients/{user}/checklist', [AdminChecklistController::class, 'store'])->name('checklists.store');
        Route::patch('/clients/{user}/checklist/{item}', [AdminChecklistController::class, 'update'])->name('checklists.update');
        Route::delete('/clients/{user}/checklist/{item}', [AdminChecklistController::class, 'destroy'])->name('checklists.destroy');
        Route::get('/clients/{user}/checklist/snapshot', [AdminChecklistController::class, 'snapshot'])->name('checklists.snapshot');

        Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
        Route::post('/leads/{lead}/convert', [AdminLeadController::class, 'convert'])->name('leads.convert');

        // ── Live Chat (admin / support-agent) ───────────────────────
        Route::get('/live-chat',                          [LiveChatController::class, 'adminIndex'])->name('liveChat.index');
        Route::get('/live-chat/{session}',                [LiveChatController::class, 'adminShow'])->name('liveChat.show');
        Route::post('/live-chat/{session}/join',          [LiveChatController::class, 'agentJoin'])->name('liveChat.join');
        Route::post('/live-chat/{session}/send',          [LiveChatController::class, 'agentSend'])->name('liveChat.send');
        Route::get('/live-chat/{session}/messages',       [LiveChatController::class, 'agentMessages'])->name('liveChat.messages');
        Route::post('/live-chat/{session}/close',         [LiveChatController::class, 'agentClose'])->name('liveChat.close');

        // ── Subscription Plans ────────────────────────────────────────
        Route::get('/subscription-plans',                     [AdminSubscriptionPlanController::class, 'index'])->name('subscription-plans.index');
        Route::get('/subscription-plans/create',              [AdminSubscriptionPlanController::class, 'create'])->name('subscription-plans.create');
        Route::post('/subscription-plans',                    [AdminSubscriptionPlanController::class, 'store'])->name('subscription-plans.store');
        Route::get('/subscription-plans/{subscriptionPlan}/edit',    [AdminSubscriptionPlanController::class, 'edit'])->name('subscription-plans.edit');
        Route::patch('/subscription-plans/{subscriptionPlan}',       [AdminSubscriptionPlanController::class, 'update'])->name('subscription-plans.update');
        Route::delete('/subscription-plans/{subscriptionPlan}',      [AdminSubscriptionPlanController::class, 'destroy'])->name('subscription-plans.destroy');

        // ── Subscriptions ─────────────────────────────────────────────
        Route::get('/subscriptions',                           [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/{subscription}/edit',       [AdminSubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::post('/subscriptions/{subscription}/revoke',    [AdminSubscriptionController::class, 'revoke'])->name('subscriptions.revoke');
        Route::patch('/subscriptions/{subscription}',          [AdminSubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}',         [AdminSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
        Route::get('/users/{user}/subscription/assign',        [AdminSubscriptionController::class, 'assign'])->name('subscriptions.assign');
        Route::post('/users/{user}/subscription',              [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');

        // ── Subscription Requests ─────────────────────────────────────
        Route::get('/subscription-requests',                                    [AdminSubscriptionRequestController::class, 'index'])->name('subscription-requests.index');
        Route::get('/subscription-requests/snapshot',                           [AdminSubscriptionRequestController::class, 'snapshot'])->name('subscription-requests.snapshot');
        Route::post('/subscription-requests/{subscriptionRequest}/approve',     [AdminSubscriptionRequestController::class, 'approve'])->name('subscription-requests.approve');
        Route::post('/subscription-requests/{subscriptionRequest}/reject',      [AdminSubscriptionRequestController::class, 'reject'])->name('subscription-requests.reject');

        // ── Client Files ──────────────────────────────────────────────
        Route::get('/client-files',                                             [AdminClientFileController::class, 'index'])->name('client-files.index');
        Route::get('/client-files/{clientFile}/preview',                        [AdminClientFileController::class, 'preview'])->name('client-files.preview');
        Route::get('/client-files/{clientFile}/download',                       [AdminClientFileController::class, 'download'])->name('client-files.download');
        Route::delete('/client-files/{clientFile}',                             [AdminClientFileController::class, 'destroy'])->name('client-files.destroy');

        // ── Per-client workspaces ─────────────────────────────────────
        Route::get('/users/{user}/files',                                       [AdminClientFileController::class, 'showClient'])->name('clients.files');
        Route::get('/users/{user}/files/folders/{clientFolder}',                [AdminClientFileController::class, 'showClientFolder'])->name('clients.files.folder');
    });

// Freelancer project routes
Route::prefix('freelancer')
    ->middleware(['auth', 'password.change.required', 'role:freelancer'])
    ->as('freelancer.')
    ->group(function () {
        Route::get('/invoices', [FreelancerInvoiceController::class, 'index'])->name('invoices');
        Route::get('/projects', [FreelancerProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/{project}', [FreelancerProjectController::class, 'show'])->name('projects.show');
        Route::patch('/projects/{project}/status', [FreelancerProjectController::class, 'updateStatus'])->name('projects.status');
        Route::post('/projects/{project}/files', [FreelancerProjectController::class, 'uploadFile'])->name('projects.files.store');
        Route::get('/earnings', [FreelancerPaymentController::class, 'earnings'])->name('earnings');
        Route::get('/freelancer-invoices', [FreelancerInvoiceController::class, 'index'])->name('freelancerInvoices.index');
        Route::post('/freelancer-invoices', [FreelancerInvoiceController::class, 'store'])->name('freelancerInvoices.store');

        Route::get('/portfolio', [FreelancerPortfolioController::class, 'index'])->name('portfolio.index');
        Route::post('/portfolio', [FreelancerPortfolioController::class, 'store'])->name('portfolio.store');
        Route::delete('/portfolio/{portfolio}', [FreelancerPortfolioController::class, 'destroy'])->name('portfolio.destroy');
        Route::get('/checklist', [FreelancerChecklistController::class, 'show'])->name('checklist.show');

        // Client private files (read-only for assigned freelancers)
        Route::get('/client-files/{clientFile}/download', [ClientFileController::class, 'download'])->name('client-files.download');
    });

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::middleware('password.change.required')->group(function () {
        Route::get('/projects/{project}', function (Request $request, \App\Models\Project $project) {
        $user = $request->user();

        if ($user->role->value === 'admin') {
            return redirect()->route('admin.projects.show', $project);
        }

        if ($user->role->value === 'client') {
            abort_unless($project->client_id === $user->id, 403);

            return redirect()->route('client.projects.show', $project);
        }

        abort_unless($project->freelancer_id === $user->id, 403);

        return redirect()->route('freelancer.projects.show', $project);
        })->name('projects.redirect');

        Route::middleware('role:admin,client')->group(function () {
            Route::get('/invoices/{invoice}', [InvoiceController::class, 'show'])->name('invoices.show');
            Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'downloadPdf'])->name('invoices.pdf');
        });

        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');
        Route::post('/notifications/{notification}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');

        Route::get('/freelancer-invoices/{freelancerInvoice}/download', function (Request $request, FreelancerInvoice $freelancerInvoice) {
        $user = $request->user();

        abort_unless(
            $user->role->value === 'admin' || $freelancerInvoice->freelancer_id === $user->id,
            403
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.freelancer-invoices.pdf', [
            'invoice' => $freelancerInvoice->loadMissing(['freelancer', 'project']),
        ]);

        $filename = ($freelancerInvoice->invoice_number ?: ('INV-' . str_pad((string) $freelancerInvoice->id, 5, '0', STR_PAD_LEFT))) . '.pdf';

        return $pdf->download($filename);
        })->name('freelancerInvoices.download');

        Route::get('/freelancer-invoices/{freelancerInvoice}/file', function (Request $request, FreelancerInvoice $freelancerInvoice) {
        $user = $request->user();

        abort_unless(
            $user->role->value === 'admin' || $freelancerInvoice->freelancer_id === $user->id,
            403
        );

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.freelancer-invoices.pdf', [
            'invoice' => $freelancerInvoice->loadMissing(['freelancer', 'project']),
        ]);

        return $pdf->stream();
        })->name('freelancerInvoices.file');

        Route::get('/projects/{project}/chat', [ChatController::class, 'show'])->middleware('subscribed.client')->name('chat.show');
        Route::get('/projects/{project}/chat/messages', [ChatController::class, 'fetchMessages'])->middleware('subscribed.client')->name('chat.messages');
        Route::post('/projects/{project}/chat/messages', [ChatController::class, 'store'])->middleware('subscribed.client')->name('chat.store');

        // Global conversation API (all authenticated users)
        Route::get('/conversations', [ConversationController::class, 'list'])->middleware('subscribed.client')->name('conversations.list');
        Route::post('/conversations', [ConversationController::class, 'store'])->middleware('subscribed.client')->name('conversations.store');
        Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'fetchMessages'])->middleware('subscribed.client')->name('conversations.messages');
        Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->middleware('subscribed.client')->name('conversations.send');
    });

    Route::post('/onboarding/complete', [OnboardingController::class, 'complete'])->name('onboarding.complete');
});

require __DIR__.'/auth.php';
