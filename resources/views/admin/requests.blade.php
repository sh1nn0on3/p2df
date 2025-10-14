@extends('layouts.app')

@section('title', 'Decryption Requests')


@section('content')
<div class="page-header">
    <h2><i class="fas fa-key"></i> Decryption Requests</h2>
    <p class="text-muted">Review and manage investigator access requests</p>
</div>

<!-- Filter Tabs -->
<div class="text-center mb-3">
    <a href="?status=pending" class="btn {{ $status === 'pending' ? 'btn-primary' : 'btn-outline-primary' }}">
        <i class="fas fa-clock"></i> Pending
    </a>
    <a href="?status=approved" class="btn {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
        <i class="fas fa-check"></i> Approved
    </a>
    <a href="?status=rejected" class="btn {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
        <i class="fas fa-times"></i> Rejected
    </a>
    <a href="?status=all" class="btn {{ $status === 'all' ? 'btn-secondary' : 'btn-outline-secondary' }}">
        <i class="fas fa-list"></i> All
    </a>
</div>

<!-- Requests List -->
@forelse($requests as $request)
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge badge-dark">#{{ $request->id }}</span>
                    <span class="ml-2">
                        @if($request->isPending())
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending Review</span>
                        @elseif($request->isApproved())
                            <span class="badge badge-success"><i class="fas fa-check"></i> Approved</span>
                        @else
                            <span class="badge badge-danger"><i class="fas fa-times"></i> Rejected</span>
                        @endif
                    </span>
                </div>
                <small class="text-muted">
                    {{ $request->created_at->format('M d, Y H:i') }}
                </small>
            </div>
        </div>

        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="d-flex align-items-center mb-3">
                        <i class="fas fa-user-secret fa-2x mr-3 text-info"></i>
                        <div>
                            <strong>{{ $request->investigator->name }}</strong>
                            <br>
                            <small class="text-muted">
                                {{ $request->investigator->email }}
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <h6 class="font-weight-bold">
                            <i class="fas fa-envelope-open-text"></i> Email Subject:
                        </h6>
                        <p class="mb-1"><strong>{{ $request->email->subject }}</strong></p>
                        <small class="text-muted">
                            <i class="fas fa-arrow-right"></i> {{ $request->email->from }} → {{ $request->email->to }}
                        </small>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="font-weight-bold mb-2">
                                <i class="fas fa-comment-alt"></i> Reason for Decryption:
                            </h6>
                            <p class="mb-0 small" style="white-space: pre-wrap;">{{ $request->reason }}</p>
                        </div>
                    </div>

                    @if($request->isPending())
                        <div class="text-center mt-3">
                            <form method="POST" action="{{ route('admin.requests.approve', $request->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-success mr-2" 
                                        onclick="return confirm('Approve this decryption request?\n\nThis will:\n1. Decrypt AES key with your private key\n2. Re-encrypt with investigator\'s public key\n3. Grant access to email content')">
                                    <i class="fas fa-check-circle"></i> Approve Request
                                </button>
                            </form>
                            <form method="POST" action="{{ route('admin.requests.reject', $request->id) }}" class="d-inline">
                                @csrf
                                <button type="submit" 
                                        class="btn btn-danger" 
                                        onclick="return confirm('Reject this request?')">
                                    <i class="fas fa-times-circle"></i> Reject
                                </button>
                            </form>
                        </div>
                    @elseif($request->isApproved())
                        <div class="text-center mt-3">
                            <div class="alert alert-success mb-0">
                                <i class="fas fa-check-circle"></i> <strong>Approved</strong>
                                <br>
                                <small>{{ $request->approved_at->format('M d, Y H:i') }}</small>
                            </div>
                        </div>
                    @else
                        <div class="text-center mt-3">
                            <div class="alert alert-danger mb-0">
                                <i class="fas fa-ban"></i> <strong>Rejected</strong>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-4x mb-3 text-muted"></i>
            <h5 class="text-muted">No {{ $status !== 'all' ? $status : '' }} requests found</h5>
            <p class="text-muted">Check other filter tabs or wait for new requests.</p>
        </div>
    </div>
@endforelse

@if($requests->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $requests->links() }}
    </div>
@endif
@endsection
