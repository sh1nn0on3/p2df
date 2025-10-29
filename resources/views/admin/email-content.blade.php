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
        background: #ffffff;
        border: none;
        border-radius: 0;
        padding: 30px;
        margin: 0;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        white-space: pre-wrap;
        word-wrap: break-word;
        word-break: break-word;
        line-height: 1.9;
        color: #333;
        font-size: 15px;
        min-height: 150px;
        width: 100%;
        overflow: visible;
    }
    
    .email-content-wrapper {
        position: relative;
        max-height: 800px;
        overflow-y: auto;
        border: 1px solid #e0e0e0;
        border-radius: 8px;
        background: #f8f9fa;
    }
    
    .email-content pre {
        background: #ffffff;
        padding: 15px;
        border-radius: 5px;
        border-left: 4px solid #007bff;
        margin: 10px 0;
        white-space: pre-wrap;
        word-wrap: break-word;
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
            <a href="{{ route('admin.emails') }}" class="btn btn-outline-secondary mr-2">
                <i class="fas fa-arrow-left"></i> Quay Lại Danh Sách
            </a>
            <a href="{{ route('admin.reports') }}" class="btn btn-outline-info">
                <i class="fas fa-file-alt"></i> Báo Cáo
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
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user"></i> Người gửi:</strong><br>
                            <span class="text-muted">{{ $email->from }}</span>
                        </div>
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user"></i> Người nhận:</strong><br>
                            <span class="text-muted">{{ $email->to }}</span>
                        </div>
                        @if($email->cc)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-users"></i> CC:</strong><br>
                            <span class="text-muted">{{ $email->cc }}</span>
                        </div>
                        @endif
                        @if($email->bcc)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-user-secret"></i> BCC:</strong><br>
                            <span class="text-muted">{{ $email->bcc }}</span>
                        </div>
                        @endif
                        <div class="col-md-12 mb-3">
                            <strong><i class="fas fa-subject"></i> Tiêu đề:</strong><br>
                            <span class="text-muted">{{ $email->subject }}</span>
                        </div>
                        @if($email->date_sent)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-paper-plane"></i> Ngày gửi:</strong><br>
                            <span class="text-muted">{{ $email->date_sent->format('d/m/Y H:i:s') }}</span>
                        </div>
                        @endif
                        @if($email->date_received)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-inbox"></i> Ngày nhận:</strong><br>
                            <span class="text-muted">{{ $email->date_received->format('d/m/Y H:i:s') }}</span>
                        </div>
                        @endif
                        @if($email->message_id)
                        <div class="col-md-12 mb-3">
                            <strong><i class="fas fa-fingerprint"></i> Message ID:</strong><br>
                            <span class="text-muted small">{{ $email->message_id }}</span>
                        </div>
                        @endif
                        @if($email->reply_to)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-reply"></i> Reply-To:</strong><br>
                            <span class="text-muted">{{ $email->reply_to }}</span>
                        </div>
                        @endif
                        @if($email->sender_ip)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-network-wired"></i> Sender IP:</strong><br>
                            <span class="text-muted">{{ $email->sender_ip }}</span>
                        </div>
                        @endif
                        @if($email->mailer)
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-mail-bulk"></i> Mailer/Client:</strong><br>
                            <span class="text-muted">{{ $email->mailer }}</span>
                        </div>
                        @endif
                        <div class="col-md-6 mb-3">
                            <strong><i class="fas fa-calendar"></i> Ngày tạo trong hệ thống:</strong><br>
                            <span class="text-muted">{{ $email->created_at->format('d/m/Y H:i:s') }}</span>
                        </div>
                    </div>
                </div>
                
                @if($email->headers && is_array($email->headers))
                <div class="mt-3">
                    <button class="btn btn-sm btn-outline-info" type="button" data-toggle="collapse" data-target="#headersCollapse">
                        <i class="fas fa-code"></i> Xem Email Headers
                    </button>
                    <div class="collapse mt-2" id="headersCollapse">
                        <div class="card card-body bg-dark text-light" style="font-family: monospace; font-size: 0.85em; max-height: 300px; overflow-y: auto;">
                            @foreach($email->headers as $key => $value)
                                <div><strong>{{ $key }}:</strong> {{ is_array($value) ? json_encode($value) : $value }}</div>
                            @endforeach
                        </div>
                    </div>
                </div>
                @endif
                
                @if($email->attachments_info && is_array($email->attachments_info) && count($email->attachments_info) > 0)
                <div class="mt-3">
                    <strong><i class="fas fa-paperclip"></i> Attachments:</strong>
                    <ul class="mb-0">
                        @foreach($email->attachments_info as $attachment)
                            <li>{{ is_array($attachment) ? ($attachment['name'] ?? json_encode($attachment)) : $attachment }}</li>
                        @endforeach
                    </ul>
                </div>
                @endif
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
                @if(empty($bodyDecrypted))
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i> 
                        <strong>Lỗi:</strong> Nội dung email không thể hiển thị được. 
                        Vui lòng kiểm tra lại email đã được mã hóa đúng cách chưa.
                    </div>
                @else
                    <!-- Debug Info -->
                    <div class="alert alert-info small mb-3" style="display: none;" id="debugInfo">
                        <strong>Debug Info:</strong><br>
                        Length: {{ strlen($bodyDecrypted) }} chars<br>
                        Lines: {{ substr_count($bodyDecrypted, "\n") + 1 }}<br>
                        First 50: {{ substr($bodyDecrypted, 0, 50) }}...<br>
                        Last 50: ...{{ substr($bodyDecrypted, -50) }}
                    </div>
                    
                    <div class="email-content-wrapper">
                        <pre class="email-content" id="emailContentDisplay">{{ $bodyDecrypted }}</pre>
                    </div>
                    <div class="mt-3 d-flex justify-content-between align-items-center">
                        <div class="text-muted small">
                            <i class="fas fa-info-circle"></i> 
                            <strong>Độ dài:</strong> {{ number_format(strlen($bodyDecrypted)) }} ký tự
                            @if(strlen($bodyDecrypted) > 0)
                                | <strong>Số dòng:</strong> {{ substr_count($bodyDecrypted, "\n") + 1 }}
                            @endif
                            <button type="button" class="btn btn-sm btn-link p-0 ml-2" onclick="document.getElementById('debugInfo').style.display = document.getElementById('debugInfo').style.display === 'none' ? 'block' : 'none';">
                                <i class="fas fa-bug"></i> Debug
                            </button>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="copyEmailContent()">
                            <i class="fas fa-copy"></i> Copy
                        </button>
                    </div>
                @endif
                
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
    (function() {
        var emailsUrl = '{{ route('admin.emails') }}';
        setTimeout(function() {
            if (confirm('Vì lý do bảo mật, nội dung email sẽ bị ẩn sau 5 phút.\\n\\nBạn có muốn quay lại trang danh sách email không?')) {
                window.location.href = emailsUrl;
            }
        }, 300000); // 5 minutes
    })();
    
    // Kiểm tra nội dung có được hiển thị không
    document.addEventListener('DOMContentLoaded', function() {
        var contentDiv = document.getElementById('emailContentDisplay');
        if (contentDiv) {
            var contentText = contentDiv.textContent || contentDiv.innerText || '';
            if (contentText.trim().length === 0) {
                console.warn('Email content appears to be empty');
            } else {
                console.log('Email content loaded successfully, length:', contentText.length);
                console.log('First 200 chars:', contentText.substring(0, 200));
                console.log('Last 200 chars:', contentText.substring(Math.max(0, contentText.length - 200)));
            }
        }
    });
    
    // Copy email content
    function copyEmailContent() {
        var contentDiv = document.getElementById('emailContentDisplay');
        if (contentDiv) {
            var contentText = contentDiv.textContent || contentDiv.innerText || '';
            var textarea = document.createElement('textarea');
            textarea.value = contentText;
            textarea.style.position = 'fixed';
            textarea.style.opacity = '0';
            document.body.appendChild(textarea);
            textarea.select();
            try {
                document.execCommand('copy');
                alert('Đã copy nội dung email vào clipboard!');
            } catch (err) {
                alert('Lỗi khi copy. Vui lòng chọn và copy thủ công.');
            }
            document.body.removeChild(textarea);
        }
    }
</script>
@endpush
@endsection

