@extends('layouts.app')

@section('title', 'Digital Forensics Workflow')

@section('content')
<div class="page-header">
    <h2><i class="fas fa-sitemap"></i> Digital Forensics Investigation Workflow</h2>
    <p class="text-muted">Luồng quá trình điều tra số trong hệ thống P2DF</p>
</div>

<!-- Overview Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Tổng quan hệ thống P2DF
            </div>
            <div class="card-body">
                <p><strong>P2DF (Privacy-Preserving Digital Forensics)</strong> là hệ thống điều tra số bảo mật, cho phép điều tra viên truy cập dữ liệu email được mã hóa trong khi vẫn đảm bảo quyền riêng tư và tính toàn vẹn của dữ liệu.</p>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6><i class="fas fa-shield-alt text-primary"></i> Nguyên tắc hoạt động:</h6>
                        <ul>
                            <li>Dữ liệu được mã hóa với AES-256-CBC</li>
                            <li>Khóa mã hóa được bảo vệ bởi RSA-2048</li>
                            <li>Mọi truy cập đều cần phê duyệt của Admin</li>
                            <li>Audit trail đầy đủ cho mọi hoạt động</li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <h6><i class="fas fa-users text-info"></i> Vai trò trong hệ thống:</h6>
                        <ul>
                            <li><strong>Admin:</strong> Quản lý dữ liệu và phê duyệt truy cập</li>
                            <li><strong>Investigator:</strong> Thực hiện điều tra và tạo báo cáo</li>
                            <li><strong>System:</strong> Ghi log và đảm bảo tính toàn vẹn</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Workflow Diagram -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-project-diagram"></i> Sơ đồ luồng quá trình điều tra
            </div>
            <div class="card-body">
                <!-- Workflow Steps -->
                <div class="workflow-container">
                    <!-- Step 1: Data Collection -->
                    <div class="workflow-step">
                        <div class="step-icon">
                            <i class="fas fa-upload"></i>
                        </div>
                        <div class="step-content">
                            <h5>1. Thu thập dữ liệu</h5>
                            <p>Admin upload dataset email và tự động mã hóa bằng AES-256-CBC với khóa riêng cho mỗi email.</p>
                            <div class="step-details">
                                <span class="badge badge-primary">Admin</span>
                                <span class="badge badge-success">AES-256-CBC</span>
                                <span class="badge badge-info">Auto Encryption</span>
                            </div>
                        </div>
                    </div>

                    <div class="workflow-arrow">
                        <i class="fas fa-arrow-down"></i>
                    </div>

                    <!-- Step 2: Investigation Request -->
                    <div class="workflow-step">
                        <div class="step-icon">
                            <i class="fas fa-search"></i>
                        </div>
                        <div class="step-content">
                            <h5>2. Yêu cầu điều tra</h5>
                            <p>Investigator xem metadata của email và gửi yêu cầu giải mã với lý do điều tra hợp lệ.</p>
                            <div class="step-details">
                                <span class="badge badge-warning">Investigator</span>
                                <span class="badge badge-info">Metadata Only</span>
                                <span class="badge badge-secondary">Request with Reason</span>
                            </div>
                        </div>
                    </div>

                    <div class="workflow-arrow">
                        <i class="fas fa-arrow-down"></i>
                    </div>

                    <!-- Step 3: Admin Review -->
                    <div class="workflow-step">
                        <div class="step-icon">
                            <i class="fas fa-gavel"></i>
                        </div>
                        <div class="step-content">
                            <h5>3. Phê duyệt Admin</h5>
                            <p>Admin xem xét yêu cầu và quyết định phê duyệt hoặc từ chối. Nếu phê duyệt, khóa AES sẽ được mã hóa lại với khóa công khai của Investigator.</p>
                            <div class="step-details">
                                <span class="badge badge-danger">Admin</span>
                                <span class="badge badge-success">RSA-2048</span>
                                <span class="badge badge-info">Key Re-encryption</span>
                            </div>
                        </div>
                    </div>

                    <div class="workflow-arrow">
                        <i class="fas fa-arrow-down"></i>
                    </div>

                    <!-- Step 4: Decryption -->
                    <div class="workflow-step">
                        <div class="step-icon">
                            <i class="fas fa-unlock"></i>
                        </div>
                        <div class="step-content">
                            <h5>4. Giải mã và điều tra</h5>
                            <p>Investigator sử dụng khóa riêng của mình để giải mã khóa AES, sau đó giải mã nội dung email để tiến hành điều tra.</p>
                            <div class="step-details">
                                <span class="badge badge-warning">Investigator</span>
                                <span class="badge badge-success">Private Key</span>
                                <span class="badge badge-info">Local Decryption</span>
                            </div>
                        </div>
                    </div>

                    <div class="workflow-arrow">
                        <i class="fas fa-arrow-down"></i>
                    </div>

                    <!-- Step 5: Report Generation -->
                    <div class="workflow-step">
                        <div class="step-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <div class="step-content">
                            <h5>5. Tạo báo cáo điều tra</h5>
                            <p>Investigator tạo báo cáo điều tra với bằng chứng, phân tích và kết luận. Báo cáo được lưu trữ an toàn trong hệ thống.</p>
                            <div class="step-details">
                                <span class="badge badge-warning">Investigator</span>
                                <span class="badge badge-success">Evidence</span>
                                <span class="badge badge-info">Secure Storage</span>
                            </div>
                        </div>
                    </div>

                    <div class="workflow-arrow">
                        <i class="fas fa-arrow-down"></i>
                    </div>

                    <!-- Step 6: Audit Trail -->
                    <div class="workflow-step">
                        <div class="step-icon">
                            <i class="fas fa-history"></i>
                        </div>
                        <div class="step-content">
                            <h5>6. Ghi nhật ký và Audit</h5>
                            <p>Mọi hoạt động được ghi lại trong nhật ký điều tra, bao gồm thời gian, người thực hiện, và kết quả của mỗi bước.</p>
                            <div class="step-details">
                                <span class="badge badge-secondary">System</span>
                                <span class="badge badge-success">Immutable Logs</span>
                                <span class="badge badge-info">Full Traceability</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Technical Details -->
