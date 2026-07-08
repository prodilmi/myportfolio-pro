<?php
/**
 * Home Page
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyPortfolioPro - Smart Portfolio Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">Manage Your Investments Smarter</h1>
                    <p class="lead mb-4">Track, analyze, and optimize your investment portfolio in real-time. Connect your brokers and get instant insights into your portfolio performance.</p>
                    <div class="d-flex gap-3">
                        <a href="<?php echo BASE_URL; ?>index.php?page=register" class="btn btn-light btn-lg">Get Started Free</a>
                        <a href="#features" class="btn btn-outline-light btn-lg">Learn More</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div style="background: rgba(255,255,255,0.1); border-radius: 10px; padding: 30px; height: 400px;">
                        <p class="text-center text-white mt-5">Dashboard Preview</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- Features Section -->
    <section class="features-section py-5" id="features">
        <div class="container">
            <h2 class="text-center mb-5">Powerful Features</h2>
            <div class="row">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📊</div>
                            <h5 class="card-title">Real-time Analytics</h5>
                            <p class="card-text">Get instant insights into your portfolio performance with advanced charts and metrics.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔗</div>
                            <h5 class="card-title">Broker Integration</h5>
                            <p class="card-text">Connect all your brokers in one place. Supports Interactive Brokers, TD Ameritrade, Fidelity, and more.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🔔</div>
                            <h5 class="card-title">Smart Alerts</h5>
                            <p class="card-text">Get notified about price changes, portfolio milestones, and market opportunities.</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📈</div>
                            <h5 class="card-title">Performance Tracking</h5>
                            <p class="card-text">Track your gains and losses with detailed performance reports and tax-loss harvesting insights.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">🛡️</div>
                            <h5 class="card-title">Bank-Level Security</h5>
                            <p class="card-text">Your data is encrypted with AES-256 and never stored in plain text.</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <div class="card-body">
                            <div style="font-size: 2.5rem; margin-bottom: 1rem;">📱</div>
                            <h5 class="card-title">Mobile Ready</h5>
                            <p class="card-text">Access your portfolio anytime, anywhere with our responsive design.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 80px 0;">
        <div class="container text-center">
            <h2 class="mb-4">Ready to Master Your Investments?</h2>
            <p class="lead mb-4">Start tracking your portfolio today. It's free and takes less than 5 minutes.</p>
            <a href="<?php echo BASE_URL; ?>index.php?page=register" class="btn btn-light btn-lg">Sign Up Now</a>
        </div>
    </section>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
