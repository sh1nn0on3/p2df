@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 30px;
        border-radius: 8px;
        margin-bottom: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }
    .page-header h2 {
        margin: 0;
        font-size: 2rem;
        font-weight: bold;
    }
    .page-header p {
        margin: 10px 0 0 0;
        opacity: 0.9;
    }
    .stat-card {
        background: white;
        border-radius: 8px;
        padding: 25px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        transition: transform 0.2s ease, box-shadow 0.2s ease;
        border-left: 4px solid #667eea;
        height: 100%;
    }
    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }
    .stat-card h5 {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .stat-card h2 {
        font-size: 2.5rem;
        font-weight: bold;
        color: #333;
        margin: 0;
    }
    .stat-card p {
        color: #6c757d;
        font-size: 0.85rem;
        margin-top: 10px;
    }
    .stat-card.total-emails { border-left-color: #667eea; }
    .stat-card.total-emails h5 { color: #667eea; }
    .stat-card.pending { border-left-color: #ffc107; }
    .stat-card.pending h5 { color: #ffc107; }
    .stat-card.approved { border-left-color: #28a745; }
    .stat-card.approved h5 { color: #28a745; }
    .stat-card.rejected { border-left-color: #dc3545; }
    .stat-card.rejected h5 { color: #dc3545; }
    
    .quick-actions-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: 100%;
    }
    .quick-actions-card .card-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border: none;
        padding: 15px 20px;
    }
    .list-group-item {
        border-left: none;
        border-right: none;
        padding: 15px 20px;
        transition: all 0.2s ease;
    }
    .list-group-item:first-child { border-top: none; }
    .list-group-item:hover {
        background: #f8f9fa;
        padding-left: 25px;
    }
    .system-info-card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        height: 100%;
    }
    .system-info-card .card-header {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
        color: white;
        border: none;
        padding: 15px 20px;
    }
    .alert-action {
        border-radius: 8px;
        border: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
    <p>Welcome back, <strong>{{ auth()->user()->name }}</strong>! Here's your system overview.</p>
</div>

<!-- Simple Stats Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card total-emails">
            <h5><i class="fas fa-envelope"></i> Total Emails</h5>
            <h2>{{ $stats['total_emails'] }}</h2>
            <p class="mb-0">Encrypted in database</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card pending">
            <h5><i class="fas fa-clock"></i> Pending</h5>
            <h2>{{ $stats['pending_requests'] }}</h2>
            <p class="mb-0">Awaiting approval</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card approved">
            <h5><i class="fas fa-check-circle"></i> Approved</h5>
            <h2>{{ $stats['approved_requests'] }}</h2>
            <p class="mb-0">Access granted</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card rejected">
            <h5><i class="fas fa-times-circle"></i> Rejected</h5>
            <h2>{{ $stats['rejected_requests'] }}</h2>
            <p class="mb-0">Access denied</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card quick-actions-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-bolt"></i> Quick Actions</h5>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.upload') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-cloud-upload-alt mr-3 text-primary fa-lg"></i>
                        <div class="flex-grow-1">
                            <strong>Upload Email Dataset</strong>
                            <br>
                            <small class="text-muted">Import and encrypt new emails</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    
                    <a href="{{ route('admin.emails') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-list mr-3 text-info fa-lg"></i>
                        <div class="flex-grow-1">
                            <strong>View All Emails</strong>
                            <br>
                            <small class="text-muted">Browse encrypted email database</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    
                    <a href="{{ route('admin.requests') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-key mr-3 text-warning fa-lg"></i>
                        <div class="flex-grow-1">
                            <strong>Review Requests</strong>
                            <br>
                            <small class="text-muted">Manage decryption requests</small>
                        </div>
                        @if($stats['pending_requests'] > 0)
                            <span class="badge badge-warning badge-pill">{{ $stats['pending_requests'] }}</span>
                        @endif
                        <i class="fas fa-chevron-right text-muted ml-2"></i>
                    </a>
                    
                     <a href="{{ route('admin.logs') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                         <i class="fas fa-history mr-3 text-dark fa-lg"></i>
                         <div class="flex-grow-1">
                             <strong>Forensic Audit Logs</strong>
                             <br>
                             <small class="text-muted">View complete audit trail</small>
                         </div>
                         <i class="fas fa-chevron-right text-muted"></i>
                     </a>
                     
                     <a href="{{ route('admin.workflow') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                         <i class="fas fa-sitemap mr-3 text-secondary fa-lg"></i>
                         <div class="flex-grow-1">
                             <strong>Investigation Workflow</strong>
                             <br>
                             <small class="text-muted">View digital forensics process</small>
                         </div>
                         <i class="fas fa-chevron-right text-muted"></i>
                     </a>
                </div>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="col-md-6 mb-4">
        <div class="card system-info-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> P2DF System Architecture</h5>
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold mb-3">Privacy-Preserving Digital Forensics</h6>
                
                <div class="mb-3">
                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-lock mr-3 text-success"></i>
                        <div>
                            <strong>AES-256-CBC Encryption</strong>
                            <br>
                            <small class="text-muted">Each email encrypted with unique key</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-key mr-3 text-danger"></i>
                        <div>
                            <strong>RSA-2048 Key Management</strong>
                            <br>
                            <small class="text-muted">Asymmetric encryption for access control</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start mb-2">
                        <i class="fas fa-user-shield mr-3 text-warning"></i>
                        <div>
                            <strong>Request-Approval Workflow</strong>
                            <br>
                            <small class="text-muted">You control investigator access</small>
                        </div>
                    </div>

                    <div class="d-flex align-items-start">
                        <i class="fas fa-history mr-3 text-info"></i>
                        <div>
                            <strong>Complete Audit Trail</strong>
                            <br>
                            <small class="text-muted">Every action logged for transparency</small>
                        </div>
                    </div>
                </div>

                <div class="alert alert-info mt-3 mb-0">
                    <strong>Your Role:</strong> You control access to encrypted data by approving/rejecting decryption requests.
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Activity Overview (if needed) -->
@if($stats['pending_requests'] > 0)
    <div class="alert alert-warning alert-action">
        <div class="d-flex align-items-center">
            <div class="mr-3">
                <i class="fas fa-bell fa-2x"></i>
            </div>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-1">Action Required!</h5>
                <p class="mb-0">You have <strong>{{ $stats['pending_requests'] }}</strong> pending decryption {{ $stats['pending_requests'] > 1 ? 'requests' : 'request' }} waiting for your review.</p>
            </div>
            <a href="{{ route('admin.requests') }}" class="btn btn-warning ml-auto">
                Review Now <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
@endif
@endsection
