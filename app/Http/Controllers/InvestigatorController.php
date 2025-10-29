<?php

namespace App\Http\Controllers;

use App\Models\Email;
use App\Models\DecryptionRequest;
use App\Models\ForensicReport;
use App\Models\ForensicLog;
use App\Services\CryptoService;
use App\Services\LogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

/**
 * InvestigatorController - Xử lý các chức năng dành cho Investigator
 * 
 * Chức năng:
 * - Xem danh sách email (chỉ metadata)
 * - Gửi yêu cầu giải mã
 * - Xem danh sách yêu cầu của mình
 * - Tự giải mã email khi được phê duyệt
 */
class InvestigatorController extends Controller
{
    protected $cryptoService;
    protected $logService;

    public function __construct(CryptoService $cryptoService, LogService $logService)
    {
        $this->cryptoService = $cryptoService;
        $this->logService = $logService;
    }

    /**
     * Dashboard investigator - Hiển thị tổng quan
     */
    public function dashboard()
    {
        $investigator = Auth::user();

        $stats = [
            'total_emails' => Email::count(),
            'my_pending_requests' => $investigator->decryptionRequests()->pending()->count(),
            'my_approved_requests' => $investigator->decryptionRequests()->approved()->count(),
            'my_rejected_requests' => $investigator->decryptionRequests()->rejected()->count(),
        ];

        // Danh sách email mới nhất
        $recentEmails = Email::latest()->limit(10)->get();

        // Ghi log xem dashboard
        $this->logService->logViewDashboard('investigator');

        return view('investigator.dashboard', compact('stats', 'recentEmails'));
    }

    /**
     * Danh sách email (chỉ hiển thị metadata - không có nội dung)
     */
    public function listEmails(Request $request)
    {
        $query = Email::query();

        // Tìm kiếm
        if ($search = $request->get('search')) {
            $query->search($search);
        }

        $emails = $query->latest()->paginate(20);

        // Ghi log xem danh sách email
        $this->logService->logViewEmailList([
            'search' => $request->get('search'),
            'page' => $request->get('page', 1),
        ]);

        return view('investigator.emails', compact('emails'));
    }

    /**
     * Xem chi tiết email (chỉ metadata)
     */
    public function viewEmail($emailId)
    {
        $email = Email::findOrFail($emailId);
        $investigator = Auth::user();

        // Kiểm tra xem đã có request cho email này chưa
        $existingRequest = DecryptionRequest::where('email_id', $emailId)
            ->where('investigator_id', $investigator->id)
            ->first();

        // Ghi log xem email
        $this->logService->logViewEmail($emailId);

        return view('investigator.email_view', compact('email', 'existingRequest'));
    }

    /**
     * Gửi yêu cầu giải mã email
     */
    public function requestDecryption(Request $request, $emailId)
    {
        $request->validate([
            'reason' => 'required|string|min:10|max:500',
        ]);

        try {
            $investigator = Auth::user();

            // Kiểm tra xem đã request chưa
            $existing = DecryptionRequest::where('email_id', $emailId)
                ->where('investigator_id', $investigator->id)
                ->first();

            if ($existing) {
                return back()->with('error', 'You have already requested decryption for this email.');
            }

            // Tạo request
            $decryptionRequest = DecryptionRequest::create([
                'email_id' => $emailId,
                'investigator_id' => $investigator->id,
                'reason' => $request->reason,
                'status' => 'pending',
            ]);

            // Ghi log
            $this->logService->logRequestDecrypt(
                $decryptionRequest->id,
                $emailId,
                $request->reason
            );

            return back()->with('success', 'Decryption request submitted successfully. Waiting for admin approval.');

        } catch (Exception $e) {
            return back()->with('error', 'Request failed: ' . $e->getMessage());
        }
    }

