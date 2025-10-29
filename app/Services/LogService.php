<?php

namespace App\Services;

use App\Models\ForensicLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

/**
 * LogService - Ghi nhật ký điều tra cho hệ thống P2DF
 * 
 * Ghi lại tất cả các hành động:
 * - Upload email
 * - Yêu cầu giải mã
 * - Phê duyệt/từ chối yêu cầu
 * - Xem/giải mã email
 * 
 * @package App\Services
 */
class LogService
{
    /**
     * Các loại hành động có thể log
     */
    public const ACTION_LOGIN = 'login';
    public const ACTION_LOGOUT = 'logout';
    public const ACTION_UPLOAD_EMAIL = 'upload_email';
    public const ACTION_VIEW_EMAIL = 'view_email';
    public const ACTION_REQUEST_DECRYPT = 'request_decrypt';
    public const ACTION_APPROVE_REQUEST = 'approve_request';
    public const ACTION_REJECT_REQUEST = 'reject_request';
    public const ACTION_DECRYPT_EMAIL = 'decrypt_email';
    public const ACTION_GENERATE_KEYS = 'generate_keys';
    public const ACTION_VIEW_LOGS = 'view_logs';
    public const ACTION_CREATE_REPORT = 'create_report';
    public const ACTION_UPDATE_REPORT = 'update_report';
    public const ACTION_VIEW_REPORT = 'view_report';
    public const ACTION_VIEW_DASHBOARD = 'view_dashboard';
    public const ACTION_VIEW_EMAIL_LIST = 'view_email_list';
    public const ACTION_VIEW_REQUESTS = 'view_requests';
    public const ACTION_VIEW_WORKFLOW = 'view_workflow';
    public const ACTION_EXTRACT_EMAIL_LOGS = 'extract_email_logs';
    public const ACTION_VIEW_UPLOAD_FORM = 'view_upload_form';
    public const ACTION_VIEW_REPORTS = 'view_reports';
    public const ACTION_REVIEW_REPORT = 'review_report';

    /**
     * Ghi log hành động của user
     * 
     * @param string $action Loại hành động (sử dụng các constants)
     * @param string|null $targetId ID của đối tượng liên quan (email_id, request_id, etc.)
     * @param array $details Chi tiết bổ sung (sẽ được lưu dạng JSON)
     * @param User|null $user User thực hiện (nếu null sẽ lấy user hiện tại)
     * @return ForensicLog
     */
    public function log(
        string $action,
        ?string $targetId = null,
        array $details = [],
        ?User $user = null
    ): ForensicLog {
        // Lấy user hiện tại nếu không truyền vào
        if (!$user) {
            $user = Auth::user();
        }

        // Nếu vẫn không có user (chưa login), throw exception
        if (!$user) {
            throw new \Exception('Cannot log action: No authenticated user');
        }

        // Tạo log entry
        return ForensicLog::create([
            'user_id' => $user->id,
            'role' => $user->role,
            'action' => $action,
            'target_id' => $targetId,
            'ip_address' => Request::ip(),
            'details' => !empty($details) ? $details : null,
            'created_at' => now(),
        ]);
    }

    /**
     * Log hành động login
     * 
     * @param User $user
     * @return ForensicLog
     */
    public function logLogin(User $user): ForensicLog
    {
        return $this->log(
            self::ACTION_LOGIN,
            null,
            ['user_agent' => Request::userAgent()],
            $user
        );
    }

    /**
     * Log hành động logout
     * 
     * @param User $user
     * @return ForensicLog
     */
    public function logLogout(User $user): ForensicLog
    {
        return $this->log(
            self::ACTION_LOGOUT,
            null,
            [],
            $user
        );
    }

    /**
     * Log upload email
     * 
     * @param int $emailId
     * @param array $details Chi tiết (from, to, subject, etc.)
     * @return ForensicLog
     */
    public function logUploadEmail(int $emailId, array $details = []): ForensicLog
    {
        return $this->log(
            self::ACTION_UPLOAD_EMAIL,
            (string)$emailId,
            $details
        );
    }

