@extends('layouts.app')

@section('title', 'Chi tiết Báo cáo Điều tra')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-file-alt"></i> Chi tiết Báo cáo Điều tra</h2>
            <p class="text-muted mb-0">Xem xét và duyệt báo cáo từ điều tra viên</p>
        </div>
        <div>
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<!-- Report Status Banner -->
<div class="row mb-4">
    <div class="col-12">
        @if($report->isCompleted())
            @if($report->isReviewedByAdmin())
                <div class="alert alert-{{ $report->getAdminActionBadgeColor() }} alert-dismissible fade show">
                    <i class="fas fa-{{ $report->admin_action === 'approved' ? 'check-circle' : ($report->admin_action === 'rejected' ? 'times-circle' : 'exclamation-triangle') }}"></i>
                    <strong>Báo cáo đã được {{ $report->getAdminActionText() }}</strong>
                    @if($report->admin_notes)
                        <br><small>{{ $report->admin_notes }}</small>
                    @endif
                    <small class="float-right">
                        Duyệt bởi: {{ $report->adminReviewer->name ?? 'N/A' }} 
                        lúc {{ $report->admin_reviewed_at->format('d/m/Y H:i') }}
                    </small>
                </div>
            @else
                <div class="alert alert-warning alert-dismissible fade show">
                    <i class="fas fa-clock"></i>
                    <strong>Báo cáo đã hoàn thành - Chờ duyệt</strong>
                    <br><small>Báo cáo này cần được admin xem xét và duyệt.</small>
                </div>
            @endif
        @else
            <div class="alert alert-info alert-dismissible fade show">
                <i class="fas fa-edit"></i>
                <strong>Báo cáo đang ở trạng thái bản nháp</strong>
                <br><small>Điều tra viên vẫn đang chỉnh sửa báo cáo này.</small>
            </div>
        @endif
    </div>
</div>

