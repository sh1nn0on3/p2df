@extends('layouts.app')

@section('title', 'My Requests')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-tasks"></i> My Decryption Requests</h2>
    <p class="text-muted">Track the status of your access requests</p>
</div>

<!-- Filter Tabs -->
<div class="text-center mb-3">
    <a href="?status=all" class="btn {{ $status === 'all' ? 'btn-primary' : 'btn-outline-secondary' }}">
        <i class="fas fa-list"></i> All
    </a>
    <a href="?status=pending" class="btn {{ $status === 'pending' ? 'btn-warning' : 'btn-outline-warning' }}">
        <i class="fas fa-clock"></i> Pending
    </a>
    <a href="?status=approved" class="btn {{ $status === 'approved' ? 'btn-success' : 'btn-outline-success' }}">
        <i class="fas fa-check"></i> Approved
    </a>
    <a href="?status=rejected" class="btn {{ $status === 'rejected' ? 'btn-danger' : 'btn-outline-danger' }}">
        <i class="fas fa-times"></i> Rejected
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
                            <span class="badge badge-warning"><i class="fas fa-clock"></i> Pending</span>
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
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge badge-dark">Request #{{ $request->id }}</span>
                        @if($request->isPending())
                            <span class="badge badge-warning ml-2">
                                <i class="fas fa-hourglass-half"></i> Awaiting Approval
                            </span>
                        @elseif($request->isApproved())
                            <span class="badge badge-success ml-2">
                                <i class="fas fa-check-circle"></i> Approved
                            </span>
                        @else
                            <span class="badge badge-danger ml-2">
                                <i class="fas fa-ban"></i> Rejected
                            </span>
                        @endif
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-clock"></i> {{ $request->created_at->diffForHumans() }}
                    </small>
                </div>

                <div class="email-preview">
                    <h6 class="font-weight-bold mb-2">
                        <i class="fas fa-envelope"></i> {{ $request->email->subject }}
                    </h6>
                    <small class="text-muted">
                        <i class="fas fa-arrow-right"></i> 
                        {{ $request->email->from }} → {{ $request->email->to }}
                    </small>
                </div>

                <div class="mb-3">
                    <strong class="small d-block mb-1">Your Justification:</strong>
                    <p class="small mb-0 text-muted" style="white-space: pre-wrap;">{{ Str::limit($request->reason, 150) }}</p>
                </div>

                <div class="row align-items-center">
                    <div class="col-md-6">
                        <small class="text-muted">
                            <i class="fas fa-calendar"></i> Submitted: {{ $request->created_at->format('M d, Y H:i') }}
                        </small>
                    </div>
                    <div class="col-md-6 text-right">
                        @if($request->isApproved())
                            <a href="{{ route('investigator.requests.decrypt', $request->id) }}" class="btn btn-success">
                                <i class="fas fa-unlock"></i> Decrypt Email
                            </a>
                        @elseif($request->isPending())
                            <span class="badge badge-warning badge-pill px-3 py-2">
                                <i class="fas fa-clock"></i> Under Review
                            </span>
                        @else
                            <span class="badge badge-danger badge-pill px-3 py-2">
                                <i class="fas fa-times"></i> Access Denied
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="card">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-4x mb-3" style="opacity: 0.3;"></i>
                <h5 class="text-muted">No {{ $status !== 'all' ? $status : '' }} requests found</h5>
                <p class="text-muted">You haven't submitted any decryption requests yet.</p>
                <a href="{{ route('investigator.emails') }}" class="btn btn-primary">
                    <i class="fas fa-search"></i> Browse Emails
                </a>
            </div>
        </div>
    @endforelse
</div>

@if($requests->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $requests->links() }}
    </div>
@endif
@endsection
