@extends('layouts.app')

@section('title', 'Nội Dung Email - Admin')

@push('styles')
<style>
    .admin-warning {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: white;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: none;
    }
    
    .email-content {
        background: #f8f9fa;
        border: 2px solid #dee2e6;
        border-radius: 10px;
        padding: 25px;
        font-family: 'Courier New', monospace;
        white-space: pre-wrap;
        word-wrap: break-word;
        line-height: 1.6;
        max-height: 500px;
        overflow-y: auto;
    }
    
    .verification-badge {
        display: inline-block;
        padding: 10px 20px;
        border-radius: 25px;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(0,0,0,0.1);
    }
    
    .verification-badge.valid {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }
    
    .verification-badge.invalid {
        background: linear-gradient(135deg, #dc3545 0%, #fd7e14 100%);
        color: white;
    }
    
    .email-info {
        background: #e9ecef;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-envelope-open"></i> Nội Dung Email - Admin</h2>
            <p class="text-muted mb-0">Xem nội dung email để đối chiếu với báo cáo</p>
        </div>
        <div>
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left"></i> Quay Lại Báo Cáo
            </a>
        </div>
    </div>
</div>

<!-- Admin Warning -->
<div class="admin-warning">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
        <div>
            <strong>⚠️ CẢNH BÁO ADMIN:</strong> Bạn đang truy cập nội dung email nhạy cảm. Hành động này được ghi log và chỉ nên thực hiện khi cần thiết để đối chiếu với báo cáo điều tra.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Email Information -->
        <div class="card mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Thông Tin Email
                </h5>
            </div>
            <div class="card-body">
                <div class="email-info">
                    <div class="row">
                        <div class="col-md-6">
                            <strong><i class="fas fa-user"></i> Người gửi:</strong><br>
                            <span class="text-muted">{{ $email->from }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-user"></i> Người nhận:</strong><br>
                            <span class="text-muted">{{ $email->to }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-subject"></i> Tiêu đề:</strong><br>
                            <span class="text-muted">{{ $email->subject }}</span>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar"></i> Ngày tạo:</strong><br>
                            <span class="text-muted">{{ $email->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Content -->
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0">
                    <i class="fas fa-file-alt"></i> Nội Dung Email Đã Giải Mã
                </h5>
            </div>
            <div class="card-body">
                <div class="email-content">
                    {{ $bodyDecrypted }}
                </div>
                
                <!-- Hash Verification -->
                <div class="text-center mt-4">
                    @if($isValid)
                        <div class="verification-badge valid">
                            <i class="fas fa-check-circle fa-lg"></i> 
                            Hash Đã Xác Thực - Nội Dung Không Bị Thay Đổi
                        </div>
                    @else
                        <div class="verification-badge invalid">
                            <i class="fas fa-exclamation-triangle fa-lg"></i> 
                            Xác Thực Hash Thất Bại - Nội Dung Có Thể Đã Bị Thay Đổi
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Security Notice -->
        <div class="card mb-4">
            <div class="card-header bg-warning text-dark">
                <h6 class="mb-0">
                    <i class="fas fa-shield-alt"></i> Lưu Ý Bảo Mật
                </h6>
            </div>
            <div class="card-body">
                <ul class="list-unstyled mb-0">
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Nội dung được giải mã bằng khóa riêng của Admin
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Mọi truy cập đều được ghi log
                    </li>
                    <li class="mb-2">
                        <i class="fas fa-check text-success"></i> 
                        Chỉ sử dụng để đối chiếu với báo cáo
                    </li>
                    <li class="mb-0">
                        <i class="fas fa-check text-success"></i> 
                        Không lưu trữ nội dung dạng plaintext
                    </li>
                </ul>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card">
            <div class="card-header">
                <h6 class="mb-0">
                    <i class="fas fa-bolt"></i> Thao Tác Nhanh
                </h6>
            </div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="{{ route('admin.reports') }}" class="btn btn-primary">
                        <i class="fas fa-file-alt"></i> Xem Báo Cáo
                    </a>
                    <a href="{{ route('admin.emails') }}" class="btn btn-info">
                        <i class="fas fa-envelope"></i> Danh Sách Email
                    </a>
                    <a href="{{ route('admin.logs') }}" class="btn btn-warning">
                        <i class="fas fa-history"></i> Xem Logs
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Auto-hide content after 5 minutes for security
    setTimeout(function() {
        if (confirm('Vì lý do bảo mật, nội dung email sẽ bị ẩn sau 5 phút.\n\nBạn có muốn quay lại trang báo cáo không?')) {
            window.location.href = '{{ route('admin.reports') }}';
        }
    }, 300000); // 5 minutes
</script>
@endpush
@endsection

