@extends('layouts.app')

@section('title', 'Investigator Dashboard')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-search"></i> Investigator Dashboard</h2>
    <p class="text-muted">Welcome, <strong>{{ auth()->user()->name }}</strong>! Ready to investigate?</p>
</div>

<!-- Simple Stats Cards -->
<div class="row">
    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-database"></i> Available</h5>
            <h2>{{ $stats['total_emails'] }}</h2>
            <p class="mb-0">Encrypted emails</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-hourglass-half"></i> Pending</h5>
            <h2>{{ $stats['my_pending_requests'] }}</h2>
            <p class="mb-0">Awaiting approval</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-unlock"></i> Approved</h5>
            <h2>{{ $stats['my_approved_requests'] }}</h2>
            <p class="mb-0">Ready to decrypt</p>
        </div>
    </div>

    <div class="col-md-3 col-sm-6 mb-4">
        <div class="stat-card">
            <h5><i class="fas fa-ban"></i> Rejected</h5>
            <h2>{{ $stats['my_rejected_requests'] }}</h2>
            <p class="mb-0">Access denied</p>
        </div>
    </div>
</div>

<div class="row">
    <!-- Recent Emails -->
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-envelope-open-text"></i> Recent Emails
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>From</th>
                                <th>To</th>
                                <th>Subject</th>
                                <th>Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentEmails as $email)
                                <tr>
                                    <td><strong>#{{ $email->id }}</strong></td>
                                    <td><small>{{ Str::limit($email->from, 25) }}</small></td>
                                    <td><small>{{ Str::limit($email->to, 25) }}</small></td>
                                    <td>{{ Str::limit($email->subject, 35) }}</td>
                                    <td><small>{{ $email->created_at->format('M d, Y') }}</small></td>
                                    <td>
                                        <a href="{{ route('investigator.emails.view', $email->id) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i> View
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('investigator.emails') }}" class="btn btn-primary btn-sm">
                    <i class="fas fa-list"></i> View All Emails
                </a>
            </div>
        </div>
    </div>

    <!-- Investigation Tools -->
    <div class="col-lg-4 mb-4">
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-tools"></i> Investigation Tools
            </div>
            <div class="card-body">
                <h6 class="font-weight-bold">Your Capabilities:</h6>
                
                <div class="mb-3">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-eye mr-2 text-info"></i>
                        <small><strong>View metadata</strong> of all emails</small>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-paper-plane mr-2 text-warning"></i>
                        <small><strong>Request decryption</strong> with justification</small>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-unlock mr-2 text-success"></i>
                        <small><strong>Decrypt approved</strong> emails privately</small>
                    </div>

                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-file-alt mr-2 text-danger"></i>
                        <small><strong>Create forensic reports</strong> with evidence</small>
                    </div>

                    <div class="d-flex align-items-center">
                        <i class="fas fa-history mr-2 text-dark"></i>
                        <small><strong>Track audit trail</strong> of all activities</small>
                    </div>
                </div>
            </div>
        </div>

        <div class="card bg-primary text-white">
            <div class="card-body text-center">
                <i class="fas fa-shield-alt fa-3x mb-3"></i>
                <h6 class="font-weight-bold">Privacy Protected</h6>
                <p class="small mb-0">All access requires admin approval. Your actions are logged for transparency.</p>
            </div>
        </div>
    </div>
</div>

@if($stats['my_approved_requests'] > 0)
    <div class="alert alert-success">
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle fa-2x mr-3"></i>
            <div>
                <h5 class="alert-heading mb-1">Approved Requests Ready!</h5>
                <p class="mb-0">You have <strong>{{ $stats['my_approved_requests'] }}</strong> approved {{ $stats['my_approved_requests'] > 1 ? 'requests' : 'request' }}. You can now decrypt and investigate.</p>
            </div>
            <a href="{{ route('investigator.requests') }}" class="btn btn-light ml-auto">
                View Requests <i class="fas fa-arrow-right ml-2"></i>
            </a>
        </div>
    </div>
@endif
@endsection
