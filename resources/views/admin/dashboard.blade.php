@extends('layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h2>
    <p class="text-muted">Welcome back, <strong>{{ auth()->user()->name }}</strong>! Here's your system overview.</p>
</div>

<!-- Simple Stats Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-envelope"></i> Total Emails</h5>
            <h2>{{ $stats['total_emails'] }}</h2>
            <p class="mb-0">Encrypted in database</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-clock"></i> Pending</h5>
            <h2>{{ $stats['pending_requests'] }}</h2>
            <p class="mb-0">Awaiting approval</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-check-circle"></i> Approved</h5>
            <h2>{{ $stats['approved_requests'] }}</h2>
            <p class="mb-0">Access granted</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-times-circle"></i> Rejected</h5>
            <h2>{{ $stats['rejected_requests'] }}</h2>
            <p class="mb-0">Access denied</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Quick Actions -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-bolt"></i> Quick Actions
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <a href="{{ route('admin.upload') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-cloud-upload-alt mr-3 text-primary"></i>
                        <div class="flex-grow-1">
                            <strong>Upload Email Dataset</strong>
                            <br>
                            <small class="text-muted">Import and encrypt new emails</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    
                    <a href="{{ route('admin.emails') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-list mr-3 text-info"></i>
                        <div class="flex-grow-1">
                            <strong>View All Emails</strong>
                            <br>
                            <small class="text-muted">Browse encrypted email database</small>
                        </div>
                        <i class="fas fa-chevron-right text-muted"></i>
                    </a>
                    
                    <a href="{{ route('admin.requests') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-key mr-3 text-warning"></i>
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
                         <i class="fas fa-history mr-3 text-dark"></i>
                         <div class="flex-grow-1">
                             <strong>Forensic Audit Logs</strong>
                             <br>
                             <small class="text-muted">View complete audit trail</small>
                         </div>
                         <i class="fas fa-chevron-right text-muted"></i>
                     </a>
                     
                     <a href="{{ route('admin.workflow') }}" class="list-group-item list-group-item-action d-flex align-items-center">
                         <i class="fas fa-sitemap mr-3 text-secondary"></i>
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
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> P2DF System Architecture
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
    <div class="alert alert-warning">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell mr-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Action Required!</h5>
                <p class="mb-0">You have <strong>{{ $stats['pending_requests'] }}</strong> pending decryption {{ $stats['pending_requests'] > 1 ? 'requests' : 'request' }} waiting for your review.</p>
            </div>
            <a href="{{ route('admin.requests') }}" class="btn btn-dark ml-auto">
                Review Now <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
@endif
@endsection
