<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\ProjectController;
use App\Http\Controllers\Api\InvoiceController;
use App\Http\Controllers\Api\ChatController;
use App\Http\Controllers\Api\PortfolioController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\LiveChatController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public routes
Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

// Public Live Chat (visitor endpoints — no auth required)
Route::prefix('live-chat')->group(function () {
    Route::post('/start',    [LiveChatController::class, 'startSession']);
    Route::post('/send',     [LiveChatController::class, 'visitorSend']);
    Route::get('/messages',  [LiveChatController::class, 'visitorMessages']);
});

// Public content
Route::get('/blog', [BlogController::class, 'index']);
Route::get('/blog/{post:slug}', [BlogController::class, 'show']);
Route::get('/portfolio', [PortfolioController::class, 'index']);
Route::get('/categories', [ProjectController::class, 'categories']);

// Authenticated routes
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    Route::put('/user/profile', [AuthController::class, 'updateProfile']);
    Route::put('/user/password', [AuthController::class, 'updatePassword']);
    Route::post('/user/push-token', [AuthController::class, 'storePushToken']);

    // Contacts (role-scoped)
    Route::get('/contacts', [ChatController::class, 'contacts']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);

    // Projects
    Route::get('/projects', [ProjectController::class, 'index']);
    Route::get('/projects/{project}', [ProjectController::class, 'show']);
    Route::post('/projects', [ProjectController::class, 'store']);
    Route::post('/projects/{project}/assign', [ProjectController::class, 'assign']);
    Route::put('/projects/{project}/status', [ProjectController::class, 'updateStatus']);
    Route::post('/projects/{project}/files', [ProjectController::class, 'uploadFile']);

    // Invoices
    Route::get('/invoices', [InvoiceController::class, 'index']);
    Route::get('/invoices/{invoice}', [InvoiceController::class, 'show']);
    Route::get('/freelancer-invoices', [InvoiceController::class, 'freelancerInvoices']);
    Route::post('/freelancer-invoices', [InvoiceController::class, 'storeFreelancerInvoice']);

    // Chat / Messaging
    Route::get('/conversations', [ChatController::class, 'conversations']);
    Route::get('/conversations/{conversation}/messages', [ChatController::class, 'messages']);
    Route::post('/conversations/{conversation}/messages', [ChatController::class, 'sendMessage']);
    Route::post('/conversations', [ChatController::class, 'createConversation']);

    // Portfolio (freelancer)
    Route::get('/my-portfolio', [PortfolioController::class, 'myPortfolio']);
    Route::post('/portfolio', [PortfolioController::class, 'store']);
    Route::delete('/portfolio/{portfolio}', [PortfolioController::class, 'destroy']);

    // Notifications
    Route::get('/notifications', [NotificationController::class, 'index']);
    Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
});
