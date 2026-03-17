<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserImportController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\BlogController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ActivityLogController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware(['auth'])->group(function () {

    // Product routes
    Route::get('/products/export', [ProductController::class, 'exportExcel'])
        ->name('products.export');

    Route::resource('products', ProductController::class);

    // User import routes
    Route::get('/users/import', [UserImportController::class, 'index'])->name('users.import.index');
    Route::post('/users/import', [UserImportController::class, 'import'])->name('users.import');

    // Full CRUD for user management
    Route::resource('users', UserController::class);

    // Breeze profile
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::patch('/profile/photo', [ProfileController::class, 'updatePhoto'])->middleware('auth')->name('profile.photo.update');


    //roles and permission
    //Route::resource('roles', \App\Http\Controllers\RoleController::class);

    Route::middleware(['role:Admin'])->group(function () {
        Route::resource('roles', RoleController::class);
        
        // Activity Logs routes
        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
        Route::get('/activity-logs/export', [ActivityLogController::class, 'export'])->name('activity-logs.export');
    });

    //blogs routes
    Route::resource('blogs', BlogController::class);
    Route::get('/blogs/{id}/pdf', [BlogController::class, 'downloadPdf'])
    ->name('blogs.pdf');

    //invoices routes 
    Route::get('/invoices/datatable', [InvoiceController::class, 'datatable'])
    ->name('invoices.datatable');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])
    ->name('invoices.pdf');
    Route::resource('invoices', InvoiceController::class);
    


});

require __DIR__.'/auth.php';
