@extends('layouts.app')

@section('title', 'Quản lý Báo cáo Điều tra')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-file-alt"></i> Quản lý Báo cáo Điều tra</h2>
    <p class="text-muted">Xem xét và duyệt các báo cáo từ điều tra viên</p>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $reports->total() }}</h4>
                        <p class="mb-0">Tổng báo cáo</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $reports->where('status', 'completed')->count() }}</h4>
                        <p class="mb-0">Đã hoàn thành</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-check-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $reports->where('status', 'completed')->whereNull('admin_reviewed_at')->count() }}</h4>
                        <p class="mb-0">Chờ duyệt</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-clock fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h4>{{ $reports->where('admin_action', 'approved')->count() }}</h4>
                        <p class="mb-0">Đã phê duyệt</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-thumbs-up fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select name="status" id="status" class="form-select">
                    <option value="all" {{ $status === 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Bản nháp</option>
                    <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Đã hoàn thành</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="severity" class="form-label">Mức độ nghiêm trọng</label>
                <select name="severity" id="severity" class="form-select">
                    <option value="all" {{ $severity === 'all' ? 'selected' : '' }}>Tất cả</option>
                    <option value="low" {{ $severity === 'low' ? 'selected' : '' }}>Thấp</option>
                    <option value="medium" {{ $severity === 'medium' ? 'selected' : '' }}>Trung bình</option>
                    <option value="high" {{ $severity === 'high' ? 'selected' : '' }}>Cao</option>
                    <option value="critical" {{ $severity === 'critical' ? 'selected' : '' }}>Nghiêm trọng</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> Lọc
                    </button>
                    <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Xóa bộ lọc
                    </a>
                </div>
            </div>
            <div class="col-md-3">
                <label class="form-label">&nbsp;</label>
                <div>
                    <a href="{{ route('admin.reports.stats') }}" class="btn btn-info">
                        <i class="fas fa-chart-bar"></i> Thống kê
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Reports List -->
<div class="card">
    <div class="card-header">
        <h5 class="mb-0">
            <i class="fas fa-list"></i> Danh sách Báo cáo
            <span class="badge badge-primary ml-2">{{ $reports->total() }}</span>
        </h5>
    </div>
    <div class="card-body p-0">
        @if($reports->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ID</th>
                            <th>Tiêu đề</th>
                            <th>Điều tra viên</th>
                            <th>Email</th>
                            <th>Mức độ</th>
                            <th>Trạng thái</th>
                            <th>Admin duyệt</th>
                            <th>Ngày tạo</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($reports as $report)
                        <tr>
                            <td>
                                <span class="badge badge-secondary">#{{ $report->id }}</span>
                            </td>
                            <td>
                                <strong>{{ $report->title }}</strong>
                                @if($report->isDraft())
                                    <span class="badge badge-warning ml-1">Bản nháp</span>
                                @endif
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-user-circle text-primary mr-2"></i>
                                    <span>{{ $report->investigator->name }}</span>
                                </div>
                            </td>
                            <td>
                                <div class="text-truncate" style="max-width: 200px;" title="{{ $report->email->subject }}">
                                    <strong>{{ $report->email->from }}</strong><br>
                                    <small class="text-muted">{{ $report->email->subject }}</small>
                                </div>
                            </td>
                            <td>
                                <span class="badge badge-{{ $report->getSeverityBadgeColor() }}">
                                    {{ ucfirst($report->severity) }}
                                </span>
                            </td>
                            <td>
                                @if($report->isCompleted())
                                    <span class="badge badge-success">Hoàn thành</span>
                                @else
                                    <span class="badge badge-warning">Bản nháp</span>
                                @endif
                            </td>
                            <td>
                                @if($report->isReviewedByAdmin())
                                    <span class="badge badge-{{ $report->getAdminActionBadgeColor() }}">
                                        {{ $report->getAdminActionText() }}
                                    </span>
                                    <br>
                                    <small class="text-muted">
                                        {{ $report->admin_reviewed_at->format('d/m/Y H:i') }}
                                    </small>
                                @else
                                    @if($report->isCompleted())
                                        <span class="badge badge-warning">Chờ duyệt</span>
                                    @else
                                        <span class="badge badge-secondary">Chưa hoàn thành</span>
                                    @endif
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $report->created_at->format('d/m/Y H:i') }}</span>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    <a href="{{ route('admin.reports.view', $report->id) }}" 
                                       class="btn btn-sm btn-outline-primary" 
                                       title="Xem chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($report->isCompleted() && !$report->isReviewedByAdmin())
                                        <a href="{{ route('admin.reports.view', $report->id) }}" 
                                           class="btn btn-sm btn-warning" 
                                           title="Cần duyệt">
                                            <i class="fas fa-gavel"></i>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="card-footer">
                {{ $reports->appends(request()->query())->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Chưa có báo cáo nào</h5>
                <p class="text-muted">Các điều tra viên sẽ tạo báo cáo sau khi hoàn thành điều tra.</p>
            </div>
        @endif
    </div>
</div>

@push('styles')
<style>
    .table th {
        border-top: none;
        font-weight: 600;
        color: #495057;
    }
    
    .badge {
        font-size: 0.75em;
    }
    
    .btn-group .btn {
        margin-right: 2px;
    }
    
    .card-footer {
        background-color: #f8f9fa;
        border-top: 1px solid #dee2e6;
    }
</style>
@endpush
@endsection

