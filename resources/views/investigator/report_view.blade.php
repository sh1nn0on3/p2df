@extends('layouts.app')

@section('title', 'Forensic Report')

@push('styles')
<style>
    .report-section {
        margin-bottom: 30px;
    }
    .report-meta {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 5px;
        margin-bottom: 20px;
    }
    .log-timeline {
        position: relative;
        padding-left: 30px;
    }
    .log-timeline:before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #dee2e6;
    }
    .log-timeline-item {
        position: relative;
        padding: 15px 0;
    }
    .log-timeline-item:before {
        content: '';
        position: absolute;
        left: -26px;
        top: 20px;
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #007bff;
        border: 2px solid #fff;
    }
    .log-timeline-item.admin:before {
        background: #dc3545;
    }
    .severity-critical { border-left: 5px solid #dc3545; }
    .severity-high { border-left: 5px solid #fd7e14; }
    .severity-medium { border-left: 5px solid #17a2b8; }
    .severity-low { border-left: 5px solid #28a745; }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div>
                <h2><i class="fas fa-file-alt"></i> Forensic Report #{{ $report->id }}</h2>
                <p class="text-muted mb-0">{{ $report->title }}</p>
            </div>
            <div>
                @if($report->isDraft())
                    <a href="{{ route('investigator.reports.edit', $report->id) }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Report
                    </a>
                @endif
                <a href="{{ route('investigator.reports') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Reports
                </a>
            </div>
        </div>
        <hr>
    </div>
</div>

<div class="row">
    <div class="col-md-8">
        <!-- Report Header -->
        <div class="card severity-{{ $report->severity }} mb-3">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Severity:</strong> 
                            <span class="badge badge-{{ $report->getSeverityBadgeColor() }} badge-lg">
                                {{ strtoupper($report->severity) }}
                            </span>
                        </p>
                        <p class="mb-1"><strong>Status:</strong> 
                            @if($report->status === 'draft')
                                <span class="badge badge-warning">Draft</span>
                            @elseif($report->status === 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-info">Reviewed</span>
                            @endif
                        </p>
                    </div>
                    <div class="col-md-6">
                        <p class="mb-1"><strong>Created:</strong> {{ $report->created_at->format('Y-m-d H:i:s') }}</p>
                        @if($report->completed_at)
                            <p class="mb-1"><strong>Completed:</strong> {{ $report->completed_at->format('Y-m-d H:i:s') }}</p>
                        @endif
                        <p class="mb-0"><strong>Investigator:</strong> {{ $report->investigator->name }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Email Information -->
        <div class="card mb-3">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-envelope"></i> Subject Email
            </div>
            <div class="card-body">
                <table class="table table-sm table-bordered mb-0">
                    <tr>
                        <th width="120">Email ID:</th>
                        <td>{{ $report->email->id }}</td>
                    </tr>
                    <tr>
                        <th>From:</th>
                        <td>{{ $report->email->from }}</td>
                    </tr>
                    <tr>
                        <th>To:</th>
                        <td>{{ $report->email->to }}</td>
                    </tr>
                    <tr>
                        <th>Subject:</th>
                        <td><strong>{{ $report->email->subject }}</strong></td>
                    </tr>
                    <tr>
                        <th>Date:</th>
                        <td>{{ $report->email->created_at->format('Y-m-d H:i:s') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        <!-- Findings -->
        <div class="card mb-3">
            <div class="card-header bg-danger text-white">
                <i class="fas fa-exclamation-triangle"></i> Key Findings
            </div>
            <div class="card-body">
                <div style="white-space: pre-wrap;">{{ $report->findings }}</div>
            </div>
        </div>

        <!-- Analysis -->
        <div class="card mb-3">
            <div class="card-header bg-info text-white">
                <i class="fas fa-search"></i> Detailed Analysis
            </div>
            <div class="card-body">
                <div style="white-space: pre-wrap;">{{ $report->analysis }}</div>
            </div>
        </div>

        <!-- Recommendations -->
        @if($report->recommendations)
            <div class="card mb-3">
                <div class="card-header bg-success text-white">
                    <i class="fas fa-lightbulb"></i> Recommendations
                </div>
                <div class="card-body">
                    <div style="white-space: pre-wrap;">{{ $report->recommendations }}</div>
                </div>
            </div>
        @endif
    </div>

    <div class="col-md-4">
        <!-- Audit Trail -->
        <div class="card">
            <div class="card-header bg-dark text-white">
                <i class="fas fa-history"></i> Audit Trail ({{ count($selectedLogs) }} logs)
            </div>
            <div class="card-body" style="max-height: 700px; overflow-y: auto;">
                @if(count($selectedLogs) > 0)
                    <div class="log-timeline">
                        @foreach($selectedLogs as $log)
                            <div class="log-timeline-item {{ $log->role }}">
                                <div class="card card-sm mb-2">
                                    <div class="card-body p-2">
                                        <p class="mb-1">
                                            <strong>{{ $log->user->name }}</strong>
                                            <span class="badge badge-{{ $log->role === 'admin' ? 'danger' : 'info' }} badge-sm">
                                                {{ $log->role }}
                                            </span>
                                        </p>
                                        <p class="mb-1">
                                            <code class="small">{{ $log->action }}</code>
                                            @if($log->target_id)
                                                → <small class="text-muted">#{{ $log->target_id }}</small>
                                            @endif
                                        </p>
                                        <p class="mb-1 small text-muted">
                                            <i class="fas fa-clock"></i> {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        </p>
                                        <p class="mb-0 small text-muted">
                                            <i class="fas fa-network-wired"></i> {{ $log->ip_address }}
                                        </p>
                                        @if($log->details)
                                            <details class="mt-1">
                                                <summary class="small text-primary" style="cursor: pointer;">Details</summary>
                                                <pre class="small mb-0 mt-1">{{ json_encode($log->details, JSON_PRETTY_PRINT) }}</pre>
                                            </details>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-muted small">No audit logs attached to this report.</p>
                @endif
            </div>
        </div>

        <!-- Export Options -->
        <div class="card mt-3">
            <div class="card-header">
                <i class="fas fa-download"></i> Export Options
            </div>
            <div class="card-body">
                <button class="btn btn-sm btn-primary btn-block" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
                <button class="btn btn-sm btn-secondary btn-block" onclick="alert('Export to PDF feature coming soon!')">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

