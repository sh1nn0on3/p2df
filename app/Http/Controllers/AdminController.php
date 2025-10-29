<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\DecryptionRequest;
use App\Models\ForensicReport;
use App\Services\CryptoService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Exception;

/**
 * AdminController - Xử lý các chức năng dành cho Admin
 * 
 * Chức năng:
 * - Upload dataset email và mã hóa
 * - Xem danh sách yêu cầu giải mã
 * - Phê duyệt/từ chối yêu cầu giải mã
 * - Xem nhật ký điều tra
 */
class AdminController extends Controller
{
    protected $cryptoService;
    protected $logService;

    public function __construct(CryptoService $cryptoService, LogService $logService)
    {
        $this->cryptoService = $cryptoService;
        $this->logService = $logService;
    }

    /**
     * Dashboard admin - Hiển thị tổng quan
     */
    public function dashboard()
    {
        $stats = [
            'total_emails' => Email::count(),
            'pending_requests' => DecryptionRequest::pending()->count(),
            'approved_requests' => DecryptionRequest::approved()->count(),
            'rejected_requests' => DecryptionRequest::rejected()->count(),
        ];

        // Ghi log xem dashboard
        $this->logService->logViewDashboard('admin');

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Hiển thị trang mô tả luồng quá trình điều tra số
     */
    public function workflow()
    {
        // Ghi log xem workflow
        $this->logService->logViewWorkflow();
        
        return view('admin.workflow');
    }

    /**
     * Hiển thị form upload email
     */
    public function showUploadForm()
    {
        // Ghi log xem form upload
        $this->logService->log(LogService::ACTION_VIEW_UPLOAD_FORM);
        
        return view('admin.upload');
    }

    /**
     * Xử lý upload và mã hóa email dataset
     * 
     * Admin upload file CSV chứa danh sách email
     * Format CSV: from,to,subject,body
     */
    public function uploadEmails(Request $request)
    {
        $request->validate([
            'email_file' => 'required|file|mimes:csv,txt|max:10240', // Max 10MB
        ]);

        try {
            $file = $request->file('email_file');
            $path = $file->getRealPath();
            
            // Đọc file CSV
            $csv = array_map('str_getcsv', file($path));
            $header = array_shift($csv); // Bỏ dòng header
            
            $uploadedCount = 0;
            $errors = [];

            // Lấy public key của Admin
            $admin = Auth::user();
            $adminPublicKeyPath = storage_path($admin->public_key_path);

            if (!file_exists($adminPublicKeyPath)) {
                return back()->with('error', 'Admin public key not found. Please generate keys first.');
            }

            foreach ($csv as $index => $row) {
                if (count($row) < 4) {
                    $errors[] = "Row " . ($index + 2) . ": Invalid format";
                    continue;
                }

                try {
                    [$from, $to, $subject, $body] = $row;

                    // Sinh AES key ngẫu nhiên cho email này
                    $aesKey = $this->cryptoService->generateAesKey();

                    // Mã hóa nội dung email bằng AES
                    $bodyEncrypted = $this->cryptoService->aesEncrypt($body, $aesKey);

                    // Mã hóa AES key bằng Public Key của Admin
                    $aesKeyEncryptedAdmin = $this->cryptoService->rsaEncrypt($aesKey, $adminPublicKeyPath);

                    // Tạo hash để verify integrity
                    $hash = $this->cryptoService->hash($body);

                    // Lưu vào database
                    $email = Email::create([
                        'from' => $from,
                        'to' => $to,
                        'subject' => $subject,
                        'body_encrypted' => $bodyEncrypted,
                        'aes_key_encrypted_admin' => $aesKeyEncryptedAdmin,
                        'hash' => $hash,
                    ]);

                    // Ghi log
                    $this->logService->logUploadEmail($email->id, [
                        'from' => $from,
                        'to' => $to,
                        'subject' => $subject,
                    ]);

                    $uploadedCount++;

                } catch (Exception $e) {
                    $errors[] = "Row " . ($index + 2) . ": " . $e->getMessage();
                }
            }

            $message = "Uploaded {$uploadedCount} emails successfully.";
            if (!empty($errors)) {
                $message .= " Errors: " . implode('; ', array_slice($errors, 0, 5));
            }

            return back()->with('success', $message);

        } catch (Exception $e) {
            return back()->with('error', 'Upload failed: ' . $e->getMessage());
        }
    }

    /**
     * Danh sách tất cả email đã upload
     */
    public function listEmails(Request $request)
    {
        $query = Email::query();

        // Tìm kiếm
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        // Get all emails (no pagination)
        $stats = [
            'total' => Email::count(),
            'today' => Email::where('created_at', '>=', now()->startOfDay())->count(),
            'this_week' => Email::where('created_at', '>=', now()->startOfWeek())->count(),
            'this_month' => Email::where('created_at', '>=', now()->startOfMonth())->count(),
        ];

        $emails = $query->latest()->get();

        // Ghi log xem danh sách email
        $this->logService->logViewEmailList([
            'search' => $request->get('search'),
            'page' => $request->get('page', 1),
        ]);

        return view('admin.emails', compact('emails', 'stats'));
    }

    /**
     * Danh sách yêu cầu giải mã
     */
    public function listRequests(Request $request)
    {
        $status = $request->get('status', 'pending');

        $query = DecryptionRequest::with(['email', 'investigator']);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(20);

        // Ghi log xem danh sách requests
        $this->logService->logViewRequests([
            'status' => $status,
            'page' => $request->get('page', 1),
        ]);

        return view('admin.requests', compact('requests', 'status'));
    }

    /**
     * Phê duyệt yêu cầu giải mã
     * 
     * Quy trình:
     * 1. Giải mã AES key bằng Private Key của Admin
     * 2. Mã hóa lại AES key bằng Public Key của Investigator
     * 3. Cập nhật request status = approved
     */
    public function approveRequest($requestId)
    {
        try {
            $request = DecryptionRequest::with(['email', 'investigator'])->findOrFail($requestId);

            if (!$request->isPending()) {
                return back()->with('error', 'Request has already been processed.');
            }

            // Lấy keys
            $admin = Auth::user();
            $adminPrivateKeyPath = storage_path($admin->private_key_path);
            $investigatorPublicKeyPath = storage_path($request->investigator->public_key_path);

            if (!file_exists($adminPrivateKeyPath)) {
                return back()->with('error', 'Admin private key not found.');
            }

            if (!file_exists($investigatorPublicKeyPath)) {
                return back()->with('error', 'Investigator public key not found.');
            }

            // Bước 1: Giải mã AES key bằng Private Key của Admin
            $aesKey = $this->cryptoService->rsaDecrypt(
                $request->email->aes_key_encrypted_admin,
                $adminPrivateKeyPath
            );

            // Bước 2: Mã hóa lại AES key bằng Public Key của Investigator
            $aesKeyEncryptedInv = $this->cryptoService->rsaEncrypt(
                $aesKey,
                $investigatorPublicKeyPath
            );

            // Bước 3: Cập nhật request
            $request->update([
                'status' => 'approved',
                'aes_key_encrypted_inv' => $aesKeyEncryptedInv,
                'approved_at' => now(),
            ]);

            // Ghi log
            $this->logService->logApproveRequest(
                $request->id,
                $request->email_id,
                $request->investigator_id
            );

            return back()->with('success', 'Request approved successfully.');

        } catch (Exception $e) {
            return back()->with('error', 'Approval failed: ' . $e->getMessage());
        }
    }

    /**
     * Từ chối yêu cầu giải mã
     */
    public function rejectRequest($requestId)
    {
        try {
            $request = DecryptionRequest::with(['email', 'investigator'])->findOrFail($requestId);

            if (!$request->isPending()) {
                return back()->with('error', 'Request has already been processed.');
            }

            $request->update([
                'status' => 'rejected',
            ]);

            // Ghi log
            $this->logService->logRejectRequest(
                $request->id,
                $request->email_id,
                $request->investigator_id
            );

            return back()->with('success', 'Request rejected.');

        } catch (Exception $e) {
            return back()->with('error', 'Rejection failed: ' . $e->getMessage());
        }
    }

    /**
     * Xem nhật ký điều tra
     */
    public function viewLogs(Request $request)
    {
        $action = $request->get('action');
        $role = $request->get('role');
        $search = $request->get('search');

        // Get logs from database with eager loading
        $query = \App\Models\ForensicLog::with('user')->orderBy('created_at', 'desc');

        // Search filter
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('action', 'LIKE', "%{$search}%")
                  ->orWhere('target_id', 'LIKE', "%{$search}%")
                  ->orWhere('ip_address', 'LIKE', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('email', 'LIKE', "%{$search}%");
                  });
            });
        }

        // Action filter
        if ($action) {
            $query->where('action', $action);
        }
        
        // Role filter
        if ($role) {
            $query->where('role', $role);
        }

        $logs = $query->limit(100)->get();

        // Ghi log xem logs
        $this->logService->logViewLogs([
            'action' => $action,
            'role' => $role,
            'search' => $search,
        ]);

        return view('admin.logs', compact('logs', 'search'));
    }

    /**
     * Danh sách tất cả báo cáo từ investigator
     */
    public function listReports(Request $request)
    {
        $status = $request->get('status', 'all');
        $severity = $request->get('severity', 'all');

        $query = ForensicReport::with(['email', 'investigator', 'decryptionRequest']);

        // Filter by status
        if ($status !== 'all') {
            $query->where('status', $status);
        }

        // Filter by severity
        if ($severity !== 'all') {
            $query->where('severity', $severity);
        }

        $reports = $query->latest()->paginate(20);

        // Ghi log xem danh sách báo cáo
        $this->logService->log(LogService::ACTION_VIEW_REPORTS, null, [
            'status' => $status,
            'severity' => $severity,
            'page' => $request->get('page', 1),
        ]);

        return view('admin.reports', compact('reports', 'status', 'severity'));
    }

    /**
     * Xem chi tiết báo cáo
     */
    public function viewReport($reportId)
    {
        $report = ForensicReport::with(['email', 'investigator', 'decryptionRequest'])
            ->findOrFail($reportId);

        // Ghi log xem báo cáo
        $this->logService->log(LogService::ACTION_VIEW_REPORT, (string)$reportId, [
            'investigator_id' => $report->investigator_id,
        ]);

        return view('admin.report-detail', compact('report'));
    }

    /**
     * Duyệt báo cáo - đánh dấu là đã xem xét
     */
    public function reviewReport(Request $request, $reportId)
    {
        $request->validate([
            'admin_notes' => 'nullable|string|max:1000',
            'action' => 'required|in:approved,rejected,needs_revision',
        ]);

        try {
            $report = ForensicReport::findOrFail($reportId);

            if ($report->status !== 'completed') {
                return back()->with('error', 'Chỉ có thể duyệt báo cáo đã hoàn thành.');
            }

            $report->update([
                'admin_reviewed_at' => now(),
                'admin_reviewed_by' => Auth::id(),
                'admin_notes' => $request->admin_notes,
                'admin_action' => $request->action,
            ]);

            // Ghi log
            $this->logService->log(LogService::ACTION_REVIEW_REPORT, (string)$reportId, [
                'action' => $request->action,
                'admin_notes' => $request->admin_notes,
            ]);

            $actionText = match($request->action) {
                'approved' => 'đã phê duyệt',
                'rejected' => 'đã từ chối',
                'needs_revision' => 'yêu cầu chỉnh sửa',
                default => 'đã xem xét'
            };

            return back()->with('success', "Báo cáo đã được {$actionText} thành công.");

        } catch (Exception $e) {
            return back()->with('error', 'Duyệt báo cáo thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Thống kê báo cáo
     */
    public function reportStats()
    {
        $stats = [
            'total_reports' => ForensicReport::count(),
            'completed_reports' => ForensicReport::completed()->count(),
            'pending_review' => ForensicReport::completed()
                ->whereNull('admin_reviewed_at')
                ->count(),
            'approved_reports' => ForensicReport::where('admin_action', 'approved')->count(),
            'rejected_reports' => ForensicReport::where('admin_action', 'rejected')->count(),
            'needs_revision' => ForensicReport::where('admin_action', 'needs_revision')->count(),
        ];

        // Thống kê theo mức độ nghiêm trọng
        $severityStats = ForensicReport::selectRaw('severity, COUNT(*) as count')
            ->groupBy('severity')
            ->pluck('count', 'severity')
            ->toArray();

        return view('admin.report-stats', compact('stats', 'severityStats'));
    }

    /**
     * Admin đọc nội dung email (chỉ khi cần thiết)
     */
    public function readEmailContent($emailId)
    {
        try {
            $email = Email::findOrFail($emailId);
            $admin = Auth::user();
            
            // Lấy private key của admin
            $adminPrivateKeyPath = storage_path($admin->private_key_path);
            
            if (!file_exists($adminPrivateKeyPath)) {
                return back()->with('error', 'Admin private key not found.');
            }

            // Giải mã AES key bằng Private Key của Admin
            $aesKey = $this->cryptoService->rsaDecrypt(
                $email->aes_key_encrypted_admin,
                $adminPrivateKeyPath
            );

            // Giải mã nội dung email
            $bodyDecrypted = $this->cryptoService->aesDecrypt(
                $email->body_encrypted,
                $aesKey
            );

            // Verify hash
            $isValid = $this->cryptoService->verifyHash($bodyDecrypted, $email->hash);

            // Ghi log - admin đọc nội dung email
            $this->logService->log(LogService::ACTION_VIEW_EMAIL, (string)$emailId, [
                'admin_read_content' => true,
                'hash_valid' => $isValid,
            ]);

            return view('admin.email-content', compact('email', 'bodyDecrypted', 'isValid'));

        } catch (Exception $e) {
            return back()->with('error', 'Không thể đọc nội dung email: ' . $e->getMessage());
        }
    }
}

