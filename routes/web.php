<?php

use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\PageSectionController as AdminPageSectionController;
use App\Http\Controllers\Admin\FreelancerController as AdminFreelancerController;
use App\Http\Controllers\Admin\FreelancerInvoiceController as AdminFreelancerInvoiceController;
use App\Http\Controllers\Admin\PortfolioController as AdminPortfolioController;
use App\Http\Controllers\Admin\ProjectController as AdminProjectController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\Client\ProjectController as ClientProjectController;
use App\Http\Controllers\ConversationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Freelancer\PortfolioController as FreelancerPortfolioController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ServicesController;
use App\Http\Controllers\PublicPortfolioController;
use App\Http\Controllers\Freelancer\FreelancerInvoiceController;
use App\Http\Controllers\Freelancer\ProjectController as FreelancerProjectController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\FreelancerPaymentController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\Admin\LeadController as AdminLeadController;
use App\Http\Controllers\ExperienceController;
use App\Http\Controllers\NewsletterController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\SettingController;
use App\Models\FreelancerInvoice;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

// Public service pages
Route::get('/services', [ServicesController::class, 'index'])->name('services.index');
Route::get('/services/{category:slug}', [ServicesController::class, 'show'])->name('services.show');

// Blog
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post:slug}', [BlogController::class, 'show'])->name('blog.show');

// Experiences page
Route::get('/experiences', [ExperienceController::class, 'index'])->name('experiences.index');

// About page
Route::get('/about', [PageController::class, 'about'])->name('about');

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

// Public CMS pages
Route::get('/page/{page:slug}', [PageController::class, 'show'])->name('pages.show');

Route::get('/dashboard', [DashboardController::class, 'redirect'])
    ->middleware('auth')
    ->name('dashboard');

foreach (['admin', 'client', 'freelancer'] as $role) {
    Route::prefix($role)
        ->middleware(['auth', 'role:'.$role])
        ->as($role.'.')
        ->group(function () {
            Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
            Route::get('/messages', [ConversationController::class, 'index'])->name('messages');
        });
}

foreach (['admin', 'client'] as $role) {
    Route::prefix($role)
        ->middleware(['auth', 'role:'.$role])
        ->as($role.'.')
        ->group(function () {
            Route::get('/invoices', [InvoiceController::class, 'index'])->name('invoices');
        });
}

// Client project routes
Route::prefix('client')
    ->middleware(['auth', 'role:client'])
    ->as('client.')
    ->group(function () {
        Route::get('/projects', [ClientProjectController::class, 'index'])->name('projects.index');
        Route::get('/projects/create', [ClientProjectController::class, 'create'])->name('projects.create');
        Route::post('/projects', [ClientProjectController::class, 'store'])->name('projects.store');
        Route::get('/projects/{project}', [ClientProjectController::class, 'show'])->name('projects.show');
    });

