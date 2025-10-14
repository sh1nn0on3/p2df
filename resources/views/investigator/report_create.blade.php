@extends('layouts.app')

@section('title', 'Create Forensic Report')

@push('styles')
<style>
    .log-item {
        border-left: 3px solid #007bff;
        padding: 10px;
        margin-bottom: 10px;
        background: #f8f9fa;
    }
    .log-item:hover {
        background: #e9ecef;
    }
    .log-item.selected {
        border-left-color: #28a745;
        background: #d4edda;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <h2><i class="fas fa-file-medical"></i> Create Forensic Report</h2>
        <p class="text-muted">Investigation report for email: <strong>{{ $email->subject }}</strong></p>
        <a href="{{ route('investigator.emails.view', $email->id) }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-left"></i> Back to Email
        </a>
        <hr>
    </div>
</div>

<form method="POST" action="{{ route('investigator.reports.store', $email->id) }}">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <i class="fas fa-file-alt"></i> Report Details
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="title">Report Title *</label>
                        <input type="text" 
                               class="form-control @error('title') is-invalid @enderror" 
                               id="title" 
                               name="title" 
                               value="{{ old('title') }}" 
                               required 
                               placeholder="e.g., Suspicious Email Investigation - Phishing Attempt">
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="severity">Severity Level *</label>
                        <select class="form-control @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                            <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Low - Minor concerns</option>
                            <option value="medium" {{ old('severity', 'medium') === 'medium' ? 'selected' : '' }}>Medium - Moderate risk</option>
                            <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>High - Significant threat</option>
                            <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Critical - Immediate action required</option>
                        </select>
                        @error('severity')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="findings">Key Findings * <small class="text-muted">(min 50 chars)</small></label>
                        <textarea class="form-control @error('findings') is-invalid @enderror" 
                                  id="findings" 
                                  name="findings" 
                                  rows="5" 
                                  required 
                                  placeholder="Summarize the main findings from your investigation...">{{ old('findings') }}</textarea>
                        @error('findings')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">What did you discover in this email?</small>
                    </div>

                    <div class="form-group">
                        <label for="analysis">Detailed Analysis * <small class="text-muted">(min 100 chars)</small></label>
                        <textarea class="form-control @error('analysis') is-invalid @enderror" 
                                  id="analysis" 
                                  name="analysis" 
                                  rows="8" 
                                  required 
                                  placeholder="Provide detailed technical analysis...">{{ old('analysis') }}</textarea>
                        @error('analysis')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Include technical details, patterns, and evidence.</small>
                    </div>

                    <div class="form-group">
                        <label for="recommendations">Recommendations</label>
                        <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                                  id="recommendations" 
                                  name="recommendations" 
                                  rows="4" 
                                  placeholder="Provide recommendations for action...">{{ old('recommendations') }}</textarea>
                        @error('recommendations')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">What actions should be taken based on your findings?</small>
                    </div>

                    <div class="form-group">
                        <label>Report Status *</label>
                        <div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_draft" value="draft" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_draft">
                                    <i class="fas fa-save"></i> Save as Draft
                                </label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="status" id="status_completed" value="completed" {{ old('status') === 'completed' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_completed">
                                    <i class="fas fa-check-circle"></i> Mark as Completed
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-success">
                        <i class="fas fa-save"></i> Create Report
                    </button>
                    <a href="{{ route('investigator.reports') }}" class="btn btn-secondary">
                        Cancel
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-history"></i> Related Logs ({{ $relatedLogs->count() }})
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <p class="small text-muted">
                        Select logs to attach to this report. These logs provide audit trail evidence.
                    </p>

                    @forelse($relatedLogs as $log)
                        <div class="log-item" data-log-id="{{ $log->id }}">
                            <div class="form-check">
                                <input class="form-check-input log-checkbox" 
                                       type="checkbox" 
                                       name="related_logs[]" 
                                       value="{{ $log->id }}" 
                                       id="log_{{ $log->id }}">
                                <label class="form-check-label small" for="log_{{ $log->id }}">
                                    <strong>{{ $log->user->name }}</strong> 
                                    <span class="badge badge-{{ $log->role === 'admin' ? 'danger' : 'info' }} badge-sm">{{ $log->role }}</span>
                                    <br>
                                    <code>{{ $log->action }}</code>
                                    @if($log->target_id)
                                        → #{{ $log->target_id }}
                                    @endif
                                    <br>
                                    <small class="text-muted">
                                        {{ $log->created_at->format('Y-m-d H:i:s') }}
                                        <br>
                                        IP: {{ $log->ip_address }}
                                    </small>
                                </label>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted small">No related logs found.</p>
                    @endforelse
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-sm btn-primary" id="selectAllLogs">
                        <i class="fas fa-check-square"></i> Select All
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="deselectAllLogs">
                        <i class="fas fa-square"></i> Deselect All
                    </button>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Toggle log item visual state
    $('.log-checkbox').change(function() {
        if ($(this).is(':checked')) {
            $(this).closest('.log-item').addClass('selected');
        } else {
            $(this).closest('.log-item').removeClass('selected');
        }
    });

    // Select all logs
    $('#selectAllLogs').click(function() {
        $('.log-checkbox').prop('checked', true).trigger('change');
    });

    // Deselect all logs
    $('#deselectAllLogs').click(function() {
        $('.log-checkbox').prop('checked', false).trigger('change');
    });
});
</script>
@endpush

