<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>License Status - Product Access</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
        }
        .license-container {
            max-width: 600px;
            width: 100%;
            background: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.05);
            padding: 40px 30px;
        }
        .status-icon {
            font-size: 4rem;
            margin-bottom: 20px;
        }
        .contact-info {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-top: 25px;
        }
        .btn-contact {
            border-radius: 50px;
            padding: 10px 25px;
        }
    </style>
</head>
<body>
    <div class="license-container text-center">
        <!-- Status Icon -->
        <div class="status-icon text-warning">
            <i class="bi bi-exclamation-triangle-fill"></i>
        </div>
        
        <!-- Main Message -->
        <h1 class="h3 mb-3">License Inactive</h1>
        <p class="text-muted mb-4">Your product license has expired or is not registered. Please renew your license to continue using all features.</p>
        
        <!-- Contact Information -->
        <div class="contact-info">
            <h5 class="mb-3">Need Assistance?</h5>
            <p class="mb-3">Contact our support team to reactivate your license:</p>
            
            <div class="row justify-content-center mb-3">
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-envelope me-2 text-primary"></i>
                        <span>info.growwth@gmail.com</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-envelope me-2 text-primary"></i>
                        <span>growwthapps@gmail.com</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-telephone me-2 text-primary"></i>
                        <span>+91 8849714934</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-telephone me-2 text-primary"></i>
                        <span>+91 7016650713</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-telephone me-2 text-primary"></i>
                        <span>+91 9099585444</span>
                    </div>
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-telephone me-2 text-primary"></i>
                        <span>+91 9727568083</span>
                    </div>
                </div>
            </div>
            
            <a href="mailto:info.growwth@gmail.com" class="btn btn-primary btn-contact mt-2">
                <i class="bi bi-envelope me-2"></i>Contact Support
            </a>
        </div>
        
        <!-- Additional Info -->
        <p class="text-muted small mt-4">
            If you believe this is an error, please contact our support team with your license details.
        </p>
    </div>

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css">
    <!-- Bootstrap JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>