@extends('layouts.app')

@section('title', 'Email List')


@section('content')
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="fas fa-database"></i> Encrypted Email Database</h2>
            <p class="text-muted mb-0">Total: <strong>{{ $emails->total() }}</strong> encrypted emails</p>
        </div>
        <a href="{{ route('admin.upload') }}" class="btn btn-success">
            <i class="fas fa-plus-circle"></i> Upload New
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <form method="GET" class="mb-3">
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

        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th><i class="fas fa-user"></i> From</th>
                        <th><i class="fas fa-user"></i> To</th>
                        <th><i class="fas fa-envelope"></i> Subject</th>
                        <th><i class="fas fa-lock"></i> Encrypted Content</th>
                        <th><i class="fas fa-calendar"></i> Date</th>
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
                                <span class="badge badge-secondary">
                                    <i class="fas fa-lock"></i> Encrypted
                                </span>
                                <small class="text-muted">{{ Str::limit($email->body_encrypted, 30) }}</small>
                            </td>
                            <td>
                                <small>
                                    {{ $email->created_at->format('M d, Y') }}
                                    <br>
                                    <span class="text-muted">{{ $email->created_at->format('H:i') }}</span>
                                </small>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-5">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <br>
                                No emails found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    @if($emails->hasPages())
        <div class="card-footer">
            <div class="d-flex justify-content-center">
                {{ $emails->links() }}
            </div>
        </div>
    @endif
</div>
@endsection
