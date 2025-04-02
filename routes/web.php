<?php

use App\Http\Controllers\BookingController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProviderController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\HomeController;
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

// Public routes
Route::get('/', function () {
    return view('welcome');
});

Route::get('/services', [ServiceController::class, 'publicIndex'])->name('services.public');
Route::get('/services/{service}', [ServiceController::class, 'publicShow'])->name('services.public.show');

Route::get('/providers', [ProviderController::class, 'publicIndex'])->name('providers.public');
Route::get('/providers/{provider}', [ProviderController::class, 'publicShow'])->name('providers.public.show');

// Authentication routes
Route::middleware('auth')->group(function () {
    // Dashboard
    Route::get('/dashboard', [HomeController::class, 'dashboard'])->name('dashboard');

    // Profile routes
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customer routes
    Route::middleware(['auth', 'role:customer,provider,admin,super_admin'])->group(function () {
        // Bookings
        Route::get('/my-bookings', [BookingController::class, 'userBookings'])->name('bookings.user');
        Route::get('/book-service/{provider}', [BookingController::class, 'bookService'])->name('bookings.service');
        Route::post('/book-service/{provider}', [BookingController::class, 'storeBooking'])->name('bookings.store-service');

        // Reviews
        Route::get('/add-review/{booking}', [ReviewController::class, 'createFromBooking'])->name('reviews.create-booking');
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::get('/my-reviews', [ReviewController::class, 'userReviews'])->name('reviews.user');
        Route::get('/reviews/{review}/edit', [ReviewController::class, 'edit'])->name('reviews.edit');
        Route::put('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
    });

    // Provider routes
    Route::middleware(['auth', 'role:provider,admin,super_admin'])->group(function () {
        Route::get('/my-provider-profile', [ProviderController::class, 'myProfile'])->name('providers.my-profile');
        Route::get('/my-provider-profile/edit', [ProviderController::class, 'editMyProfile'])->name('providers.edit-my-profile');
        Route::put('/my-provider-profile', [ProviderController::class, 'updateMyProfile'])->name('providers.update-my-profile');

        Route::get('/my-bookings-requests', [BookingController::class, 'providerBookings'])->name('bookings.provider');
        Route::put('/bookings/{booking}/status', [BookingController::class, 'updateStatus'])->name('bookings.update-status');
    });

    // Admin routes
    Route::middleware(['auth', 'role:admin,super_admin'])->group(function () {
        // Users
        Route::resource('users', UserController::class);

        // Services
        Route::resource('services', ServiceController::class);

        // Providers
        Route::resource('providers', ProviderController::class);

        // Bookings
        Route::resource('bookings', BookingController::class);

        // Reviews
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::get('/reviews/{review}', [ReviewController::class, 'show'])->name('reviews.show');
    });

    // Super Admin routes
    Route::middleware(['auth', 'role:super_admin'])->group(function () {
        Route::get('/system-settings', [HomeController::class, 'systemSettings'])->name('system.settings');
        Route::put('/system-settings', [HomeController::class, 'updateSystemSettings'])->name('system.settings.update');
    });
});

require __DIR__ . '/auth.php';
