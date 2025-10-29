<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\InvestigatorController;

/*
|--------------------------------------------------------------------------
| Web Routes - P2DF Email Forensic System
|--------------------------------------------------------------------------
|
| Routes phân quyền theo role:
| - Guest: login
| - Admin: quản lý email, duyệt request, xem logs
| - Investigator: xem email, gửi request, giải mã
|
*/

// Trang chủ - redirect to login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/home', function () {
    return redirect()->route('/');
});
// ===========================
// Authentication Routes
// ===========================
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
});

Route::post('/logout', [AuthController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// ===========================
// Admin Routes
// ===========================
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Email Management
    Route::get('/emails', [AdminController::class, 'listEmails'])->name('emails');
    Route::get('/upload', [AdminController::class, 'showUploadForm'])->name('upload');
    Route::post('/upload', [AdminController::class, 'uploadEmails'])->name('upload.submit');

    // Decryption Requests Management
    Route::get('/requests', [AdminController::class, 'listRequests'])->name('requests');
    Route::post('/requests/{id}/approve', [AdminController::class, 'approveRequest'])->name('requests.approve');
    Route::post('/requests/{id}/reject', [AdminController::class, 'rejectRequest'])->name('requests.reject');

    // Forensic Logs
    Route::get('/logs', [AdminController::class, 'viewLogs'])->name('logs');

    // Workflow Documentation
    Route::get('/workflow', [AdminController::class, 'workflow'])->name('workflow');

    // Forensic Reports Management
    Route::get('/reports', [AdminController::class, 'listReports'])->name('reports');
    Route::get('/reports/{id}', [AdminController::class, 'viewReport'])->name('reports.view');
    Route::post('/reports/{id}/review', [AdminController::class, 'reviewReport'])->name('reports.review');
    Route::get('/reports-stats', [AdminController::class, 'reportStats'])->name('reports.stats');
    
    // Admin read email content
    Route::get('/emails/{id}/content', [AdminController::class, 'readEmailContent'])->name('emails.content');
});

// ===========================
// Investigator Routes
// ===========================
Route::prefix('investigator')->name('investigator.')->middleware(['auth', 'role:investigator'])->group(function () {
    // Dashboard
    Route::get('/dashboard', [InvestigatorController::class, 'dashboard'])->name('dashboard');

    // Email Viewing (metadata only)
    Route::get('/emails', [InvestigatorController::class, 'listEmails'])->name('emails');
    Route::get('/emails/{id}', [InvestigatorController::class, 'viewEmail'])->name('emails.view');

    // Decryption Request
    Route::post('/emails/{id}/request', [InvestigatorController::class, 'requestDecryption'])->name('emails.request');

    // My Requests
    Route::get('/requests', [InvestigatorController::class, 'myRequests'])->name('requests');
    Route::get('/requests/{id}/decrypt', [InvestigatorController::class, 'decryptEmail'])->name('requests.decrypt');

    // My Logs
    Route::get('/logs', [InvestigatorController::class, 'myLogs'])->name('logs');

    // Forensic Reports
    Route::get('/reports', [InvestigatorController::class, 'myReports'])->name('reports');
    Route::get('/emails/{id}/create-report', [InvestigatorController::class, 'createReportForm'])->name('reports.create');
    Route::post('/emails/{id}/create-report', [InvestigatorController::class, 'storeReport'])->name('reports.store');
    Route::get('/reports/{id}', [InvestigatorController::class, 'viewReport'])->name('reports.view');
    Route::get('/reports/{id}/edit', [InvestigatorController::class, 'editReportForm'])->name('reports.edit');
    Route::put('/reports/{id}', [InvestigatorController::class, 'updateReport'])->name('reports.update');
    Route::post('/reports/{id}/status', [InvestigatorController::class, 'updateReportStatus'])->name('reports.status');
    
    // AJAX endpoint for extracting logs
    Route::get('/emails/{id}/logs', [InvestigatorController::class, 'extractEmailLogs'])->name('emails.logs');
});
