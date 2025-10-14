@extends('layouts.app')

@section('title', 'Email List')


@section('content')
<div class="page-header">
    <h2><i class="fas fa-database"></i> Encrypted Email Database</h2>
    <p class="text-muted">Browse available emails - Request decryption to view content</p>
</div>

<div class="card mb-3">
    <div class="card-body">
        <form method="GET">
            <div class="input-group">
                <input type="text" 
                       name="search" 
                       class="form-control" 
                       placeholder="Search emails by sender, recipient, or subject..." 
                       value="{{ request('search') }}">
                <div class="input-group-append">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Search
                    </button>
                    @if(request('search'))
                        <a href="{{ route('investigator.emails') }}" class="btn btn-secondary">
                            Clear
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Email Cards -->
@forelse($emails as $email)
    <div class="card mb-3">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="badge badge-primary">#{{ $email->id }}</span>
                    <span class="badge badge-secondary ml-2">
                        <i class="fas fa-lock"></i> Encrypted
                    </span>
                </div>
                <small class="text-muted">
                    {{ $email->created_at->format('M d, Y H:i') }}
                </small>
            </div>
        </div>

        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h5 class="mb-3">
                        <i class="fas fa-envelope"></i> {{ $email->subject }}
                    </h5>
                    
                    <div class="mb-2">
                        <i class="fas fa-user"></i> <strong>From:</strong> {{ $email->from }}
                    </div>
                    
                    <div class="mb-3">
                        <i class="fas fa-user"></i> <strong>To:</strong> {{ $email->to }}
                    </div>

                    <div class="alert alert-warning">
                        <i class="fas fa-lock"></i> <strong>Content is encrypted</strong> - Request decryption to view plaintext
                    </div>
                </div>

                <div class="col-md-4 text-center">
                    <a href="{{ route('investigator.emails.view', $email->id) }}" class="btn btn-info btn-block">
                        <i class="fas fa-eye"></i> View Details
                    </a>
                </div>
            </div>
        </div>
    </div>
@empty
    <div class="card">
        <div class="card-body text-center py-5">
            <i class="fas fa-inbox fa-4x mb-3 text-muted"></i>
            <h5 class="text-muted">No emails found</h5>
            <p class="text-muted">Try different search terms or check back later.</p>
        </div>
    </div>
@endforelse

@if($emails->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $emails->links() }}
    </div>
@endif
@endsection
