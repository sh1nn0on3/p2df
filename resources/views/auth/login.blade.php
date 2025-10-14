<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - P2DF Email Forensic</title>
    
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        body {
            background: #f8f9fa;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        .login-container {
            width: 100%;
            max-width: 400px;
            padding: 20px;
        }

        .login-card {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .login-header {
            background: #343a40;
            color: white;
            padding: 30px;
            text-align: center;
        }

        .login-header h4 {
            font-weight: 600;
            font-size: 1.5rem;
            margin-bottom: 10px;
        }

        .login-header p {
            margin: 0;
            opacity: 0.9;
        }

        .login-body {
            padding: 30px;
        }

        .form-group label {
            font-weight: 500;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control {
            border-radius: 4px;
            border: 1px solid #ced4da;
            padding: 10px 12px;
            font-size: 1rem;
        }

        .form-control:focus {
            border-color: #80bdff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .input-icon {
            position: relative;
        }

        .input-icon i {
            position: absolute;
            left: 12px;
            top: 50%;
            transform: translateY(-50%);
            color: #6c757d;
        }

        .input-icon .form-control {
            padding-left: 40px;
        }

        .btn-login {
            background: #343a40;
            color: white;
            border: 1px solid #343a40;
            border-radius: 4px;
            padding: 10px;
            font-weight: 500;
            font-size: 1rem;
            width: 100%;
        }

        .btn-login:hover {
            background: #555555;
            border-color: #555555;
            color: white;
        }

        .custom-checkbox .custom-control-label {
            font-weight: 400;
            color: #6c757d;
        }

        .demo-card {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 20px;
            margin-top: 20px;
        }

        .demo-card h6 {
            font-weight: 600;
            color: #495057;
            margin-bottom: 15px;
        }

        .demo-account {
            background: white;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            padding: 12px 15px;
            margin-bottom: 10px;
            font-size: 0.9rem;
        }

        .demo-account .badge {
            float: right;
        }

        .shield-icon {
            font-size: 2.5rem;
            margin-bottom: 10px;
        }

        .invalid-feedback {
            font-weight: 400;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <div class="shield-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h4>P2DF Forensic System</h4>
                <p>Privacy-Preserving Digital Forensics</p>
            </div>

            <div class="login-body">
                <form method="POST" action="{{ route('login.submit') }}">
                    @csrf
                    
                    <div class="form-group">
                        <label for="email"><i class="fas fa-envelope"></i> Email Address</label>
                        <div class="input-icon">
                            <i class="fas fa-user"></i>
                            <input type="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   id="email" 
                                   name="email" 
                                   value="{{ old('email') }}" 
                                   required 
                                   autofocus 
                                   placeholder="Enter your email">
                            @error('email')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <div class="input-icon">
                            <i class="fas fa-key"></i>
                            <input type="password" 
                                   class="form-control @error('password') is-invalid @enderror" 
                                   id="password" 
                                   name="password" 
                                   required 
                                   placeholder="Enter your password">
                            @error('password')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="remember" name="remember">
                            <label class="custom-control-label" for="remember">Remember me</label>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-login">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
            </div>
        </div>

        <!-- Demo accounts -->
        <div class="demo-card">
            <h6><i class="fas fa-info-circle"></i> Demo Accounts</h6>
            <div class="demo-account">
                <strong>Admin Account</strong>
                <span class="badge badge-danger">ADMIN</span>
                <br>
                <small class="text-muted">admin@example.com / password</small>
            </div>
            <div class="demo-account">
                <strong>Investigator 1</strong>
                <span class="badge badge-info">INVESTIGATOR</span>
                <br>
                <small class="text-muted">inv1@example.com / password</small>
            </div>
            <div class="demo-account">
                <strong>Investigator 2</strong>
                <span class="badge badge-info">INVESTIGATOR</span>
                <br>
                <small class="text-muted">inv2@example.com / password</small>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
