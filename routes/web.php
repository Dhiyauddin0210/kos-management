<?php

use App\Http\Controllers\Admin\BankAccountController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\ExtensionController;
use App\Http\Controllers\Admin\InvoiceController;
use App\Http\Controllers\Admin\LeadController;
use App\Http\Controllers\Admin\MaintenanceController;
use App\Http\Controllers\Admin\PaymentController;
use App\Http\Controllers\Admin\PropertyController;
use App\Http\Controllers\Admin\RoomController;
use App\Http\Controllers\Admin\SettingController;
use App\Http\Controllers\Admin\TenantController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Tenant\DashboardController as TenantDashboardController;
use App\Http\Controllers\Tenant\ExtensionController as TenantExtensionController;
use App\Http\Controllers\Tenant\InvoiceController as TenantInvoiceController;
use App\Http\Controllers\Tenant\MaintenanceController as TenantMaintenanceController;
use App\Http\Controllers\Tenant\PaymentController as TenantPaymentController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    $user = auth()->user();
    if ($user->role === 'admin') {
        return redirect()->route('admin.dashboard');
    }
    if ($user->role === 'penghuni') {
        return redirect()->route('tenant.dashboard');
    }
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| ADMIN PANEL
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::redirect('/', '/admin/dashboard');

    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('properties', PropertyController::class);
    Route::resource('rooms', RoomController::class);
    Route::resource('tenants', TenantController::class);
    Route::post('tenants/{tenant}/checkout', [TenantController::class, 'checkout'])->name('tenants.checkout');

    Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);
    Route::post('invoices/generate', [InvoiceController::class, 'generate'])->name('invoices.generate');
    Route::post('invoices/{invoice}/send-reminder', [InvoiceController::class, 'sendReminder'])->name('invoices.send-reminder');
    Route::get('invoices/export', [InvoiceController::class, 'export'])->name('invoices.export');

    Route::resource('payments', PaymentController::class)->only(['index', 'show', 'destroy']);
    Route::post('payments/{payment}/verify', [PaymentController::class, 'verify'])->name('payments.verify');
    Route::post('payments/{payment}/reject', [PaymentController::class, 'reject'])->name('payments.reject');

    Route::resource('leads', LeadController::class)->except(['create', 'store', 'edit']);
    Route::get('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');

    Route::resource('maintenances', MaintenanceController::class)->except(['create', 'store', 'edit']);

    Route::resource('extensions', ExtensionController::class)->only(['index', 'show', 'destroy']);
    Route::post('extensions/{extension}/approve', [ExtensionController::class, 'approve'])->name('extensions.approve');
    Route::post('extensions/{extension}/reject', [ExtensionController::class, 'reject'])->name('extensions.reject');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
    Route::get('settings/qr-code', [SettingController::class, 'qrCode'])->name('settings.qr-code');
    Route::get('settings/qr-preview', [SettingController::class, 'qrPreview'])->name('settings.qr-preview');

    Route::resource('bank-accounts', BankAccountController::class)->only(['store', 'update', 'destroy']);
});

/*
|--------------------------------------------------------------------------
| TENANT PANEL (Penghuni)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'tenant'])->prefix('tenant')->name('tenant.')->group(function () {
    Route::redirect('/', '/tenant/dashboard');

    // Dashboard
    Route::get('/dashboard', [TenantDashboardController::class, 'index'])->name('dashboard');

    // Tagihan
    Route::get('invoices', [TenantInvoiceController::class, 'index'])->name('invoices.index');
    Route::get('invoices/{invoice}', [TenantInvoiceController::class, 'show'])->name('invoices.show');

    // Pembayaran
    Route::get('payments', [TenantPaymentController::class, 'index'])->name('payments.index');
    Route::post('invoices/{invoice}/pay', [TenantPaymentController::class, 'store'])->name('invoices.pay');

    // Maintenance (Lapor Kerusakan)
    Route::get('maintenances', [TenantMaintenanceController::class, 'index'])->name('maintenances.index');
    Route::get('maintenances/create', [TenantMaintenanceController::class, 'create'])->name('maintenances.create');
    Route::post('maintenances', [TenantMaintenanceController::class, 'store'])->name('maintenances.store');
    Route::get('maintenances/{maintenance}', [TenantMaintenanceController::class, 'show'])->name('maintenances.show');

    // Perpanjangan (Extensions)
    Route::get('extensions', [TenantExtensionController::class, 'index'])->name('extensions.index');
    Route::get('extensions/create', [TenantExtensionController::class, 'create'])->name('extensions.create');
    Route::post('extensions', [TenantExtensionController::class, 'store'])->name('extensions.store');
    Route::get('extensions/{extension}', [TenantExtensionController::class, 'show'])->name('extensions.show');
});

require __DIR__.'/auth.php';