<div class="row">
    <!-- Report Information -->
    <div class="col-lg-8">
        <!-- Basic Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Thông tin Báo cáo
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-tag text-primary"></i> Tiêu đề</h6>
                        <p class="text-muted">{{ $report->title }}</p>
                        
                        <h6><i class="fas fa-exclamation-triangle text-warning"></i> Mức độ nghiêm trọng</h6>
                        <span class="badge badge-{{ $report->getSeverityBadgeColor() }} badge-lg">
                            {{ ucfirst($report->severity) }}
                        </span>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-user text-info"></i> Điều tra viên</h6>
                        <p class="text-muted">{{ $report->investigator->name }}</p>
                        
                        <h6><i class="fas fa-calendar text-success"></i> Ngày tạo</h6>
                        <p class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</p>
                        
                        @if($report->completed_at)
                            <h6><i class="fas fa-check-circle text-success"></i> Ngày hoàn thành</h6>
                            <p class="text-muted">{{ $report->completed_at->format('d/m/Y H:i') }}</p>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Information -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-envelope"></i> Thông tin Email
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6><i class="fas fa-user text-primary"></i> Người gửi</h6>
                        <p class="text-muted">{{ $report->email->from }}</p>
                        
                        <h6><i class="fas fa-user-friends text-info"></i> Người nhận</h6>
                        <p class="text-muted">{{ $report->email->to }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-subject text-warning"></i> Tiêu đề</h6>
                        <p class="text-muted">{{ $report->email->subject }}</p>
                        
                        <h6><i class="fas fa-calendar text-success"></i> Ngày tạo email</h6>
                        <p class="text-muted">{{ $report->email->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Report Content -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Nội dung Báo cáo
                </h5>
            </div>
            <div class="card-body">
                <!-- Findings -->
                <div class="mb-4">
                    <h6><i class="fas fa-search text-primary"></i> Phát hiện (Findings)</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($report->findings)) !!}
                    </div>
                </div>

                <!-- Analysis -->
                <div class="mb-4">
                    <h6><i class="fas fa-microscope text-info"></i> Phân tích (Analysis)</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($report->analysis)) !!}
                    </div>
                </div>

                <!-- Recommendations -->
                <div class="mb-4">
                    <h6><i class="fas fa-lightbulb text-warning"></i> Khuyến nghị (Recommendations)</h6>
                    <div class="border rounded p-3 bg-light">
                        {!! nl2br(e($report->recommendations)) !!}
                    </div>
                </div>

                <!-- Related Logs -->
                @if($report->related_logs && count($report->related_logs) > 0)
                <div class="mb-4">
                    <h6><i class="fas fa-list text-secondary"></i> Nhật ký liên quan</h6>
                    <div class="border rounded p-3 bg-light">
                        <ul class="list-unstyled mb-0">
                            @foreach($report->related_logs as $log)
                                <li><i class="fas fa-circle text-primary mr-2"></i>{{ $log }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Admin Review Panel -->
    <div class="col-lg-4">
        @if($report->isCompleted() && !$report->isReviewedByAdmin())
            <!-- Review Form -->
            <div class="card mb-4">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-gavel"></i> Duyệt Báo cáo
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.reports.review', $report->id) }}" method="POST">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="action" class="form-label">Quyết định</label>
                            <select name="action" id="action" class="form-select" required>
                                <option value="">-- Chọn quyết định --</option>
                                <option value="approved">✅ Phê duyệt</option>
                                <option value="rejected">❌ Từ chối</option>
                                <option value="needs_revision">⚠️ Yêu cầu chỉnh sửa</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="admin_notes" class="form-label">Ghi chú của Admin</label>
                            <textarea name="admin_notes" id="admin_notes" 
                                    class="form-control" rows="4" 
                                    placeholder="Nhập ghi chú về quyết định duyệt..."></textarea>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-gavel"></i> Duyệt Báo cáo
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        @elseif($report->isReviewedByAdmin())
            <!-- Review Result -->
            <div class="card mb-4">
                <div class="card-header bg-{{ $report->getAdminActionBadgeColor() }} text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-check-circle"></i> Kết quả Duyệt
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>Quyết định:</strong>
                        <span class="badge badge-{{ $report->getAdminActionBadgeColor() }} ml-2">
                            {{ $report->getAdminActionText() }}
                        </span>
                    </div>
                    
                    @if($report->admin_notes)
                    <div class="mb-3">
                        <strong>Ghi chú:</strong>
                        <div class="border rounded p-2 mt-1 bg-light">
                            {{ $report->admin_notes }}
                        </div>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <strong>Duyệt bởi:</strong>
                        <p class="text-muted mb-1">{{ $report->adminReviewer->name ?? 'N/A' }}</p>
                        <small class="text-muted">
                            {{ $report->admin_reviewed_at->format('d/m/Y H:i') }}
                        </small>
                    </div>
                </div>
            </div>
        @endif

        <!-- Report Statistics -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-bar"></i> Thống kê
                </h5>
            </div>
            <div class="card-body">
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-primary">{{ strlen($report->findings) }}</h4>
                        <small class="text-muted">Ký tự Findings</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-info">{{ strlen($report->analysis) }}</h4>
                        <small class="text-muted">Ký tự Analysis</small>
                    </div>
                </div>
                <hr>
                <div class="row text-center">
                    <div class="col-6">
                        <h4 class="text-warning">{{ strlen($report->recommendations) }}</h4>
                        <small class="text-muted">Ký tự Recommendations</small>
                    </div>
                    <div class="col-6">
                        <h4 class="text-secondary">{{ count($report->related_logs ?? []) }}</h4>
                        <small class="text-muted">Logs liên quan</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-bolt"></i> Thao tác nhanh
                </h5>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.emails.content', $report->email_id) }}" class="btn btn-outline-primary">
                        <i class="fas fa-envelope-open"></i> Đọc Nội Dung Email
                    </a>
                    <a href="{{ route('admin.emails') }}" class="btn btn-outline-info">
                        <i class="fas fa-envelope"></i> Danh Sách Email
                    </a>
                    <a href="{{ route('admin.logs') }}" class="btn btn-outline-warning">
                        <i class="fas fa-history"></i> Xem Logs
                    </a>
                    <a href="{{ route('admin.requests') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-key"></i> Yêu Cầu Giải Mã
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .badge-lg {
        font-size: 0.9em;
        padding: 0.5em 0.75em;
    }
    
    .card-header h5 {
        font-weight: 600;
    }
    
    .bg-light {
        background-color: #f8f9fa !important;
    }
    
    .text-truncate {
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
</style>
@endpush
@endsection