    /**
     * Danh sách yêu cầu giải mã của investigator
     */
    public function myRequests(Request $request)
    {
        $investigator = Auth::user();
        $status = $request->get('status', 'all');

        $query = DecryptionRequest::with('email')
            ->where('investigator_id', $investigator->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $requests = $query->latest()->paginate(20);

        // Ghi log xem danh sách requests
        $this->logService->logViewRequests([
            'status' => $status,
            'page' => $request->get('page', 1),
        ]);

        return view('investigator.requests', compact('requests', 'status'));
    }

    /**
     * Tự giải mã email khi đã được phê duyệt
     * 
     * Quy trình:
     * 1. Kiểm tra request đã approved
     * 2. Giải mã AES key bằng Private Key của Investigator
     * 3. Giải mã nội dung email bằng AES key
     * 4. Hiển thị plaintext (tạm thời)
     */
    public function decryptEmail($requestId)
    {
        try {
            $investigator = Auth::user();
            $request = DecryptionRequest::with('email')
                ->where('id', $requestId)
                ->where('investigator_id', $investigator->id)
                ->firstOrFail();

            // Kiểm tra status
            if (!$request->isApproved()) {
                return back()->with('error', 'Request has not been approved yet.');
            }

            // Kiểm tra có AES key encrypted không
            if (empty($request->aes_key_encrypted_inv)) {
                return back()->with('error', 'AES key not available.');
            }

            // Lấy private key của investigator
            $investigatorPrivateKeyPath = storage_path($investigator->private_key_path);

            if (!file_exists($investigatorPrivateKeyPath)) {
                return back()->with('error', 'Your private key not found.');
            }

            // Bước 1: Giải mã AES key bằng Private Key của Investigator
            $aesKey = $this->cryptoService->rsaDecrypt(
                $request->aes_key_encrypted_inv,
                $investigatorPrivateKeyPath
            );

            // Bước 2: Giải mã nội dung email bằng AES key
            $bodyDecrypted = $this->cryptoService->aesDecrypt(
                $request->email->body_encrypted,
                $aesKey
            );

            // Verify hash
            $isValid = $this->cryptoService->verifyHash($bodyDecrypted, $request->email->hash);

            // Ghi log
            $this->logService->logDecryptEmail($request->email_id, $request->id);

            return view('investigator.email_decrypted', [
                'email' => $request->email,
                'request' => $request,
                'bodyDecrypted' => $bodyDecrypted,
                'isValid' => $isValid,
            ]);

        } catch (Exception $e) {
            return back()->with('error', 'Decryption failed: ' . $e->getMessage());
        }
    }

    /**
     * Xem lịch sử hoạt động của investigator
     */
    public function myLogs()
    {
        $investigator = Auth::user();
        $logs = $this->logService->getLogsByUser($investigator->id, 50);

        return view('investigator.logs', compact('logs'));
    }

    /**
     * Danh sách báo cáo điều tra của investigator
     */
    public function myReports(Request $request)
    {
        $investigator = Auth::user();
        $status = $request->get('status', 'all');

        $query = ForensicReport::with(['email'])
            ->where('investigator_id', $investigator->id);

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $reports = $query->latest()->paginate(20);

        return view('investigator.reports', compact('reports', 'status'));
    }

    /**
     * Form tạo báo cáo điều tra mới
     */
    public function createReportForm($emailId)
    {
        $investigator = Auth::user();
        $email = Email::findOrFail($emailId);

        // Kiểm tra xem investigator đã có approved request chưa
        $approvedRequest = DecryptionRequest::where('email_id', $emailId)
            ->where('investigator_id', $investigator->id)
            ->where('status', 'approved')
            ->first();

        if (!$approvedRequest) {
            return back()->with('error', 'You must have an approved decryption request to create a report for this email.');
        }

        // Lấy logs liên quan đến email này
        $relatedLogs = ForensicLog::where(function($query) use ($emailId) {
            $query->where('action', 'view_email')
                  ->orWhere('action', 'request_decrypt')
                  ->orWhere('action', 'decrypt_email');
        })
        ->where('target_id', $emailId)
        ->orWhere('target_id', $approvedRequest->id)
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('investigator.report_create', compact('email', 'approvedRequest', 'relatedLogs'));
    }

    /**
     * Lưu báo cáo điều tra
     */
    public function storeReport(Request $request, $emailId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'severity' => 'required|in:low,medium,high,critical',
            'findings' => 'required|string|min:50',
            'analysis' => 'required|string|min:100',
            'recommendations' => 'nullable|string',
            'related_logs' => 'nullable|array',
            'status' => 'required|in:draft,completed',
        ]);

