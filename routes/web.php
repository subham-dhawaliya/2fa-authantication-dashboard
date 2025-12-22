<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Broadcast;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\TwoFactorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\GoogleAuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\CallController;

Route::get('/', function () {
    return view('welcome');
});

// Broadcasting auth routes
Broadcast::routes(['middleware' => ['web', 'auth']]);

// Google OAuth Routes
Route::get('/auth/google', [GoogleAuthController::class, 'redirect'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleAuthController::class, 'callback'])->name('google.callback');

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Email verification link (no auth required - user clicks from email)
Route::get('/2fa/verify/{token}', [TwoFactorController::class, 'verifyViaLink'])->name('2fa.verify.link');

// 2FA Routes (authenticated but not 2FA verified)
Route::middleware('auth')->group(function () {
    Route::get('/2fa/verify', [TwoFactorController::class, 'show'])->name('2fa.verify');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify']);
    Route::post('/2fa/resend', [TwoFactorController::class, 'resend'])->name('2fa.resend');
    Route::match(['get', 'post'], '/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Routes (authenticated + 2FA verified + admin role)
Route::middleware(['auth', '2fa', 'admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'create'])->name('admin.users.create');
    Route::post('/users', [AdminController::class, 'store'])->name('admin.users.store');
    Route::get('/users/{user}/edit', [AdminController::class, 'edit'])->name('admin.users.edit');
    Route::put('/users/{user}', [AdminController::class, 'update'])->name('admin.users.update');
    Route::delete('/users/{user}', [AdminController::class, 'destroy'])->name('admin.users.destroy');
    
    // Password change OTP verification
    Route::get('/users/{user}/verify-otp', [AdminController::class, 'showVerifyOtp'])->name('admin.users.verify-otp');
    Route::post('/users/{user}/verify-otp', [AdminController::class, 'verifyOtp']);
    Route::post('/users/{user}/resend-otp', [AdminController::class, 'resendOtp'])->name('admin.users.resend-otp');
});

// User Routes (authenticated + 2FA verified)
Route::middleware(['auth', '2fa'])->prefix('user')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/profile', [UserController::class, 'editProfile'])->name('user.profile');
    Route::post('/profile', [UserController::class, 'updateProfile'])->name('user.profile.update');
    Route::get('/change-password', [UserController::class, 'changePassword'])->name('user.password');
    Route::post('/change-password', [UserController::class, 'updatePassword'])->name('user.password.update');
});


// Chat Routes (authenticated + 2FA verified)
Route::middleware(['auth', '2fa'])->prefix('chat')->group(function () {
    Route::get('/', [ChatController::class, 'index'])->name('chat.index');
    Route::get('/messages/{user}', [ChatController::class, 'getMessages'])->name('chat.messages');
    Route::post('/send', [ChatController::class, 'sendMessage'])->name('chat.send');
    Route::post('/mark-read', [ChatController::class, 'markAsRead'])->name('chat.mark-read');
    Route::get('/unread', [ChatController::class, 'unreadCount'])->name('chat.unread');
});

// Call Routes
Route::middleware(['auth', '2fa'])->prefix('call')->group(function () {
    Route::post('/signal', [CallController::class, 'signal'])->name('call.signal');
});