// Admin project routes
Route::prefix('admin')
    ->middleware(['auth', 'role:admin'])
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
        Route::patch('/portfolio/{portfolio}/approve', [AdminPortfolioController::class, 'approve'])->name('portfolio.approve');
        Route::patch('/portfolio/{portfolio}/reject', [AdminPortfolioController::class, 'reject'])->name('portfolio.reject');

        Route::get('/settings', [SettingController::class, 'edit'])->name('settings');
        Route::post('/settings', [SettingController::class, 'update'])->name('settings.update');

        Route::get('/categories', [CategoryController::class, 'index'])->name('categories.index');
        Route::post('/categories', [CategoryController::class, 'store'])->name('categories.store');
        Route::patch('/categories/{category}', [CategoryController::class, 'update'])->name('categories.update');
        Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])->name('categories.destroy');

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

        Route::get('/subscribers', [\App\Http\Controllers\Admin\SubscriberController::class, 'index'])->name('subscribers.index');
        Route::get('/subscribers/export', [\App\Http\Controllers\Admin\SubscriberController::class, 'export'])->name('subscribers.export');
        Route::delete('/subscribers/{subscriber}', [\App\Http\Controllers\Admin\SubscriberController::class, 'destroy'])->name('subscribers.destroy');

        Route::get('/media', [\App\Http\Controllers\Admin\MediaController::class, 'index'])->name('media.index');
        Route::get('/media/api', [\App\Http\Controllers\Admin\MediaController::class, 'api'])->name('media.api');
        Route::post('/media', [\App\Http\Controllers\Admin\MediaController::class, 'store'])->name('media.store');
        Route::delete('/media/{medium}', [\App\Http\Controllers\Admin\MediaController::class, 'destroy'])->name('media.destroy');

        Route::post('/projects/{project}/freelancer-payment', [FreelancerPaymentController::class, 'setAgreed'])->name('projects.freelancerPayment.set');
        Route::post('/freelancer-payments/{freelancerPayment}/log', [FreelancerPaymentController::class, 'addPayment'])->name('freelancerPayments.addPayment');

        Route::get('/freelancer-invoices', [AdminFreelancerInvoiceController::class, 'index'])->name('freelancerInvoices.index');
        Route::get('/freelancer-invoices/{freelancerInvoice}', [AdminFreelancerInvoiceController::class, 'show'])->name('freelancerInvoices.show');
        Route::patch('/freelancer-invoices/{freelancerInvoice}/approve', [AdminFreelancerInvoiceController::class, 'approve'])->name('freelancerInvoices.approve');
        Route::patch('/freelancer-invoices/{freelancerInvoice}/reject', [AdminFreelancerInvoiceController::class, 'reject'])->name('freelancerInvoices.reject');

        Route::get('/freelancers', [AdminFreelancerController::class, 'index'])->name('freelancers.index');

        Route::get('/leads', [AdminLeadController::class, 'index'])->name('leads.index');
        Route::get('/leads/{lead}', [AdminLeadController::class, 'show'])->name('leads.show');
        Route::patch('/leads/{lead}/status', [AdminLeadController::class, 'updateStatus'])->name('leads.status');
        Route::post('/leads/{lead}/convert', [AdminLeadController::class, 'convert'])->name('leads.convert');
    });

// Freelancer project routes
Route::prefix('freelancer')
    ->middleware(['auth', 'role:freelancer'])
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
    });

Route::middleware('auth')->group(function () {
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

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

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

        abort_unless(Storage::disk('local')->exists($freelancerInvoice->file_path), 404);

        return response()->download(
            storage_path('app/' . $freelancerInvoice->file_path),
            'freelancer-invoice-' . $freelancerInvoice->id . '.pdf'
        );
    })->name('freelancerInvoices.download');

    Route::get('/freelancer-invoices/{freelancerInvoice}/file', function (Request $request, FreelancerInvoice $freelancerInvoice) {
        $user = $request->user();

        abort_unless(
            $user->role->value === 'admin' || $freelancerInvoice->freelancer_id === $user->id,
            403
        );

        $path = storage_path('app/' . $freelancerInvoice->file_path);

        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="freelancer-invoice-' . $freelancerInvoice->id . '.pdf"',
        ]);
    })->name('freelancerInvoices.file');

    Route::get('/projects/{project}/chat', [ChatController::class, 'show'])->name('chat.show');
    Route::get('/projects/{project}/chat/messages', [ChatController::class, 'fetchMessages'])->name('chat.messages');
    Route::post('/projects/{project}/chat/messages', [ChatController::class, 'store'])->name('chat.store');

    // Global conversation API (all authenticated users)
    Route::get('/conversations', [ConversationController::class, 'list'])->name('conversations.list');
    Route::post('/conversations', [ConversationController::class, 'store'])->name('conversations.store');
    Route::get('/conversations/{conversation}/messages', [ConversationController::class, 'fetchMessages'])->name('conversations.messages');
    Route::post('/conversations/{conversation}/messages', [ConversationController::class, 'sendMessage'])->name('conversations.send');
});

require __DIR__.'/auth.php';