        try {
            $investigator = Auth::user();

            // Tìm decryption request
            $decryptionRequest = DecryptionRequest::where('email_id', $emailId)
                ->where('investigator_id', $investigator->id)
                ->where('status', 'approved')
                ->firstOrFail();

            $report = ForensicReport::create([
                'email_id' => $emailId,
                'investigator_id' => $investigator->id,
                'decryption_request_id' => $decryptionRequest->id,
                'title' => $request->title,
                'severity' => $request->severity,
                'findings' => $request->findings,
                'analysis' => $request->analysis,
                'recommendations' => $request->recommendations,
                'related_logs' => $request->related_logs ?? [],
                'status' => $request->status,
                'completed_at' => $request->status === 'completed' ? now() : null,
            ]);

            // Ghi log
            $this->logService->logCreateReport(
                $report->id,
                $emailId,
                $request->title
            );

            return redirect()->route('investigator.reports.view', $report->id)
                ->with('success', 'Forensic report created successfully.');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to create report: ' . $e->getMessage());
        }
    }

    /**
     * Investigator đổi trạng thái báo cáo từ draft thành completed
     */
    public function updateReportStatus(Request $request, $reportId)
    {
        $request->validate([
            'status' => 'required|in:draft,completed',
        ]);

        try {
            $investigator = Auth::user();
            $report = ForensicReport::where('id', $reportId)
                ->where('investigator_id', $investigator->id)
                ->firstOrFail();

            $oldStatus = $report->status;
            $newStatus = $request->status;

            // Cập nhật trạng thái
            $report->update([
                'status' => $newStatus,
                'completed_at' => $newStatus === 'completed' ? now() : null,
            ]);

            // Ghi log
            $this->logService->logUpdateReport($report->id, $newStatus, [
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
            ]);

            $statusText = $newStatus === 'completed' ? 'hoàn thành' : 'bản nháp';
            return back()->with('success', "Báo cáo đã được chuyển sang trạng thái {$statusText}.");

        } catch (Exception $e) {
            return back()->with('error', 'Cập nhật trạng thái thất bại: ' . $e->getMessage());
        }
    }

    /**
     * Xem chi tiết báo cáo điều tra
     */
    public function viewReport($reportId)
    {
        $investigator = Auth::user();
        
        $report = ForensicReport::with(['email', 'decryptionRequest'])
            ->where('id', $reportId)
            ->where('investigator_id', $investigator->id)
            ->firstOrFail();

        // Ghi log xem báo cáo
        $this->logService->logViewReport($report->id);

        // Lấy logs đã được chọn trong report
        $selectedLogs = [];
        if ($report->related_logs && count($report->related_logs) > 0) {
            $selectedLogs = ForensicLog::whereIn('id', $report->related_logs)
                ->with('user')
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('investigator.report_view', compact('report', 'selectedLogs'));
    }

    /**
     * Form chỉnh sửa báo cáo
     */
    public function editReportForm($reportId)
    {
        $investigator = Auth::user();
        
        $report = ForensicReport::with(['email'])
            ->where('id', $reportId)
            ->where('investigator_id', $investigator->id)
            ->firstOrFail();

        if ($report->isCompleted()) {
            return back()->with('error', 'Cannot edit completed report.');
        }

        // Lấy logs liên quan
        $relatedLogs = ForensicLog::where(function($query) use ($report) {
            $query->where('action', 'view_email')
                  ->orWhere('action', 'request_decrypt')
                  ->orWhere('action', 'decrypt_email')
                  ->orWhere('action', 'create_report');
        })
        ->where(function($query) use ($report) {
            $query->where('target_id', $report->email_id)
                  ->orWhere('target_id', $report->decryption_request_id)
                  ->orWhere('target_id', $report->id);
        })
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

        return view('investigator.report_edit', compact('report', 'relatedLogs'));
    }

    /**
     * Cập nhật báo cáo
     */
    public function updateReport(Request $request, $reportId)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'severity' => 'required|in:low,medium,high,critical',
            'findings' => 'required|string|min:50',
            'analysis' => 'required|string|min:100',
            'recommendations' => 'nullable|string',
            'related_logs' => 'nullable|array',
            'status' => 'required|in:draft,completed',
        ]);

        try {
            $investigator = Auth::user();
            
            $report = ForensicReport::where('id', $reportId)
                ->where('investigator_id', $investigator->id)
                ->firstOrFail();

            $report->update([
                'title' => $request->title,
                'severity' => $request->severity,
                'findings' => $request->findings,
                'analysis' => $request->analysis,
                'recommendations' => $request->recommendations,
                'related_logs' => $request->related_logs ?? [],
                'status' => $request->status,
                'completed_at' => $request->status === 'completed' ? now() : null,
            ]);

            // Ghi log
            $this->logService->logUpdateReport(
                $report->id,
                $request->status,
                ['title' => $request->title, 'severity' => $request->severity]
            );

            return redirect()->route('investigator.reports.view', $report->id)
                ->with('success', 'Report updated successfully.');

        } catch (Exception $e) {
            return back()->with('error', 'Failed to update report: ' . $e->getMessage());
        }
    }

    /**
     * Trích xuất logs liên quan đến email (AJAX)
     */
    public function extractEmailLogs($emailId)
    {
        $investigator = Auth::user();

        // Kiểm tra quyền truy cập
        $approvedRequest = DecryptionRequest::where('email_id', $emailId)
            ->where('investigator_id', $investigator->id)
            ->where('status', 'approved')
            ->first();

        if (!$approvedRequest) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        // Lấy tất cả logs liên quan đến email này
        $logs = ForensicLog::where(function($query) use ($emailId, $approvedRequest) {
            $query->where('target_id', $emailId)
                  ->orWhere('target_id', $approvedRequest->id);
        })
        ->orWhere(function($query) use ($emailId) {
            $query->where('action', 'view_email')
                  ->where('details->email_id', $emailId);
        })
        ->with('user')
        ->orderBy('created_at', 'desc')
        ->get();

        // Ghi log trích xuất logs
        $this->logService->logExtractEmailLogs($emailId, $logs->count());

        return response()->json([
            'logs' => $logs,
            'count' => $logs->count(),
        ]);
    }
}

