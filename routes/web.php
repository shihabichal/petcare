<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OwnerController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\ExportController;

Route::get('/', fn() => redirect('/login'));

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Internal AJAX API (requires auth)
Route::middleware('auth')->prefix('api')->group(function () {
    Route::get('/customers/{customer}/pets', [ApiController::class, 'petsByCustomer'])->name('api.pets.by.customer');
});

// ============================================================
// OWNER ROUTES
// ============================================================
Route::middleware(['auth', 'is.owner'])->prefix('owner')->name('owner.')->group(function () {

    Route::get('/dashboard', [OwnerController::class, 'dashboard'])->name('dashboard');

    // Admin management
    Route::get('/admins', [OwnerController::class, 'admins'])->name('admins');
    Route::post('/admins', [OwnerController::class, 'storeAdmin'])->name('admins.store');
    Route::put('/admins/{user}/password', [OwnerController::class, 'updateAdminPassword'])->name('admins.password');
    Route::delete('/admins/{user}', [OwnerController::class, 'destroyAdmin'])->name('admins.destroy');

    // Customer management
    Route::get('/customers', [OwnerController::class, 'customers'])->name('customers');
    Route::post('/customers', [OwnerController::class, 'storeCustomer'])->name('customers.store');
    Route::put('/customers/{customer}', [OwnerController::class, 'updateCustomer'])->name('customers.update');
    Route::delete('/customers/{customer}', [OwnerController::class, 'destroyCustomer'])->name('customers.destroy');

    // Pet management (under a customer)
    Route::post('/customers/{customer}/pets', [OwnerController::class, 'storePet'])->name('pets.store');
    Route::delete('/pets/{pet}', [OwnerController::class, 'destroyPet'])->name('pets.destroy');

    // Service management
    Route::get('/services', [OwnerController::class, 'services'])->name('services');
    Route::post('/services', [OwnerController::class, 'storeService'])->name('services.store');
    Route::put('/services/{service}', [OwnerController::class, 'updateService'])->name('services.update');
    Route::delete('/services/{service}', [OwnerController::class, 'destroyService'])->name('services.destroy');

    // Transaction management
    Route::get('/transactions', [OwnerController::class, 'transactions'])->name('transactions');
    Route::post('/transactions', [OwnerController::class, 'storeTransaction'])->name('transactions.store');
    Route::put('/transactions/{transaction}/verify', [OwnerController::class, 'verifyPayment'])->name('transactions.verify');
    Route::put('/transactions/{transaction}/status', [OwnerController::class, 'updateStatus'])->name('transactions.status');
    Route::put('/transactions/{transaction}/notes', [OwnerController::class, 'updateTransaction'])->name('transactions.notes');
    Route::delete('/transactions/{transaction}', [OwnerController::class, 'destroyTransaction'])->name('transactions.destroy');

    // Export PDF
    Route::get('/export/customers',    [ExportController::class, 'customers'])->name('export.customers');
    Route::get('/export/transactions', [ExportController::class, 'transactions'])->name('export.transactions');
});

// ============================================================
// ADMIN ROUTES
// ============================================================
Route::middleware(['auth', 'is.admin'])->prefix('admin')->name('admin.')->group(function () {

    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Customer management
    Route::get('/customers', [AdminController::class, 'customers'])->name('customers');
    Route::post('/customers', [AdminController::class, 'storeCustomer'])->name('customers.store');
    Route::put('/customers/{customer}', [AdminController::class, 'updateCustomer'])->name('customers.update');
    Route::delete('/customers/{customer}', [AdminController::class, 'destroyCustomer'])->name('customers.destroy');

    // Pet management
    Route::post('/customers/{customer}/pets', [AdminController::class, 'storePet'])->name('pets.store');
    Route::delete('/pets/{pet}', [AdminController::class, 'destroyPet'])->name('pets.destroy');

    // Transaction management
    Route::get('/transactions', [AdminController::class, 'transactions'])->name('transactions');
    Route::post('/transactions', [AdminController::class, 'storeTransaction'])->name('transactions.store');
    Route::put('/transactions/{transaction}/verify', [AdminController::class, 'verifyPayment'])->name('transactions.verify');
    Route::put('/transactions/{transaction}/status', [AdminController::class, 'updateStatus'])->name('transactions.status');
    Route::put('/transactions/{transaction}/notes', [AdminController::class, 'updateNotes'])->name('transactions.notes');
});
