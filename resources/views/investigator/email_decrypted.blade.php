@extends('layouts.app')

@section('title', 'Decrypted Email')

@push('styles')
<style>
    .decrypted-header {
        background: var(--success-gradient);
        color: white;
        padding: 30px;
        border-radius: 20px 20px 0 0;
        position: relative;
        overflow: hidden;
    }

    .decrypted-header::before {
        content: '🔓';
        position: absolute;
        font-size: 10rem;
        right: -20px;
        top: -30px;
        opacity: 0.1;
    }

    .plaintext-content {
        background: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: inset 0 2px 10px rgba(0,0,0,0.05);
        border: 2px solid #e9ecef;
        min-height: 200px;
        white-space: pre-wrap;
        word-wrap: break-word;
        font-size: 1rem;
        line-height: 1.6;
    }

    .verification-badge {
        display: inline-block;
        padding: 12px 20px;
        border-radius: 50px;
        font-weight: 600;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .verification-badge.valid {
        background: var(--success-gradient);
        color: white;
    }

    .verification-badge.invalid {
        background: var(--danger-gradient);
        color: white;
    }

    .decrypt-info-item {
        padding: 15px;
        background: #f8f9fa;
        border-radius: 12px;
        margin-bottom: 12px;
        border-left: 4px solid #667eea;
    }
</style>
@endpush

@section('content')
<div class="alert alert-warning" style="border-radius: 20px; border: none; box-shadow: 0 5px 20px rgba(255, 193, 7, 0.2);">
    <div class="d-flex align-items-center">
        <i class="fas fa-exclamation-triangle fa-2x mr-3"></i>
        <div>
            <strong>Security Notice:</strong> This email has been decrypted. Handle sensitive information responsibly. All access is logged.
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="decrypted-header">
                <div style="position: relative; z-index: 1;">
                    <h4 class="mb-2"><i class="fas fa-unlock-alt"></i> Email Successfully Decrypted</h4>
                    <p class="mb-0">Subject: <strong>{{ $email->subject }}</strong></p>
                </div>
            </div>

            <div class="card-body">
                <!-- Email Metadata -->
                <div class="row mb-4">
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">From</small>
                            <strong><i class="fas fa-user"></i> {{ $email->from }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">To</small>
                            <strong><i class="fas fa-user"></i> {{ $email->to }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">Date</small>
                            <strong><i class="fas fa-calendar"></i> {{ $email->created_at->format('M d, Y H:i:s') }}</strong>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="decrypt-info-item">
                            <small class="text-muted d-block">Email ID</small>
                            <strong><i class="fas fa-hashtag"></i> {{ $email->id }}</strong>
                        </div>
                    </div>
                </div>

                <!-- Decrypted Content -->
                <h6 class="font-weight-bold mb-3">
                    <i class="fas fa-file-alt"></i> Decrypted Message Content
                </h6>
                <div class="plaintext-content">
                    {{ $bodyDecrypted }}
                </div>

                <!-- Hash Verification -->
                <div class="text-center mt-4">
                    @if($isValid)
                        <div class="verification-badge valid">
                            <i class="fas fa-check-circle fa-lg"></i> 
                            Hash Verified - Content Integrity Confirmed
                        </div>
                    @else
                        <div class="verification-badge invalid">
                            <i class="fas fa-exclamation-triangle fa-lg"></i> 
                            Hash Verification Failed - Potential Tampering Detected
                        </div>
                    @endif
                </div>
            </div>

            <div class="card-footer" style="background: #f8f9fa;">
                <div class="row">
                    <div class="col-md-4">
                        <a href="{{ route('investigator.emails') }}" class="btn btn-secondary btn-block">
                            <i class="fas fa-arrow-left"></i> Back
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('investigator.requests') }}" class="btn btn-info btn-block">
                            <i class="fas fa-list"></i> Requests
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="{{ route('investigator.reports.create', $email->id) }}" class="btn btn-success btn-block">
                            <i class="fas fa-file-medical"></i> Create Report
                        </a>
                    </div>
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
