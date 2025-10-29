@extends('layouts.app')

@section('title', 'Thống kê Báo cáo Điều tra')

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-chart-bar"></i> Thống kê Báo cáo Điều tra</h2>
            <p class="text-muted mb-0">Phân tích và thống kê các báo cáo từ điều tra viên</p>
        </div>
        <div>
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>
</div>

<!-- Overview Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3>{{ $stats['total_reports'] }}</h3>
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
                        <h3>{{ $stats['completed_reports'] }}</h3>
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
                        <h3>{{ $stats['pending_review'] }}</h3>
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
                        <h3>{{ $stats['approved_reports'] }}</h3>
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

<!-- Review Statistics -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3>{{ $stats['rejected_reports'] }}</h3>
                        <p class="mb-0">Đã từ chối</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-times-circle fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3>{{ $stats['needs_revision'] }}</h3>
                        <p class="mb-0">Cần chỉnh sửa</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-edit fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h3>{{ $stats['total_reports'] - $stats['completed_reports'] }}</h3>
                        <p class="mb-0">Bản nháp</p>
                    </div>
                    <div class="align-self-center">
                        <i class="fas fa-file-alt fa-2x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Severity Distribution -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-pie"></i> Phân bố theo Mức độ Nghiêm trọng
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    @php
                        $severityLabels = [
                            'low' => 'Thấp',
                            'medium' => 'Trung bình', 
                            'high' => 'Cao',
                            'critical' => 'Nghiêm trọng'
                        ];
                        $severityColors = [
                            'low' => 'success',
                            'medium' => 'info',
                            'high' => 'warning', 
                            'critical' => 'danger'
                        ];
                    @endphp
                    
                    @foreach($severityLabels as $key => $label)
                        <div class="col-md-3 mb-3">
                            <div class="card border-{{ $severityColors[$key] }}">
                                <div class="card-body text-center">
                                    <h3 class="text-{{ $severityColors[$key] }}">
                                        {{ $severityStats[$key] ?? 0 }}
                                    </h3>
                                    <p class="mb-0">{{ $label }}</p>
                                    @if($stats['total_reports'] > 0)
                                        <small class="text-muted">
                                            {{ round(($severityStats[$key] ?? 0) / $stats['total_reports'] * 100, 1) }}%
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Completion Rate -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-chart-line"></i> Tỷ lệ Hoàn thành
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>Tỷ lệ hoàn thành báo cáo</h6>
                        @php
                            $completionRate = $stats['total_reports'] > 0 ? 
                                round($stats['completed_reports'] / $stats['total_reports'] * 100, 1) : 0;
                        @endphp
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: {{ $completionRate }}%"
                                 aria-valuenow="{{ $completionRate }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $completionRate }}%
                            </div>
                        </div>
                        <p class="text-muted">
                            {{ $stats['completed_reports'] }} / {{ $stats['total_reports'] }} báo cáo đã hoàn thành
                        </p>
                    </div>
                    <div class="col-md-6">
                        <h6>Tỷ lệ duyệt báo cáo</h6>
                        @php
                            $approvalRate = $stats['completed_reports'] > 0 ? 
                                round($stats['approved_reports'] / $stats['completed_reports'] * 100, 1) : 0;
                        @endphp
                        <div class="progress mb-3" style="height: 25px;">
                            <div class="progress-bar bg-info" 
                                 role="progressbar" 
                                 style="width: {{ $approvalRate }}%"
                                 aria-valuenow="{{ $approvalRate }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $approvalRate }}%
                            </div>
                        </div>
                        <p class="text-muted">
                            {{ $stats['approved_reports'] }} / {{ $stats['completed_reports'] }} báo cáo đã được phê duyệt
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="fas fa-history"></i> Tóm tắt Hoạt động
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-file-plus fa-2x text-primary mb-2"></i>
                            <h5>{{ $stats['total_reports'] }}</h5>
                            <p class="text-muted mb-0">Báo cáo được tạo</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                            <h5>{{ $stats['completed_reports'] }}</h5>
                            <p class="text-muted mb-0">Báo cáo hoàn thành</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="text-center p-3">
                            <i class="fas fa-gavel fa-2x text-warning mb-2"></i>
                            <h5>{{ $stats['pending_review'] }}</h5>
                            <p class="text-muted mb-0">Chờ duyệt</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .card-body h3 {
        font-size: 2.5rem;
        font-weight: 700;
    }
    
    .progress {
        border-radius: 10px;
    }
    
    .progress-bar {
        border-radius: 10px;
        font-weight: 600;
    }
    
    .card.border-* {
        border-width: 2px !important;
    }
</style>
@endpush
@endsection

