@extends('layouts.app')

@section('title', 'Decrypted Email')

@push('styles')
<style>
    .decrypted-header {
        background: #28a745;
        color: white;
        padding: 20px;
        border-radius: 8px 8px 0 0;
    }

    .plaintext-content {
        background: #ffffff;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 20px;
        min-height: 200px;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 14px;
        line-height: 1.6;
        font-family: 'Courier New', monospace;
        color: #333;
    }

    .verification-badge {
        display: inline-block;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
    }

    .verification-badge.valid {
        background: #28a745;
        color: white;
    }

    .verification-badge.invalid {
        background: #dc3545;
        color: white;
    }

    .decrypt-info-item {
        padding: 12px;
        background: #f8f9fa;
        border-radius: 6px;
        margin-bottom: 8px;
        border-left: 3px solid #007bff;
    }

    .security-notice {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        border-radius: 6px;
        padding: 15px;
        margin-bottom: 20px;
        color: #856404;
    }

    .btn-action {
        margin: 2px;
        font-size: 14px;
    }
</style>
@endpush

@section('content')
<div class="security-notice">
    <div class="d-flex align-items-center">
        <i class="fas fa-shield-alt fa-2x mr-3 text-warning"></i>
        <div>
            <strong>🔒 Security Notice:</strong> Email đã được giải mã. Xử lý thông tin nhạy cảm một cách có trách nhiệm. Mọi truy cập đều được ghi log.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="decrypted-header">
                <div style="position: relative; z-index: 1;">
                    <h4 class="mb-2"><i class="fas fa-unlock-alt"></i> Email Đã Được Giải Mã Thành Công</h4>
                    <p class="mb-0">Tiêu đề: <strong>{{ $email->subject }}</strong></p>
                </div>
            </div>

            <div class="card-body">
                <!-- Email Metadata -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">Người gửi</small>
                            <strong><i class="fas fa-user"></i> {{ $email->from }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">Người nhận</small>
                            <strong><i class="fas fa-user"></i> {{ $email->to }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">Ngày tạo</small>
                            <strong><i class="fas fa-calendar"></i> {{ $email->created_at->format('d/m/Y H:i:s') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">Email ID</small>
                            <strong><i class="fas fa-hashtag"></i> #{{ $email->id }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Decrypted Content -->
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-file-alt"></i> Nội Dung Email Đã Giải Mã
                </h6>
                <div class="plaintext-content">
                    {{ $bodyDecrypted }}
                </div>

                <!-- Hash Verification -->
                <div class="text-center mt-4">
                    @if($isValid)
                        <div class="verification-badge valid">
                            <i class="fas fa-check-circle fa-lg"></i> 
                            Hash Đã Xác Thực - Tính Toàn Vẹn Nội Dung Được Xác Nhận
                        </div>
                    @else
                        <div class="verification-badge invalid">
                            <i class="fas fa-exclamation-triangle fa-lg"></i> 
                            Xác Thực Hash Thất Bại - Phát Hiện Khả Năng Bị Can Thiệp
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer bg-light">
                <div class="d-flex justify-content-between">
                    <a href="{{ route('investigator.emails') }}" class="btn btn-outline-secondary btn-action">
                        <i class="fas fa-arrow-left"></i> Quay Lại
                    </a>
                    <a href="{{ route('investigator.requests') }}" class="btn btn-outline-info btn-action">
                        <i class="fas fa-list"></i> Yêu Cầu
                    </a>
                    <a href="{{ route('investigator.reports.create', $email->id) }}" class="btn btn-success btn-action">
                        <i class="fas fa-file-medical"></i> Tạo Báo Cáo
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Decryption Info -->
        <div class="card mb-3" style="background: var(--info-gradient); color: white;">
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-shield-alt"></i> Decryption Information
                </h6>
                
                <div class="mb-3">
                    <small class="d-block" style="opacity: 0.9;">Request ID</small>
                    <strong>#{{ $request->id }}</strong>
                </div>

                <div class="mb-3">
                    <small class="d-block" style="opacity: 0.9;">Approved At</small>
                    <strong>{{ $request->approved_at->format('M d, Y H:i') }}</strong>
                </div>

                <div class="mb-3">
                    <small class="d-block" style="opacity: 0.9;">Encryption Method</small>
                    <strong>AES-256-CBC</strong>
                </div>

                <div class="mb-0">
                    <small class="d-block" style="opacity: 0.9;">Integrity Check</small>
                    <strong>SHA-256 Hash</strong>
                </div>
            </div>
        </div>

        <!-- Privacy Notice -->
        <div class="card">
            <div class="card-header bg-warning text-dark">
                <i class="fas fa-shield-alt"></i> Privacy & Security
            </div>
            <div class="card-body">
                <p class="small mb-2">
                    <i class="fas fa-check text-success"></i> Decrypted using your private RSA key
                </p>
                <p class="small mb-2">
                    <i class="fas fa-check text-success"></i> AES key was re-encrypted by Admin
                </p>
                <p class="small mb-2">
                    <i class="fas fa-check text-success"></i> Content never stored as plaintext
                </p>
                <p class="small mb-0">
                    <i class="fas fa-check text-success"></i> All actions logged for audit
                </p>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-bolt"></i> Next Steps
            </div>
            <div class="card-body">
                <p class="small mb-2">After reviewing this email, you can:</p>
                <div class="d-grid gap-2">
                    <a href="{{ route('investigator.reports.create', $email->id) }}" class="btn btn-success btn-sm mb-2">
                        <i class="fas fa-file-medical"></i> Write Forensic Report
                    </a>
                    <a href="{{ route('investigator.emails') }}" class="btn btn-info btn-sm">
                        <i class="fas fa-search"></i> Investigate More Emails
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Auto-hide plaintext after 10 minutes for security
    setTimeout(function() {
        if (confirm('For security reasons, this decrypted content will be hidden after 10 minutes.\n\nWould you like to create a forensic report before it\'s hidden?')) {
            window.location.href = '{{ route('investigator.reports.create', $email->id) }}';
        }
    }, 600000); // 10 minutes
</script>
@endpush
