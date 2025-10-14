@extends('layouts.app')

@section('title', 'My Forensic Reports')

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-file-alt"></i> My Forensic Reports</h2>
        <p class="text-muted">Investigation reports you have created</p>
        <hr>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <form method="GET" class="form-inline">
            <label class="mr-2">Filter:</label>
            <select name="status" class="form-control mr-2" onchange="this.form.submit()">
                <option value="all" {{ $status === 'all' ? 'selected' : '' }}>All</option>
                <option value="draft" {{ $status === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="completed" {{ $status === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="reviewed" {{ $status === 'reviewed' ? 'selected' : '' }}>Reviewed</option>
            </select>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Email Subject</th>
                    <th>Severity</th>
                    <th>Status</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($reports as $report)
                    <tr>
                        <td>{{ $report->id }}</td>
                        <td>
                            <strong>{{ $report->title }}</strong>
                        </td>
                        <td>
                            <small>{{ Str::limit($report->email->subject, 40) }}</small>
                        </td>
                        <td>
                            <span class="badge badge-{{ $report->getSeverityBadgeColor() }}">
                                {{ strtoupper($report->severity) }}
                            </span>
                        </td>
                        <td>
                            @if($report->status === 'draft')
                                <span class="badge badge-warning">Draft</span>
                            @elseif($report->status === 'completed')
                                <span class="badge badge-success">Completed</span>
                            @else
                                <span class="badge badge-info">Reviewed</span>
                            @endif
                        </td>
                        <td><small>{{ $report->created_at->format('Y-m-d H:i') }}</small></td>
                        <td>
                            <a href="{{ route('investigator.reports.view', $report->id) }}" class="btn btn-sm btn-info">
                                <i class="fas fa-eye"></i> View
                            </a>
                            @if($report->isDraft())
                                <a href="{{ route('investigator.reports.edit', $report->id) }}" class="btn btn-sm btn-primary">
                                    <i class="fas fa-edit"></i> Edit
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted">No reports found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="card-footer">
        {{ $reports->links() }}
    </div>
</div>
@endsection

