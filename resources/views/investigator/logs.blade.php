@extends('layouts.app')

@section('title', 'Nhật Ký Hoạt Động')

@push('styles')
<style>
    .log-item {
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
        margin-bottom: 15px;
    }
    
    .log-item:hover {
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transform: translateX(3px);
    }
    
    .log-item.action-login { border-left-color: #28a745; }
    .log-item.action-logout { border-left-color: #6c757d; }
    .log-item.action-request_decrypt { border-left-color: #ffc107; }
    .log-item.action-decrypt_email { border-left-color: #17a2b8; }
    .log-item.action-create_report { border-left-color: #e83e8c; }
    .log-item.action-view_email { border-left-color: #6f42c1; }
    .log-item.action-update_report { border-left-color: #fd7e14; }
    
    .action-icon {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 16px;
    }
    
    .action-icon.login { background: #28a745; }
    .action-icon.logout { background: #6c757d; }
    .action-icon.request_decrypt { background: #ffc107; }
    .action-icon.decrypt_email { background: #17a2b8; }
    .action-icon.create_report { background: #e83e8c; }
    .action-icon.view_email { background: #6f42c1; }
    .action-icon.update_report { background: #fd7e14; }
    
    .log-details {
        background: #f8f9fa;
        border-radius: 6px;
        padding: 10px;
        margin-top: 10px;
        font-size: 13px;
    }
    
    .filter-card {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
    
    .stats-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h2><i class="fas fa-history"></i> Nhật Ký Hoạt Động</h2>
    <p class="text-muted">Theo dõi các hoạt động gần đây của bạn</p>
</div>

<!-- Statistics -->
<div class="stats-card">
    <div class="row text-center">
        <div class="col-md-3">
            <h3>{{ $logs->count() }}</h3>
            <p class="mb-0">Tổng Logs</p>
        </div>
        <div class="col-md-3">
            <h3>{{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}</h3>
            <p class="mb-0">Hôm Nay</p>
        </div>
        <div class="col-md-3">
            <h3>{{ $logs->where('created_at', '>=', now()->startOfWeek())->count() }}</h3>
            <p class="mb-0">Tuần Này</p>
        </div>
        <div class="col-md-3">
            <h3>{{ $logs->where('action', 'request_decrypt')->count() }}</h3>
            <p class="mb-0">Yêu Cầu Giải Mã</p>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="filter-card">
    <h6 class="mb-3"><i class="fas fa-filter"></i> Bộ Lọc Nhanh</h6>
    <div class="row">
        <div class="col-md-2">
            <a href="{{ route('investigator.logs') }}" class="btn btn-outline-primary btn-sm btn-block">
                <i class="fas fa-list"></i> Tất Cả
            </a>
        </div>
        <div class="col-md-2">
            <a href="?action=login" class="btn btn-outline-success btn-sm btn-block">
                <i class="fas fa-sign-in-alt"></i> Đăng Nhập
            </a>
        </div>
        <div class="col-md-2">
            <a href="?action=view_email" class="btn btn-outline-info btn-sm btn-block">
                <i class="fas fa-eye"></i> Xem Email
            </a>
        </div>
        <div class="col-md-2">
            <a href="?action=request_decrypt" class="btn btn-outline-warning btn-sm btn-block">
                <i class="fas fa-key"></i> Yêu Cầu Giải Mã
            </a>
        </div>
        <div class="col-md-2">
            <a href="?action=decrypt_email" class="btn btn-outline-primary btn-sm btn-block">
                <i class="fas fa-unlock"></i> Giải Mã
            </a>
        </div>
        <div class="col-md-2">
            <a href="?action=create_report" class="btn btn-outline-danger btn-sm btn-block">
                <i class="fas fa-file-alt"></i> Tạo Báo Cáo
            </a>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">

        <!-- Logs List -->
        @forelse($logs as $log)
            <div class="card log-item action-{{ $log->action }}">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="action-icon {{ $log->action }}">
                                @switch($log->action)
                                    @case('login')
                                        <i class="fas fa-sign-in-alt"></i>
                                        @break
                                    @case('logout')
                                        <i class="fas fa-sign-out-alt"></i>
                                        @break
                                    @case('view_email')
                                        <i class="fas fa-eye"></i>
                                        @break
                                    @case('request_decrypt')
                                        <i class="fas fa-key"></i>
                                        @break
                                    @case('decrypt_email')
                                        <i class="fas fa-unlock"></i>
                                        @break
                                    @case('create_report')
                                        <i class="fas fa-file-alt"></i>
                                        @break
                                    @case('update_report')
                                        <i class="fas fa-edit"></i>
                                        @break
                                    @default
                                        <i class="fas fa-circle"></i>
                                @endswitch
                            </div>
                        </div>
                        
                        <div class="col">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="mb-1">
                                        @switch($log->action)
                                            @case('login')
                                                Đăng nhập hệ thống
                                                @break
                                            @case('logout')
                                                Đăng xuất khỏi hệ thống
                                                @break
                                            @case('view_email')
                                                Xem chi tiết email
                                                @break
                                            @case('request_decrypt')
                                                Yêu cầu giải mã email
                                                @break
                                            @case('decrypt_email')
                                                Giải mã email
                                                @break
                                            @case('create_report')
                                                Tạo báo cáo điều tra
                                                @break
                                            @case('update_report')
                                                Cập nhật báo cáo
                                                @break
                                            @default
                                                {{ $log->action }}
                                        @endswitch
                                        
                                        @if($log->target_id)
                                            <span class="badge badge-secondary ml-2">#{{ $log->target_id }}</span>
                                        @endif
                                    </h6>
                                    
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-clock"></i> 
                                        {{ $log->created_at->format('d/m/Y H:i:s') }}
                                        <span class="ml-2">({{ $log->created_at->diffForHumans() }})</span>
                                    </p>
                                </div>
                                
                                <div class="text-right">
                                    <p class="text-muted small mb-1">
                                        <i class="fas fa-network-wired"></i> {{ $log->ip_address }}
                                    </p>
                                    
                                    @if($log->action === 'decrypt_email' || $log->action === 'view_email')
                                        <span class="badge badge-info"><i class="fas fa-envelope"></i> Email</span>
                                    @elseif($log->action === 'create_report' || $log->action === 'update_report')
                                        <span class="badge badge-danger"><i class="fas fa-file-alt"></i> Báo Cáo</span>
                                    @elseif($log->action === 'request_decrypt')
                                        <span class="badge badge-warning"><i class="fas fa-key"></i> Yêu Cầu</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($log->details && is_array($log->details) && count($log->details) > 0)
                                <div class="log-details">
                                    <strong>Chi tiết:</strong>
                                    <ul class="list-unstyled mb-0 mt-1">
                                        @foreach($log->details as $key => $value)
                                            <li>
                                                <i class="fas fa-angle-right text-primary"></i> 
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                @if(is_string($value))
                                                    {{ Str::limit($value, 100) }}
                                                @else
                                                    {{ json_encode($value) }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-history fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Không tìm thấy nhật ký hoạt động</h5>
                    <p class="text-muted">Bạn chưa có hoạt động nào được ghi lại.</p>
                </div>
            </div>
        @endforelse

        @if($logs->count() >= 50)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Hiển thị 50 logs gần nhất. Các logs cũ hơn được lưu trữ để tối ưu hiệu suất hệ thống.
            </div>
        @endif
    </div>
</div>
@endsection
