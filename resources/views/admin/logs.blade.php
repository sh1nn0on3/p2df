@extends('layouts.app')

@section('title', 'Forensic Logs')

@push('styles')
<style>
    .log-row:hover {
        background: #f8f9fa;
        transition: background-color 0.2s ease;
    }
    .action-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        padding: 4px 8px;
        border-radius: 4px;
    }
    .log-stats {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 25px;
        border-radius: 8px;
        margin-bottom: 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .log-stats h4 {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 5px;
    }
    .log-stats p {
        margin: 0;
        opacity: 0.9;
        font-size: 0.9rem;
    }
    .action-description {
        font-size: 0.9rem;
        color: #6c757d;
    }
    .search-box {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    .search-box:focus-within {
        border-color: #667eea;
    }
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    .filter-badge {
        display: inline-block;
        margin: 2px;
        padding: 4px 8px;
        background: #667eea;
        color: white;
        border-radius: 12px;
        font-size: 0.8rem;
    }
    .filter-badge i {
        cursor: pointer;
        margin-left: 5px;
    }
    .table-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 20px;
    }
    .btn-search {
        background: #667eea;
        border-color: #667eea;
        color: white;
    }
    .btn-search:hover {
        background: #5568d3;
        border-color: #5568d3;
    }
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }
    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-history"></i> Forensic Audit Logs</h2>
        <p class="text-muted">Complete audit trail of all system activities</p>
        <hr>
    </div>
</div>

