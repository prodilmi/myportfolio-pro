<?php
/**
 * Dashboard Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

// Get user's portfolios
$portfolios = $db->findAll('portfolios', ['user_id' => $user['id']]);

// Calculate totals
$total_invested = 0;
$total_value = 0;
foreach ($portfolios as $portfolio) {
    $total_invested += $portfolio['total_invested'];
    $total_value += $portfolio['current_value'];
}

$total_gain = $total_value - $total_invested;
$gain_percent = $total_invested > 0 ? ($total_gain / $total_invested) * 100 : 0;

// Get recent trades
$recent_trades = $db->findAll('trades', [], ['order_by' => 'trade_date DESC', 'limit' => 5]);

// Get unread notifications count
$unread_notifications = $db->findAll('notifications', ['user_id' => $user['id'], 'read' => false]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MyPortfolioPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js@3.7.1/dist/chart.min.js"></script>
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <!-- Welcome Section -->
        <div class="row mb-4">
            <div class="col-12">
                <h1>Welcome back, <?php echo htmlspecialchars($user['first_name']); ?>!</h1>
                <p class="text-muted">Here's an overview of your portfolio performance</p>
            </div>
        </div>
        
        <!-- Key Metrics -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Total Invested</h6>
                    <h3>$<?php echo number_format($total_invested, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Current Value</h6>
                    <h3>$<?php echo number_format($total_value, 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Total Gain/Loss</h6>
                    <h3 class="<?php echo $total_gain >= 0 ? 'text-success' : 'text-danger'; ?>">
                        $<?php echo number_format($total_gain, 2); ?>
                    </h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Return %</h6>
                    <h3 class="<?php echo $gain_percent >= 0 ? 'text-success' : 'text-danger'; ?>">
                        <?php echo number_format($gain_percent, 2); ?>%
                    </h3>
                </div>
            </div>
        </div>
        
        <!-- Charts Section -->
        <div class="row mb-4">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Portfolio Allocation</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="allocationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Performance Over Time</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Portfolios & Recent Trades -->
        <div class="row">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Your Portfolios</h5>
                        <a href="<?php echo BASE_URL; ?>index.php?page=portfolios" class="btn btn-sm btn-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($portfolios)): ?>
                            <p class="text-muted">No portfolios yet. <a href="<?php echo BASE_URL; ?>index.php?page=portfolios">Create one</a></p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($portfolios, 0, 5) as $portfolio): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1"><?php echo htmlspecialchars($portfolio['name']); ?></h6>
                                            <small class="text-muted">$<?php echo number_format($portfolio['current_value'], 2); ?></small>
                                        </div>
                                        <a href="<?php echo BASE_URL; ?>index.php?page=portfolio-detail&id=<?php echo $portfolio['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Notifications</h5>
                        <?php if (count($unread_notifications) > 0): ?>
                            <span class="badge bg-primary"><?php echo count($unread_notifications); ?></span>
                        <?php endif; ?>
                    </div>
                    <div class="card-body">
                        <?php if (empty($unread_notifications)): ?>
                            <p class="text-muted">No unread notifications</p>
                        <?php else: ?>
                            <div class="list-group list-group-flush">
                                <?php foreach (array_slice($unread_notifications, 0, 5) as $notif): ?>
                                    <div class="list-group-item">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($notif['title']); ?></h6>
                                        <small class="text-muted"><?php echo htmlspecialchars($notif['message']); ?></small>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <a href="<?php echo BASE_URL; ?>index.php?page=notifications" class="btn btn-sm btn-outline-primary mt-3">View All</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>js/dashboard.js"></script>
</body>
</html>
