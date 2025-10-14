@extends('layouts.app')

@section('title', 'Forensic Logs')

@push('styles')
<style>
    .log-row:hover {
        background: #f8f9fa;
    }
    .action-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.75rem;
        padding: 3px 6px;
    }
    .log-stats {
        background: #343a40;
        color: white;
        padding: 20px;
        border-radius: 4px;
        margin-bottom: 20px;
    }
    .action-description {
        font-size: 0.9rem;
        color: #6c757d;
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
            <p class="mb-0">Total Logs Displayed</p>
        </div>
        <div class="col-md-3">
            <h4>{{ $logs->where('role', 'admin')->count() }}</h4>
            <p class="mb-0">Admin Actions</p>
        </div>
        <div class="col-md-3">
            <h4>{{ $logs->where('role', 'investigator')->count() }}</h4>
            <p class="mb-0">Investigator Actions</p>
        </div>
        <div class="col-md-3">
            <h4>{{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}</h4>
            <p class="mb-0">Today's Activity</p>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-row align-items-center">
            <div class="col-auto">
                <label class="sr-only" for="role">Role</label>
                <select name="role" id="role" class="form-control form-control-sm">
                    <option value="">All Roles</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                    <option value="investigator" {{ request('role') === 'investigator' ? 'selected' : '' }}>Investigator</option>
                </select>
            </div>
            <div class="col-auto">
                <label class="sr-only" for="action">Action</label>
                <select name="action" id="action" class="form-control form-control-sm">
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
            <div class="col-auto">
                <button type="submit" class="btn btn-sm btn-primary">
                    <i class="fas fa-filter"></i> Filter
                </button>
            </div>
            @if(request('role') || request('action'))
                <div class="col-auto">
                    <a href="{{ route('admin.logs') }}" class="btn btn-sm btn-secondary">
                        <i class="fas fa-times"></i> Clear
                    </a>
                </div>
            @endif
        </form>
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
                        <td colspan="8" class="text-center text-muted">No logs found.</td>
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
@endsection