<!-- Statistics Overview -->
<div class="log-stats">
    <div class="row">
        <div class="col-md-3">
            <h4>{{ $logs->count() }}</h4>
            <p class="mb-0"><i class="fas fa-list"></i> Total Logs Displayed</p>
        </div>
        <div class="col-md-3">
            <h4>{{ $logs->where('role', 'admin')->count() }}</h4>
            <p class="mb-0"><i class="fas fa-user-shield"></i> Admin Actions</p>
        </div>
        <div class="col-md-3">
            <h4>{{ $logs->where('role', 'investigator')->count() }}</h4>
            <p class="mb-0"><i class="fas fa-user-search"></i> Investigator Actions</p>
        </div>
        <div class="col-md-3">
            <h4>{{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}</h4>
            <p class="mb-0"><i class="fas fa-clock"></i> Today's Activity</p>
        </div>
    </div>
</div>

<!-- Search and Filter Box -->
<div class="search-box">
    <h5 class="mb-3"><i class="fas fa-search"></i> Search & Filter</h5>
    
    <!-- Active Filters -->
    @if(request('search') || request('role') || request('action'))
        <div class="mb-3">
            <small class="text-muted d-block mb-2">Active Filters:</small>
            @if(request('search'))
                <span class="filter-badge">
                    Search: "{{ request('search') }}"
                    <i class="fas fa-times" onclick="clearFilter('search')"></i>
                </span>
            @endif
            @if(request('role'))
                <span class="filter-badge">
                    Role: {{ ucfirst(request('role')) }}
                    <i class="fas fa-times" onclick="clearFilter('role')"></i>
                </span>
            @endif
            @if(request('action'))
                <span class="filter-badge">
                    Action: {{ request('action') }}
                    <i class="fas fa-times" onclick="clearFilter('action')"></i>
                </span>
            @endif
        </div>
    @endif
    
    <form method="GET" action="{{ route('admin.logs') }}">
        <div class="row">
            <!-- Search Input -->
            <div class="col-md-4 mb-3">
                <label for="search" class="form-label"><i class="fas fa-search"></i> Search</label>
                <input type="text" 
                       class="form-control" 
                       id="search" 
                       name="search" 
                       placeholder="Search by user, IP, action, target..." 
                       value="{{ request('search') }}">
                <small class="form-text text-muted">Search across users, actions, IP addresses, and targets</small>
            </div>
            
            <!-- Role Filter -->
            <div class="col-md-3 mb-3">
                <label for="role" class="form-label"><i class="fas fa-user-tag"></i> Role</label>
                <select name="role" id="role" class="form-control">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="investigator" {{ request('role') === 'investigator' ? 'selected' : '' }}>Investigator</option>
                </select>
            </div>
            
            <!-- Action Filter -->
            <div class="col-md-5 mb-3">
                <label for="action" class="form-label"><i class="fas fa-tasks"></i> Action</label>
                <select name="action" id="action" class="form-control">
                    <option value="">All Actions</option>
                    <option value="login" {{ request('action') === 'login' ? 'selected' : '' }}>Login</option>
                    <option value="logout" {{ request('action') === 'logout' ? 'selected' : '' }}>Logout</option>
                    <option value="view_dashboard" {{ request('action') === 'view_dashboard' ? 'selected' : '' }}>View Dashboard</option>
                    <option value="view_email_list" {{ request('action') === 'view_email_list' ? 'selected' : '' }}>View Email List</option>
                    <option value="view_requests" {{ request('action') === 'view_requests' ? 'selected' : '' }}>View Requests</option>
                    <option value="view_workflow" {{ request('action') === 'view_workflow' ? 'selected' : '' }}>View Workflow</option>
                    <option value="upload_email" {{ request('action') === 'upload_email' ? 'selected' : '' }}>Upload Email</option>
                    <option value="view_email" {{ request('action') === 'view_email' ? 'selected' : '' }}>View Email Details</option>
                    <option value="request_decrypt" {{ request('action') === 'request_decrypt' ? 'selected' : '' }}>Request Decrypt</option>
                    <option value="approve_request" {{ request('action') === 'approve_request' ? 'selected' : '' }}>Approve Request</option>
                    <option value="reject_request" {{ request('action') === 'reject_request' ? 'selected' : '' }}>Reject Request</option>
                    <option value="decrypt_email" {{ request('action') === 'decrypt_email' ? 'selected' : '' }}>Decrypt Email</option>
                    <option value="create_report" {{ request('action') === 'create_report' ? 'selected' : '' }}>Create Report</option>
                    <option value="update_report" {{ request('action') === 'update_report' ? 'selected' : '' }}>Update Report</option>
                    <option value="view_report" {{ request('action') === 'view_report' ? 'selected' : '' }}>View Report</option>
                    <option value="extract_email_logs" {{ request('action') === 'extract_email_logs' ? 'selected' : '' }}>Extract Email Logs</option>
                </select>
            </div>
        </div>
        
        <!-- Action Buttons -->
        <div class="row">
            <div class="col-md-12">
                <button type="submit" class="btn btn-search">
                    <i class="fas fa-filter"></i> Apply Filters
                </button>
                @if(request('role') || request('action') || request('search'))
                    <a href="{{ route('admin.logs') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Clear All
                    </a>
                @endif
                <span class="ml-2 text-muted">
                    <i class="fas fa-info-circle"></i> Showing {{ $logs->count() }} log{{ $logs->count() !== 1 ? 's' : '' }}
                </span>
            </div>
        </div>
    </form>
</div>

<!-- Logs Table -->
<div class="card table-card">
    <div class="card-header">
        <h5 class="mb-0"><i class="fas fa-table"></i> Audit Trail</h5>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th width="60">ID</th>
                    <th>User</th>
                    <th width="100">Role</th>
                    <th>Action</th>
                    <th>Target</th>
                    <th>IP Address</th>
                    <th>Timestamp</th>
                    <th width="80">Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr class="log-row">
                        <td><strong>#{{ $log->id }}</strong></td>
                        <td>
                            <i class="fas fa-user-circle"></i> {{ $log->user->name }}
                        </td>
                        <td>
                            <span class="badge badge-{{ $log->role === 'admin' ? 'danger' : 'info' }}">
                                {{ strtoupper($log->role) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge badge-dark action-badge">
                                <i class="{{ \App\Services\LogService::getActionIcon($log->action) }}"></i>
                                {{ $log->action }}
                            </span>
                            <br>
                            <small class="action-description">
                                {{ \App\Services\LogService::getActionDescription($log->action) }}
                            </small>
                        </td>
                        <td>
                            @if($log->target_id)
                                <span class="badge badge-secondary">#{{ $log->target_id }}</span>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <small><i class="fas fa-network-wired"></i> {{ $log->ip_address }}</small>
                        </td>
                        <td>
                            <small>
                                {{ $log->created_at->format('Y-m-d H:i:s') }}
                                <br>
                                <span class="text-muted">{{ $log->created_at->diffForHumans() }}</span>
                            </small>
                        </td>
                        <td>
                            @if($log->details && is_array($log->details))
                                <button type="button" 
                                        class="btn btn-sm btn-outline-info" 
                                        data-toggle="modal" 
                                        data-target="#logDetailsModal{{ $log->id }}">
                                    <i class="fas fa-info-circle"></i>
                                </button>
                                
                                <!-- Modal -->
                                <div class="modal fade" id="logDetailsModal{{ $log->id }}" tabindex="-1" role="dialog">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Log Details #{{ $log->id }}</h5>
                                                <button type="button" class="close" data-dismiss="modal">
                                                    <span>&times;</span>
                                                </button>
                                            </div>
                                            <div class="modal-body">
                                                <pre class="bg-light p-3 rounded">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8">
                            <div class="empty-state">
                                <i class="fas fa-search"></i>
                                <h5>No logs found</h5>
                                <p>No logs match your search criteria. Try adjusting your filters.</p>
                                @if(request('search') || request('role') || request('action'))
                                    <a href="{{ route('admin.logs') }}" class="btn btn-primary">
                                        <i class="fas fa-times"></i> Clear All Filters
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        <small class="text-muted">
            <i class="fas fa-info-circle"></i> 
            Logs are stored permanently for forensic audit purposes. 
            Displaying latest 100 entries. Total system logs may be higher.
        </small>
    </div>
</div>

@if($logs->count() >= 100)
    <div class="alert alert-info mt-3">
        <i class="fas fa-info-circle"></i> 
        <strong>Note:</strong> Showing latest 100 logs. Use filters to narrow down results or contact system administrator for full log export.
    </div>
@endif

@push('scripts')
<script>
function clearFilter(filterType) {
    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete(filterType);
    window.location.href = '{{ route("admin.logs") }}?' + urlParams.toString();
}

// Auto-submit search on Enter key
document.getElementById('search')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        this.closest('form').submit();
    }
});
</script>
@endpush
@endsection