<div class="row mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-lock"></i> Bảo mật và Mã hóa
            </div>
            <div class="card-body">
                <h6><i class="fas fa-key text-primary"></i> AES-256-CBC Encryption</h6>
                <p>Mỗi email được mã hóa với khóa AES-256-CBC duy nhất, đảm bảo dữ liệu được bảo vệ ở mức độ cao nhất.</p>

                <h6><i class="fas fa-shield-alt text-success"></i> RSA-2048 Key Management</h6>
                <p>Khóa AES được bảo vệ bằng mã hóa RSA-2048, chỉ Admin mới có thể giải mã để chuyển quyền truy cập.</p>

                <h6><i class="fas fa-user-shield text-warning"></i> Zero-Knowledge Architecture</h6>
                <p>Hệ thống được thiết kế để Admin không thể xem nội dung email, chỉ có thể quản lý quyền truy cập.</p>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-clipboard-list"></i> Quy trình và Tuân thủ
            </div>
            <div class="card-body">
                <h6><i class="fas fa-balance-scale text-info"></i> Legal Compliance</h6>
                <p>Hệ thống tuân thủ các tiêu chuẩn điều tra số quốc tế và đảm bảo tính hợp pháp của bằng chứng.</p>

                <h6><i class="fas fa-eye text-danger"></i> Transparency</h6>
                <p>Mọi hoạt động đều được ghi lại và có thể truy xuất, đảm bảo tính minh bạch trong quá trình điều tra.</p>

                <h6><i class="fas fa-certificate text-success"></i> Chain of Custody</h6>
                <p>Chuỗi quản lý bằng chứng được duy trì hoàn chỉnh từ thu thập đến báo cáo cuối cùng.</p>
            </div>
        </div>
    </div>
</div>

<!-- Benefits and Features -->
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-star"></i> Lợi ích và Tính năng chính
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="fas fa-shield-alt fa-2x text-primary mb-3"></i>
                            <h6>Bảo mật Tuyệt đối</h6>
                            <p>Dữ liệu được mã hóa end-to-end, chỉ người được ủy quyền mới có thể truy cập.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="fas fa-search fa-2x text-info mb-3"></i>
                            <h6>Điều tra Hiệu quả</h6>
                            <p>Giao diện thân thiện giúp điều tra viên tập trung vào phân tích thay vì kỹ thuật.</p>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="feature-item">
                            <i class="fas fa-gavel fa-2x text-success mb-3"></i>
                            <h6>Tuân thủ Pháp lý</h6>
                            <p>Đáp ứng các tiêu chuẩn điều tra số quốc tế và đảm bảo tính hợp pháp của bằng chứng.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .workflow-container {
        position: relative;
        padding: 20px 0;
    }

    .workflow-step {
        display: flex;
        align-items: center;
        margin-bottom: 30px;
        padding: 20px;
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        transition: all 0.3s ease;
    }

    .workflow-step:hover {
        background: #e9ecef;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }

    .step-icon {
        width: 60px;
        height: 60px;
        background: #343a40;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-right: 20px;
        flex-shrink: 0;
    }

    .step-content {
        flex: 1;
    }

    .step-content h5 {
        margin-bottom: 10px;
        color: #495057;
        font-weight: 600;
    }

    .step-content p {
        margin-bottom: 15px;
        color: #6c757d;
        line-height: 1.6;
    }

    .step-details {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }

    .workflow-arrow {
        text-align: center;
        margin: 15px 0;
        color: #6c757d;
        font-size: 1.2rem;
    }

    .feature-item {
        text-align: center;
        padding: 20px;
    }

    .feature-item h6 {
        margin-bottom: 15px;
        font-weight: 600;
        color: #495057;
    }

    .feature-item p {
        color: #6c757d;
        line-height: 1.6;
    }

    @media (max-width: 768px) {
        .workflow-step {
            flex-direction: column;
            text-align: center;
        }

        .step-icon {
            margin-right: 0;
            margin-bottom: 15px;
        }

        .step-details {
            justify-content: center;
        }
    }
</style>
@endpush
@endsection




