@extends('layouts.app')

@section('title', 'Upload Emails')


@section('content')
<div class="page-header">
    <h2><i class="fas fa-cloud-upload-alt"></i> Upload Email Dataset</h2>
    <p class="text-muted">Import and automatically encrypt email dataset with P2DF security</p>
</div>

<div class="row">
    <div class="col-lg-8 mb-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-csv"></i> Upload & Encrypt
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.upload.submit') }}" enctype="multipart/form-data" id="uploadForm">
                    @csrf
                    
                    <div class="border border-dashed p-4 text-center" onclick="document.getElementById('email_file').click()" style="cursor: pointer;">
                        <i class="fas fa-cloud-upload-alt fa-3x text-muted mb-3"></i>
                        <h5 class="mb-2">Drop CSV file here or click to browse</h5>
                        <p class="text-muted mb-0">Maximum file size: 10MB</p>
                        <p class="text-muted small">Supported formats: .csv, .txt</p>
                    </div>

                    <input type="file" 
                           class="d-none @error('email_file') is-invalid @enderror" 
                           id="email_file" 
                           name="email_file" 
                           accept=".csv,.txt" 
                           required
                           onchange="showFileName(this)">
                    
                    @error('email_file')
                        <div class="alert alert-danger mt-3">
                            {{ $message }}
                        </div>
                    @enderror

                    <div id="fileInfo" class="mt-3" style="display: none;">
                        <div class="alert alert-info">
                            <i class="fas fa-file-csv"></i> Selected: <strong id="fileName"></strong>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-lock"></i> Upload & Encrypt Now
                        </button>
                        <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary btn-lg">
                            <i class="fas fa-times"></i> Cancel
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- CSV Format -->
        <div class="card mb-3">
            <div class="card-header">
                <i class="fas fa-question-circle"></i> CSV Format Required
            </div>
            <div class="card-body">
                <p class="small mb-2"><strong>Header Row:</strong></p>
                <pre class="bg-light p-2 small">from,to,subject,body</pre>
                
                <p class="small mb-2 mt-3"><strong>Example Data:</strong></p>
                <pre class="bg-light p-2 small">john@ex.com,jane@ex.com,Hello,Message here</pre>
            </div>
        </div>

        <!-- Encryption Process -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-lock"></i> Encryption Process
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2"><strong>Generate AES-256 Key</strong> - Unique per email</li>
                    <li class="mb-2"><strong>Encrypt Content</strong> - AES-256-CBC</li>
                    <li class="mb-2"><strong>Encrypt AES Key</strong> - RSA-2048 with Admin key</li>
                    <li class="mb-2"><strong>Store Encrypted</strong> - Save to database</li>
                    <li><strong>Log Upload</strong> - Audit trail</li>
                </ol>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function showFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('fileName').textContent = input.files[0].name;
        document.getElementById('fileInfo').style.display = 'block';
    }
}
</script>
@endpush
