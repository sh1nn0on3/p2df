@extends('layouts.app')

@section('title', 'Email List')

@push('styles')
<style>
    .page-header {
        background: white;
        border: 1px solid #dee2e6;
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .page-header h2 {
        margin: 0;
        font-size: 1.5rem;
        color: #333;
    }
    .stats-bar {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .search-box {
        background: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 15px;
        margin-bottom: 20px;
    }
    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
    }
    .table-card {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }
    .table-card .card-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        padding: 12px 15px;
    }
    .table-hover tbody tr:hover {
        background: #f8f9fa;
    }
    .empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 3rem;
        margin-bottom: 15px;
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-database"></i> Encrypted Email Database</h2>
            <p class="text-muted mb-0">
                @if(request('search'))
                    Found: <strong>{{ $emails->count() }}</strong> results
                @else
                    Total: <strong>{{ $stats['total'] }}</strong> encrypted emails
                @endif
            </p>
        </div>
        <a href="{{ route('admin.upload') }}" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Upload New
        </a>
    </div>
</div>

<!-- Stats Bar -->
<div class="stats-bar">
    <div class="row text-center">
        <div class="col-md-3">
            <strong class="text-primary">{{ $stats['total'] }}</strong>
            <br>
            <small class="text-muted">Total Emails</small>
        </div>
        <div class="col-md-3">
            <strong class="text-success">{{ $stats['today'] }}</strong>
            <br>
            <small class="text-muted">Today</small>
        </div>
        <div class="col-md-3">
            <strong class="text-info">{{ $stats['this_week'] }}</strong>
            <br>
            <small class="text-muted">This Week</small>
        </div>
        <div class="col-md-3">
            <strong class="text-warning">{{ $stats['this_month'] }}</strong>
            <br>
            <small class="text-muted">This Month</small>
        </div>
    </div>
</div>

<!-- Search Box -->
<div class="search-box">
    <form method="GET" action="{{ route('admin.emails') }}">
        <div class="input-group">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Search by sender, recipient, or subject..." 
                   value="{{ request('search') }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Search
                </button>
                @if(request('search'))
                    <a href="{{ route('admin.emails') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i>
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Email Table -->
<div class="card table-card">
    <div class="card-header">
        <strong><i class="fas fa-table"></i> Email Records</strong>
    </div>
    <div class="card-body p-0">

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Subject</th>
                        <th>Date Sent</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($emails as $email)
                        <tr>
                            <td><span class="badge badge-primary">#{{ $email->id }}</span></td>
                            <td><small>{{ $email->from }}</small></td>
                            <td><small>{{ $email->to }}</small></td>
                            <td><strong>{{ $email->subject }}</strong></td>
                            <td>
                                @if($email->date_sent)
                                    <small>{{ $email->date_sent->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $email->date_sent->format('H:i') }}</small>
                                @else
                                    <small class="text-muted">{{ $email->created_at->format('M d, Y') }}</small>
                                    <br>
                                    <small class="text-muted">{{ $email->created_at->format('H:i') }}</small>
                                @endif
                            </td>
                            <td>
                                <span class="badge badge-success">
                                    <i class="fas fa-lock"></i> Encrypted
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('admin.emails.content', $email->id) }}" 
                                   class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <br>
                                @if(request('search'))
                                    No emails found matching your search.
                                @else
                                    No emails found.
                                @endif
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Auto-submit search on Enter key
document.querySelector('input[name="search"]')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        this.closest('form').submit();
    }
});
</script>
@endpush
@endsection
