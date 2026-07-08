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

// Get user portfolios
$portfolios = $db->findAll('portfolios', ['user_id' => $user['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Welcome, <?php echo htmlspecialchars($user['first_name'] ?? $user['username']); ?>!</h1>
                <p class="text-muted">Your Investment Dashboard</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total Invested</h6>
                        <h3 class="card-text">$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Current Value</h6>
                        <h3 class="card-text">$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total Gain/Loss</h6>
                        <h3 class="card-text">$0.00</h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Return %</h6>
                        <h3 class="card-text">0.00%</h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Portfolio Allocation</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="allocationChart"></canvas>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 mb-4">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Performance</h5>
                    </div>
                    <div class="card-body">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Your Portfolios</h5>
                        <a href="<?php echo BASE_URL; ?>index.php?page=portfolio&action=create" class="btn btn-sm btn-primary">Add Portfolio</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($portfolios)): ?>
                            <p class="text-muted">No portfolios yet. Create one to get started!</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>Currency</th>
                                            <th>Total Invested</th>
                                            <th>Current Value</th>
                                            <th>Gain/Loss</th>
                                            <th>Return %</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($portfolios as $portfolio): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($portfolio['name']); ?></td>
                                                <td><?php echo htmlspecialchars($portfolio['currency']); ?></td>
                                                <td>$<?php echo number_format($portfolio['total_invested'], 2); ?></td>
                                                <td>$<?php echo number_format($portfolio['current_value'], 2); ?></td>
                                                <td>$<?php echo number_format($portfolio['current_value'] - $portfolio['total_invested'], 2); ?></td>
                                                <td><?php echo number_format((($portfolio['current_value'] - $portfolio['total_invested']) / $portfolio['total_invested']) * 100, 2); ?>%</td>
                                                <td>
                                                    <a href="<?php echo BASE_URL; ?>index.php?page=portfolio&id=<?php echo $portfolio['id']; ?>" class="btn btn-sm btn-outline-primary">View</a>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>js/dashboard.js"></script>
</body>
</html>
