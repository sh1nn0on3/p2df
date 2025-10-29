@extends('layouts.app')

@section('title', 'Tạo Báo Cáo Điều Tra')

@push('styles')
<style>
    .log-item {
        border-left: 3px solid #007bff;
        padding: 10px;
        margin-bottom: 10px;
        background: #f8f9fa;
        border-radius: 5px;
        transition: all 0.3s ease;
    }
    .log-item:hover {
        background: #e9ecef;
        transform: translateX(5px);
    }
    .log-item.selected {
        border-left-color: #28a745;
        background: #d4edda;
        box-shadow: 0 2px 5px rgba(40, 167, 69, 0.2);
    }
    
    .form-section {
        background: #f8f9fa;
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        border: 1px solid #e9ecef;
    }
    
    .form-section h6 {
        color: #495057;
        font-weight: 600;
        margin-bottom: 15px;
        padding-bottom: 10px;
        border-bottom: 2px solid #007bff;
    }
    
    .status-badge {
        display: inline-block;
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.9em;
        font-weight: 600;
    }
    
    .status-badge.draft {
        background: #ffc107;
        color: #212529;
    }
    
    .status-badge.completed {
        background: #28a745;
        color: white;
    }
    
    .email-preview {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 20px;
    }
</style>
@endpush

@section('content')
<div class="row">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2><i class="fas fa-file-medical"></i> Tạo Báo Cáo Điều Tra</h2>
                <p class="text-muted mb-0">Báo cáo điều tra cho email: <strong>{{ $email->subject }}</strong></p>
            </div>
            <div>
                <a href="{{ route('investigator.emails.view', $email->id) }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Quay Lại Email
                </a>
            </div>
        </div>
        
        <!-- Email Preview -->
        <div class="email-preview">
            <div class="row">
                <div class="col-md-6">
                    <strong><i class="fas fa-user"></i> Người gửi:</strong> {{ $email->from }}
                </div>
                <div class="col-md-6">
                    <strong><i class="fas fa-user"></i> Người nhận:</strong> {{ $email->to }}
                </div>
                <div class="col-md-6">
                    <strong><i class="fas fa-calendar"></i> Ngày tạo:</strong> {{ $email->created_at->format('d/m/Y H:i') }}
                </div>
                <div class="col-md-6">
                    <strong><i class="fas fa-hashtag"></i> Email ID:</strong> #{{ $email->id }}
                </div>
            </div>
        </div>
    </div>
</div>

<form method="POST" action="{{ route('investigator.reports.store', $email->id) }}">
    @csrf
    
    <div class="row">
        <div class="col-md-8">
            <div class="form-section">
                <h6><i class="fas fa-file-alt"></i> Thông Tin Báo Cáo</h6>
                <div class="form-group">
                    <label for="title">Tiêu đề Báo cáo *</label>
                    <input type="text" 
                           class="form-control @error('title') is-invalid @enderror" 
                           id="title" 
                           name="title" 
                           value="{{ old('title') }}" 
                           required 
                           placeholder="VD: Điều tra Email Nghi Ngờ - Phishing Attempt">
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="severity">Mức độ Nghiêm trọng *</label>
                    <select class="form-control @error('severity') is-invalid @enderror" id="severity" name="severity" required>
                        <option value="low" {{ old('severity') === 'low' ? 'selected' : '' }}>Thấp - Mối quan ngại nhỏ</option>
                        <option value="medium" {{ old('severity', 'medium') === 'medium' ? 'selected' : '' }}>Trung bình - Rủi ro vừa phải</option>
                        <option value="high" {{ old('severity') === 'high' ? 'selected' : '' }}>Cao - Mối đe dọa đáng kể</option>
                        <option value="critical" {{ old('severity') === 'critical' ? 'selected' : '' }}>Nghiêm trọng - Cần hành động ngay lập tức</option>
                    </select>
                    @error('severity')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="form-section">
                <h6><i class="fas fa-search"></i> Phát Hiện Chính</h6>

                <div class="form-group">
                    <label for="findings">Phát Hiện Chính * <small class="text-muted">(tối thiểu 50 ký tự)</small></label>
                    <textarea class="form-control @error('findings') is-invalid @enderror" 
                              id="findings" 
                              name="findings" 
                              rows="5" 
                              required 
                              placeholder="Tóm tắt những phát hiện chính từ cuộc điều tra của bạn...">{{ old('findings') }}</textarea>
                    @error('findings')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Bạn đã phát hiện gì trong email này?</small>
                </div>
            </div>
            
            <div class="form-section">
                <h6><i class="fas fa-microscope"></i> Phân Tích Chi Tiết</h6>

                <div class="form-group">
                    <label for="analysis">Phân Tích Chi Tiết * <small class="text-muted">(tối thiểu 100 ký tự)</small></label>
                    <textarea class="form-control @error('analysis') is-invalid @enderror" 
                              id="analysis" 
                              name="analysis" 
                              rows="8" 
                              required 
                              placeholder="Cung cấp phân tích kỹ thuật chi tiết...">{{ old('analysis') }}</textarea>
                    @error('analysis')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Bao gồm chi tiết kỹ thuật, mẫu hình và bằng chứng.</small>
                </div>
            </div>
            
            <div class="form-section">
                <h6><i class="fas fa-lightbulb"></i> Khuyến Nghị</h6>

                <div class="form-group">
                    <label for="recommendations">Khuyến Nghị</label>
                    <textarea class="form-control @error('recommendations') is-invalid @enderror" 
                              id="recommendations" 
                              name="recommendations" 
                              rows="4" 
                              placeholder="Cung cấp khuyến nghị cho hành động...">{{ old('recommendations') }}</textarea>
                    @error('recommendations')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">Những hành động nào nên được thực hiện dựa trên phát hiện của bạn?</small>
                </div>
            </div>
            
            <div class="form-section">
                <h6><i class="fas fa-cog"></i> Trạng Thái Báo Cáo</h6>

                <div class="form-group">
                    <label>Trạng Thái Báo Cáo *</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_draft" value="draft" {{ old('status', 'draft') === 'draft' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_draft">
                                    <span class="status-badge draft">
                                        <i class="fas fa-save"></i> Lưu Bản Nháp
                                    </span>
                                    <br><small class="text-muted">Tiếp tục chỉnh sửa sau</small>
                                </label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" id="status_completed" value="completed" {{ old('status') === 'completed' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_completed">
                                    <span class="status-badge completed">
                                        <i class="fas fa-check-circle"></i> Hoàn Thành
                                    </span>
                                    <br><small class="text-muted">Gửi cho admin duyệt</small>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <button type="submit" class="btn btn-success btn-lg">
                    <i class="fas fa-save"></i> Tạo Báo Cáo
                </button>
                <a href="{{ route('investigator.reports') }}" class="btn btn-secondary btn-lg ml-2">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header bg-dark text-white">
                    <i class="fas fa-history"></i> Logs Liên Quan ({{ $relatedLogs->count() }})
                </div>
                <div class="card-body" style="max-height: 600px; overflow-y: auto;">
                    <p class="small text-muted">
                        Chọn logs để đính kèm vào báo cáo này. Các logs này cung cấp bằng chứng audit trail.
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
                        <p class="text-muted small">Không tìm thấy logs liên quan.</p>
                    @endforelse
                </div>
                <div class="card-footer">
                    <button type="button" class="btn btn-sm btn-primary" id="selectAllLogs">
                        <i class="fas fa-check-square"></i> Chọn Tất Cả
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary" id="deselectAllLogs">
                        <i class="fas fa-square"></i> Bỏ Chọn Tất Cả
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

