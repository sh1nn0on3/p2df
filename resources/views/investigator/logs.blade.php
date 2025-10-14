@extends('layouts.app')

@section('title', 'My Activity Logs')

@push('styles')
<style>
    .log-card {
        border-left: 4px solid #007bff;
        transition: all 0.3s ease;
    }
    .log-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateX(5px);
    }
    .log-card.action-login {
        border-left-color: #28a745;
    }
    .log-card.action-logout {
        border-left-color: #6c757d;
    }
    .log-card.action-request_decrypt {
        border-left-color: #ffc107;
    }
    .log-card.action-decrypt_email {
        border-left-color: #17a2b8;
    }
    .log-card.action-create_report {
        border-left-color: #e83e8c;
    }
    .action-badge {
        font-family: 'Courier New', monospace;
        font-size: 0.85rem;
    }
    .timeline-container {
        position: relative;
        padding-left: 40px;
    }
    .timeline-container:before {
        content: '';
        position: absolute;
        left: 15px;
        top: 0;
        bottom: 0;
        width: 3px;
        background: linear-gradient(to bottom, #007bff, #6c757d);
    }
    .timeline-item {
        position: relative;
        margin-bottom: 20px;
    }
    .timeline-dot {
        position: absolute;
        left: -32px;
        top: 15px;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #007bff;
        border: 3px solid #fff;
        box-shadow: 0 0 0 2px #007bff;
    }
    .log-details {
        background: #f8f9fa;
        border-radius: 4px;
        padding: 8px;
        margin-top: 8px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-history"></i> My Activity Logs</h2>
        <p class="text-muted">Audit trail of your recent activities</p>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-3">
        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-filter"></i> Quick Filters
            </div>
            <div class="list-group list-group-flush">
                <a href="?action=login" class="list-group-item list-group-item-action">
                    <i class="fas fa-sign-in-alt text-success"></i> Login
                </a>
                <a href="?action=view_email" class="list-group-item list-group-item-action">
                    <i class="fas fa-eye text-info"></i> View Email
                </a>
                <a href="?action=request_decrypt" class="list-group-item list-group-item-action">
                    <i class="fas fa-key text-warning"></i> Request Decrypt
                </a>
                <a href="?action=decrypt_email" class="list-group-item list-group-item-action">
                    <i class="fas fa-unlock text-primary"></i> Decrypt Email
                </a>
                <a href="?action=create_report" class="list-group-item list-group-item-action">
                    <i class="fas fa-file-alt text-danger"></i> Create Report
                </a>
                <a href="{{ route('investigator.logs') }}" class="list-group-item list-group-item-action">
                    <i class="fas fa-list"></i> All Actions
                </a>
            </div>
        </div>

        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Statistics
            </div>
            <div class="card-body">
                <p class="mb-1"><strong>Total Logs:</strong> {{ $logs->count() }}</p>
                <p class="mb-1"><strong>Today:</strong> {{ $logs->where('created_at', '>=', now()->startOfDay())->count() }}</p>
                <p class="mb-0"><strong>This Week:</strong> {{ $logs->where('created_at', '>=', now()->startOfWeek())->count() }}</p>
            </div>
        </div>
    </div>

    <div class="col-md-9">
        <div class="timeline-container">
            @forelse($logs as $log)
                <div class="timeline-item">
                    <div class="timeline-dot"></div>
                    <div class="card log-card action-{{ $log->action }}">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-8">
                                    <h6 class="card-title mb-2">
                                        <span class="badge badge-primary action-badge">
                                            {{ $log->action }}
                                        </span>
                                        @if($log->target_id)
                                            <span class="badge badge-secondary">#{{ $log->target_id }}</span>
                                        @endif
                                    </h6>
                                    
                                    <p class="card-text small mb-1">
                                        <i class="fas fa-user text-muted"></i> <strong>{{ $log->user->name }}</strong>
                                    </p>
                                    
                                    <p class="card-text small mb-0">
                                        <i class="fas fa-clock text-muted"></i> 
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        <small class="text-muted">({{ $log->created_at->diffForHumans() }})</small>
                                    </p>
                                </div>
                                
                                <div class="col-md-4 text-right">
                                    <p class="card-text small mb-1">
                                        <i class="fas fa-network-wired text-muted"></i> 
                                        {{ $log->ip_address }}
                                    </p>
                                    
                                    @if($log->action === 'decrypt_email' || $log->action === 'view_email')
                                        <span class="badge badge-info"><i class="fas fa-envelope"></i> Email</span>
                                    @elseif($log->action === 'create_report' || $log->action === 'update_report')
                                        <span class="badge badge-danger"><i class="fas fa-file-alt"></i> Report</span>
                                    @elseif($log->action === 'request_decrypt')
                                        <span class="badge badge-warning"><i class="fas fa-key"></i> Request</span>
                                    @endif
                                </div>
                            </div>
                            
                            @if($log->details && is_array($log->details) && count($log->details) > 0)
                                <div class="log-details mt-2">
                                    <strong class="small">Details:</strong>
                                    <ul class="list-unstyled small mb-0 mt-1">
                                        @foreach($log->details as $key => $value)
                                            <li>
                                                <i class="fas fa-angle-right text-primary"></i> 
                                                <strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                                @if(is_string($value))
                                                    {{ Str::limit($value, 100) }}
                                                @else
                                                    {{ json_encode($value) }}
                                                @endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> No activity logs found.
                </div>
            @endforelse
        </div>

        @if($logs->count() >= 50)
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle"></i> 
                Showing latest 50 logs. Older logs are archived for system performance.
            </div>
        @endif
    </div>
</div>
@endsection