    /**
     * Log xem email (metadata)
     * 
     * @param int $emailId
     * @return ForensicLog
     */
    public function logViewEmail(int $emailId): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_EMAIL,
            (string)$emailId
        );
    }

    /**
     * Log yêu cầu giải mã
     * 
     * @param int $requestId
     * @param int $emailId
     * @param string $reason
     * @return ForensicLog
     */
    public function logRequestDecrypt(int $requestId, int $emailId, string $reason): ForensicLog
    {
        return $this->log(
            self::ACTION_REQUEST_DECRYPT,
            (string)$requestId,
            [
                'email_id' => $emailId,
                'reason' => $reason,
            ]
        );
    }

    /**
     * Log phê duyệt yêu cầu giải mã
     * 
     * @param int $requestId
     * @param int $emailId
     * @param int $investigatorId
     * @return ForensicLog
     */
    public function logApproveRequest(int $requestId, int $emailId, int $investigatorId): ForensicLog
    {
        return $this->log(
            self::ACTION_APPROVE_REQUEST,
            (string)$requestId,
            [
                'email_id' => $emailId,
                'investigator_id' => $investigatorId,
            ]
        );
    }

    /**
     * Log từ chối yêu cầu giải mã
     * 
     * @param int $requestId
     * @param int $emailId
     * @param int $investigatorId
     * @return ForensicLog
     */
    public function logRejectRequest(int $requestId, int $emailId, int $investigatorId): ForensicLog
    {
        return $this->log(
            self::ACTION_REJECT_REQUEST,
            (string)$requestId,
            [
                'email_id' => $emailId,
                'investigator_id' => $investigatorId,
            ]
        );
    }

    /**
     * Log giải mã email
     * 
     * @param int $emailId
     * @param int $requestId
     * @return ForensicLog
     */
    public function logDecryptEmail(int $emailId, int $requestId): ForensicLog
    {
        return $this->log(
            self::ACTION_DECRYPT_EMAIL,
            (string)$emailId,
            [
                'request_id' => $requestId,
            ]
        );
    }

    /**
     * Log sinh RSA key pair
     * 
     * @param string $keyName
     * @return ForensicLog
     */
    public function logGenerateKeys(string $keyName): ForensicLog
    {
        return $this->log(
            self::ACTION_GENERATE_KEYS,
            null,
            ['key_name' => $keyName]
        );
    }

    /**
     * Log xem nhật ký điều tra
     * 
     * @param array $filters Các filter đã áp dụng
     * @return ForensicLog
     */
    public function logViewLogs(array $filters = []): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_LOGS,
            null,
            ['filters' => $filters]
        );
    }

    /**
     * Lấy logs theo user
     * 
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsByUser(int $userId, int $limit = 50)
    {
        return ForensicLog::where('user_id', $userId)
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy logs theo action
     * 
     * @param string $action
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsByAction(string $action, int $limit = 50)
    {
        return ForensicLog::action($action)
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy logs theo role
     * 
     * @param string $role
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsByRole(string $role, int $limit = 50)
    {
        return ForensicLog::role($role)
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy tất cả logs gần đây
     * 
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getRecentLogs(int $limit = 100)
    {
        return ForensicLog::with('user')
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Log xem dashboard
     * 
     * @param string $dashboardType admin hoặc investigator
     * @return ForensicLog
     */
    public function logViewDashboard(string $dashboardType = 'admin'): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_DASHBOARD,
            null,
            ['dashboard_type' => $dashboardType]
        );
    }

    /**
     * Log xem danh sách email
     * 
     * @param array $filters Các filter đã áp dụng
     * @return ForensicLog
     */
    public function logViewEmailList(array $filters = []): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_EMAIL_LIST,
            null,
            ['filters' => $filters]
        );
    }

    /**
     * Log xem danh sách requests
     * 
     * @param array $filters Các filter đã áp dụng
     * @return ForensicLog
     */
    public function logViewRequests(array $filters = []): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_REQUESTS,
            null,
            ['filters' => $filters]
        );
    }

    /**
     * Log xem workflow documentation
     * 
     * @return ForensicLog
     */
    public function logViewWorkflow(): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_WORKFLOW,
            null,
            []
        );
    }

    /**
     * Log tạo báo cáo điều tra
     * 
     * @param int $reportId
     * @param int $emailId
     * @param string $title
     * @return ForensicLog
     */
    public function logCreateReport(int $reportId, int $emailId, string $title): ForensicLog
    {
        return $this->log(
            self::ACTION_CREATE_REPORT,
            (string)$reportId,
            [
                'email_id' => $emailId,
                'report_title' => $title,
            ]
        );
    }

    /**
     * Log cập nhật báo cáo điều tra
     * 
     * @param int $reportId
     * @param string $status
     * @param array $changes
     * @return ForensicLog
     */
    public function logUpdateReport(int $reportId, string $status, array $changes = []): ForensicLog
    {
        return $this->log(
            self::ACTION_UPDATE_REPORT,
            (string)$reportId,
            [
                'status' => $status,
                'changes' => $changes,
            ]
        );
    }

    /**
     * Log xem báo cáo điều tra
     * 
     * @param int $reportId
     * @return ForensicLog
     */
    public function logViewReport(int $reportId): ForensicLog
    {
        return $this->log(
            self::ACTION_VIEW_REPORT,
            (string)$reportId
        );
    }

    /**
     * Log trích xuất logs từ email
     * 
     * @param int $emailId
     * @param int $extractedLogsCount
     * @return ForensicLog
     */
    public function logExtractEmailLogs(int $emailId, int $extractedLogsCount): ForensicLog
    {
        return $this->log(
            self::ACTION_EXTRACT_EMAIL_LOGS,
            (string)$emailId,
            ['extracted_logs_count' => $extractedLogsCount]
        );
    }

    /**
     * Lấy logs theo target_id (email_id, request_id, report_id)
     * 
     * @param string $targetId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsByTarget(string $targetId, int $limit = 50)
    {
        return ForensicLog::where('target_id', $targetId)
            ->with('user')
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy logs theo khoảng thời gian
     * 
     * @param string $startDate
     * @param string $endDate
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getLogsByDateRange(string $startDate, string $endDate, int $limit = 100)
    {
        return ForensicLog::with('user')
            ->whereBetween('created_at', [$startDate, $endDate])
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Lấy thống kê logs theo action
     * 
     * @param string|null $startDate
     * @param string|null $endDate
     * @return \Illuminate\Support\Collection
     */
    public function getLogStatsByAction(?string $startDate = null, ?string $endDate = null)
    {
        $query = ForensicLog::selectRaw('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'desc');

        if ($startDate && $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        return $query->get();
    }

    /**
     * Lấy mô tả tiếng Việt cho action
     * 
     * @param string $action
     * @return string
     */
    public static function getActionDescription(string $action): string
    {
        $descriptions = [
            'login' => 'Đăng nhập hệ thống',
            'logout' => 'Đăng xuất khỏi hệ thống',
            'view_dashboard' => 'Xem trang tổng quan',
            'view_email_list' => 'Xem danh sách email',
            'view_requests' => 'Xem danh sách yêu cầu',
            'view_workflow' => 'Xem tài liệu quy trình',
            'view_upload_form' => 'Xem form tải lên',
            'upload_email' => 'Tải lên email mới',
            'view_email' => 'Xem chi tiết email',
            'request_decrypt' => 'Yêu cầu giải mã',
            'approve_request' => 'Phê duyệt yêu cầu',
            'reject_request' => 'Từ chối yêu cầu',
            'decrypt_email' => 'Giải mã email',
            'create_report' => 'Tạo báo cáo điều tra',
            'update_report' => 'Cập nhật báo cáo',
            'view_report' => 'Xem báo cáo',
            'extract_email_logs' => 'Trích xuất logs',
            'view_logs' => 'Xem nhật ký hệ thống',
            'generate_keys' => 'Tạo cặp khóa RSA',
            'view_reports' => 'Xem danh sách báo cáo',
            'review_report' => 'Duyệt báo cáo',
        ];

        return $descriptions[$action] ?? $action;
    }

    /**
     * Lấy icon cho action
     * 
     * @param string $action
     * @return string
     */
    public static function getActionIcon(string $action): string
    {
        $icons = [
            'login' => 'fas fa-sign-in-alt',
            'logout' => 'fas fa-sign-out-alt',
            'view_dashboard' => 'fas fa-tachometer-alt',
            'view_email_list' => 'fas fa-list',
            'view_requests' => 'fas fa-key',
            'view_workflow' => 'fas fa-sitemap',
            'view_upload_form' => 'fas fa-cloud-upload-alt',
            'upload_email' => 'fas fa-upload',
            'view_email' => 'fas fa-eye',
            'request_decrypt' => 'fas fa-unlock-alt',
            'approve_request' => 'fas fa-check-circle',
            'reject_request' => 'fas fa-times-circle',
            'decrypt_email' => 'fas fa-unlock',
            'create_report' => 'fas fa-file-alt',
            'update_report' => 'fas fa-edit',
            'view_report' => 'fas fa-file-alt',
            'extract_email_logs' => 'fas fa-download',
            'view_logs' => 'fas fa-history',
            'generate_keys' => 'fas fa-key',
            'view_reports' => 'fas fa-file-alt',
            'review_report' => 'fas fa-gavel',
        ];

        return $icons[$action] ?? 'fas fa-circle';
    }
}

