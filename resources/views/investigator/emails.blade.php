@extends('layouts.app')

@section('title', 'Danh Sách Email')

@push('styles')
<style>
    .email-card {
        transition: all 0.3s ease;
        border: 1px solid #dee2e6;
        margin-bottom: 15px;
    }
    
    .email-card:hover {
        box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        transform: translateY(-2px);
    }
    
    .email-meta {
        font-size: 13px;
        color: #6c757d;
    }
    
    .email-subject {
        font-weight: 600;
        color: #495057;
        margin-bottom: 8px;
    }
    
    .email-participants {
        font-size: 14px;
        color: #6c757d;
    }
    
    .encrypted-badge {
        background: #dc3545;
        color: white;
        font-size: 11px;
        padding: 4px 8px;
        border-radius: 12px;
    }
    
    .search-box {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 20px;
        margin-bottom: 20px;
    }
    
    .email-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 15px;
    }
    
    @media (max-width: 768px) {
        .email-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="page-header">
    <h2><i class="fas fa-envelope"></i> Danh Sách Email</h2>
    <p class="text-muted">
        @if(request('search'))
            Tìm thấy: <strong>{{ $emails->count() }}</strong> kết quả
        @else
            Tổng cộng: <strong>{{ $emails->count() }}</strong> email
        @endif
    </p>
</div>

<!-- Search Box -->
<div class="search-box">
    <form method="GET">
        <div class="input-group">
            <input type="text" 
                   name="search" 
                   class="form-control" 
                   placeholder="Tìm kiếm email theo người gửi, người nhận hoặc tiêu đề..." 
                   value="{{ request('search') }}">
            <div class="input-group-append">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> Tìm
                </button>
                @if(request('search'))
                    <a href="{{ route('investigator.emails') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Xóa
                    </a>
                @endif
            </div>
        </div>
    </form>
</div>

<!-- Email Grid -->
<div class="email-grid">
    @forelse($emails as $email)
        <div class="card email-card">
            <div class="card-body">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <span class="badge badge-primary">#{{ $email->id }}</span>
                        <span class="encrypted-badge ml-2">
                            <i class="fas fa-lock"></i> Mã hóa
                        </span>
                    </div>
                    <small class="email-meta">
                        {{ $email->created_at->format('d/m/Y H:i') }}
                    </small>
                </div>

                <!-- Subject -->
                <div class="email-subject">
                    {{ Str::limit($email->subject, 50) }}
                </div>

                <!-- Participants -->
                <div class="email-participants mb-3">
                    <div class="mb-1">
                        <i class="fas fa-user text-primary"></i> 
                        <strong>Từ:</strong> {{ Str::limit($email->from, 30) }}
                    </div>
                    <div>
                        <i class="fas fa-user text-info"></i> 
                        <strong>Đến:</strong> {{ Str::limit($email->to, 30) }}
                    </div>
                </div>

                <!-- Warning -->
                <div class="alert alert-warning alert-sm mb-3">
                    <i class="fas fa-lock"></i> 
                    <small>Nội dung đã được mã hóa - Yêu cầu giải mã để xem</small>
                </div>

                <!-- Action Button -->
                <a href="{{ route('investigator.emails.view', $email->id) }}" class="btn btn-outline-primary btn-sm btn-block">
                    <i class="fas fa-eye"></i> Xem Chi Tiết
                </a>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-inbox fa-4x mb-3 text-muted"></i>
                    <h5 class="text-muted">Không tìm thấy email</h5>
                    <p class="text-muted">Thử các từ khóa tìm kiếm khác hoặc kiểm tra lại sau.</p>
                </div>
            </div>
        </div>
    @endforelse
</div>
@endsection
