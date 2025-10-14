@extends('layouts.app')

@section('title', 'View Email')

@push('styles')
<style>
    .email-detail-card {
        border-radius: 25px;
        overflow: hidden;
    }

    .email-meta-row {
        padding: 15px 20px;
        border-bottom: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }

    .email-meta-row:hover {
        background: rgba(102, 126, 234, 0.03);
    }

    .email-meta-row:last-child {
        border-bottom: none;
    }

    .encrypted-preview {
        background: linear-gradient(135deg, #f5f7fa 0%, #e0e7ff 100%);
        border-radius: 15px;
        padding: 20px;
        border-left: 5px solid #667eea;
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
        color: #666;
        max-height: 250px;
        overflow-y: auto;
        position: relative;
    }

    .encrypted-preview::after {
        content: '🔒 ENCRYPTED';
        position: absolute;
        top: 10px;
        right: 15px;
        background: rgba(220, 53, 69, 0.9);
        color: white;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.7rem;
        font-weight: bold;
    }

    .request-form-card {
        border-radius: 20px;
        border: 3px dashed #667eea;
        background: linear-gradient(135deg, rgba(102, 126, 234, 0.03) 0%, rgba(118, 75, 162, 0.03) 100%);
    }

    .status-card {
        border-radius: 20px;
        overflow: hidden;
    }

    .status-card.pending {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
    }

    .status-card.approved {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
    }

    .status-card.rejected {
        background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
        color: white;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-envelope-open"></i> Email Details</h2>
            <p class="text-muted mb-0">Encrypted email #{{ $email->id }}</p>
        </div>
        <a href="{{ route('investigator.emails') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Back to List
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card email-detail-card">
            <div class="card-header bg-dark">
                <i class="fas fa-info-circle"></i> Email Metadata
            </div>
            <div class="card-body p-0">
                <div class="email-meta-row">
                    <strong><i class="fas fa-hashtag"></i> Email ID:</strong>
                    <span class="float-right badge badge-primary">#{{ $email->id }}</span>
                </div>
                <div class="email-meta-row">
                    <strong><i class="fas fa-user"></i> From:</strong>
                    <span class="float-right">{{ $email->from }}</span>
                </div>
                <div class="email-meta-row">
                    <strong><i class="fas fa-user"></i> To:</strong>
                    <span class="float-right">{{ $email->to }}</span>
                </div>
                <div class="email-meta-row">
                    <strong><i class="fas fa-envelope"></i> Subject:</strong>
                    <span class="float-right font-weight-bold">{{ $email->subject }}</span>
                </div>
                <div class="email-meta-row">
                    <strong><i class="fas fa-calendar"></i> Date:</strong>
                    <span class="float-right">{{ $email->created_at->format('Y-m-d H:i:s') }}</span>
                </div>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-danger">
                <i class="fas fa-lock"></i> Encrypted Content (Cannot Read)
            </div>
            <div class="card-body">
                <div class="encrypted-preview">
                    {{ Str::limit($email->body_encrypted, 800) }}
                </div>
                <div class="alert alert-danger mt-3 mb-0" style="border-radius: 15px; border: none;">
                    <i class="fas fa-exclamation-triangle"></i> 
                    <strong>Privacy Protected:</strong> Content is AES-256 encrypted. Submit a decryption request to access plaintext.
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        @if($existingRequest)
            <div class="card status-card {{ $existingRequest->status }}">
                <div class="card-body text-center" style="padding: 30px;">
                    @if($existingRequest->isPending())
                        <i class="fas fa-clock fa-3x mb-3"></i>
                        <h5>Request Pending</h5>
                        <p class="mb-3">Your decryption request is awaiting admin approval</p>
                    @elseif($existingRequest->isApproved())
                        <i class="fas fa-check-circle fa-3x mb-3"></i>
                        <h5>Request Approved!</h5>
                        <p class="mb-3">You can now decrypt this email</p>
                        <a href="{{ route('investigator.requests.decrypt', $existingRequest->id) }}" class="btn btn-light btn-lg btn-block">
                            <i class="fas fa-unlock"></i> Decrypt Now
                        </a>
                        <a href="{{ route('investigator.reports.create', $email->id) }}" class="btn btn-outline-light btn-lg btn-block">
                            <i class="fas fa-file-medical"></i> Create Report
                        </a>
                    @else
                        <i class="fas fa-times-circle fa-3x mb-3"></i>
                        <h5>Request Rejected</h5>
                        <p class="mb-0">Your request was not approved</p>
                    @endif

                    <hr style="border-color: rgba(255,255,255,0.3);">

                    <div class="text-left">
                        <p class="mb-1"><strong>Reason:</strong></p>
                        <p class="small">{{ $existingRequest->reason }}</p>
                        <p class="mb-0 small">
                            <i class="fas fa-clock"></i> Requested: {{ $existingRequest->created_at->format('M d, Y H:i') }}
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="card request-form-card">
                <div class="card-header" style="background: var(--primary-gradient); color: white; border: none;">
                    <i class="fas fa-key"></i> Request Decryption Access
                </div>
                <div class="card-body">
                    <div class="alert alert-info" style="border-radius: 12px; background: rgba(23, 162, 184, 0.1); border: none;">
                        <i class="fas fa-info-circle"></i> <strong>Privacy Notice:</strong> You need admin approval to decrypt this email.
                    </div>

                    <form method="POST" action="{{ route('investigator.emails.request', $email->id) }}">
                        @csrf
                        <div class="form-group">
                            <label for="reason" class="font-weight-bold">
                                <i class="fas fa-comment-dots"></i> Justification for Decryption *
                            </label>
                            <textarea name="reason" 
                                      id="reason" 
                                      class="form-control @error('reason') is-invalid @enderror" 
                                      rows="5" 
                                      required 
                                      placeholder="Explain why you need to decrypt this email. Include:&#10;- Investigation purpose&#10;- Suspected violations&#10;- Expected evidence&#10;&#10;(Minimum 10 characters required)">{{ old('reason') }}</textarea>
                            @error('reason')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="fas fa-shield-alt"></i> Your request will be reviewed by admin with full audit trail
                            </small>
                        </div>
                        
                        <button type="submit" class="btn btn-warning btn-block btn-lg">
                            <i class="fas fa-paper-plane"></i> Submit Decryption Request
                        </button>
                    </form>
                </div>
            </div>
        @endif

        <!-- Email Actions -->
        @if(!$existingRequest)
            <div class="card mt-3">
                <div class="card-header">
                    <i class="fas fa-info-circle"></i> Need Help?
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold mb-3">What happens next?</h6>
                    <ol class="small mb-0">
                        <li class="mb-2">You submit decryption request with reason</li>
                        <li class="mb-2">Admin reviews your justification</li>
                        <li class="mb-2">If approved, AES key is re-encrypted for you</li>
                        <li class="mb-2">You decrypt email using your private key</li>
                        <li class="mb-0">All actions are logged</li>
                    </ol>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
