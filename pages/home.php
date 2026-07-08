<?php
/**
 * Home Page
 */

use App\Core\Auth;

$auth = new Auth($db);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPortfolioPro - Professional Portfolio Management</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="hero-section">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Professional Portfolio Management</h1>
                    <p class="lead mb-4">Track, manage, and analyze your investment portfolio with ease. Monitor multiple brokers, analyze performance, and make informed decisions.</p>
                    <div>
                        <?php if ($auth->isAuthenticated()): ?>
                            <a href="<?php echo BASE_URL; ?>index.php?page=dashboard" class="btn btn-primary btn-lg">Go to Dashboard</a>
                        <?php else: ?>
                            <a href="<?php echo BASE_URL; ?>index.php?page=login" class="btn btn-primary btn-lg me-3">Login</a>
                            <a href="<?php echo BASE_URL; ?>index.php?page=register" class="btn btn-outline-primary btn-lg">Register</a>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-lg-6">
                    <img src="<?php echo ASSETS_URL; ?>img/dashboard-preview.png" alt="Dashboard Preview" class="img-fluid rounded-lg shadow">
                </div>
            </div>
        </div>
    </div>
    
    <div class="features-section py-5">
        <div class="container">
            <h2 class="text-center mb-5">Key Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-pie fa-3x text-primary mb-3"></i>
                            <h5 class="card-title">Portfolio Management</h5>
                            <p class="card-text">Manage multiple portfolios across different brokers with ease.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-chart-line fa-3x text-success mb-3"></i>
                            <h5 class="card-title">Performance Analytics</h5>
                            <p class="card-text">Track performance metrics and analyze your investment gains.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body text-center">
                            <i class="fas fa-lock fa-3x text-warning mb-3"></i>
                            <h5 class="card-title">Secure & Private</h5>
                            <p class="card-text">Your data is encrypted and secure. We never share your information.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
