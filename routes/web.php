<?php

use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\QuotationController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\SettingController;
use Illuminate\Support\Facades\Route;

// Redirect root to dashboard
Route::get('/', fn () => redirect()->route('dashboard'));

// Debug/Test routes
Route::get('/test/delete-status', [\App\Http\Controllers\TestController::class, 'testDelete'])->name('test.delete-status');
Route::post('/test/destroy-customer/{id}', [\App\Http\Controllers\TestController::class, 'testDestroyCustomer'])->name('test.destroy-customer');
// Auth routes (Breeze)
require __DIR__ . '/auth.php';

Route::get('/test/delete-model', [\App\Http\Controllers\TestController::class, 'testDeleteModel'])->name('test.delete-model');
// Protected routes
Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Customers
    Route::resource('customers', CustomerController::class);

    // Items / Services
    Route::get('/items/search', [ItemController::class, 'search'])->name('items.search');
    Route::resource('items', ItemController::class)->except(['show']);

    // Quotations
    Route::post('/quotations/{quotation}/duplicate', [QuotationController::class, 'duplicate'])->name('quotations.duplicate');
    Route::post('/quotations/{quotation}/convert-to-order', [QuotationController::class, 'convertToOrder'])->name('quotations.convert-to-order');
    Route::patch('/quotations/{quotation}/status', [QuotationController::class, 'updateStatus'])->name('quotations.update-status');
    Route::get('/quotations/{quotation}/pdf', [QuotationController::class, 'pdf'])->name('quotations.pdf');
    Route::resource('quotations', QuotationController::class);

    // Orders
    Route::patch('/orders/{order}/status', [OrderController::class, 'updateStatus'])->name('orders.update-status');
    Route::post('/orders/{order}/convert-to-invoice', [OrderController::class, 'convertToInvoice'])->name('orders.convert-to-invoice');
    Route::post('/orders/{order}/convert-to-project', [OrderController::class, 'convertToProject'])->name('orders.convert-to-project');
    Route::resource('orders', OrderController::class);

    // Projects
    Route::resource('projects', ProjectController::class)->only(['index', 'show']);
    Route::patch('/projects/{project}/status', [ProjectController::class, 'updateStatus'])->name('projects.update-status');
    Route::post('/projects/{project}/expenses', [ProjectController::class, 'storeExpense'])->name('projects.add-expense');
    Route::delete('/projects/{project}/expenses/{expense}', [ProjectController::class, 'destroyExpense'])->name('projects.delete-expense');
    Route::patch('/projects/{project}/advance', [ProjectController::class, 'updateAdvance'])->name('projects.update-advance');

    // Purchase Orders
    Route::patch('/purchase-orders/{purchaseOrder}/status', [PurchaseOrderController::class, 'updateStatus'])->name('purchase-orders.update-status');
    Route::get('/purchase-orders/{purchaseOrder}/pdf', [PurchaseOrderController::class, 'pdf'])->name('purchase-orders.pdf');
    Route::resource('purchase-orders', PurchaseOrderController::class);

    // Invoices
    Route::post('/invoices/{invoice}/payment', [InvoiceController::class, 'addPayment'])->name('invoices.add-payment');
    Route::patch('/invoices/{invoice}/status', [InvoiceController::class, 'updateStatus'])->name('invoices.update-status');
    Route::get('/invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');
    Route::resource('invoices', InvoiceController::class)->except(['edit', 'update']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');

    // Settings
    Route::get('/settings', [SettingController::class, 'index'])->name('settings.index');
    Route::patch('/settings', [SettingController::class, 'update'])->name('settings.update');
});
