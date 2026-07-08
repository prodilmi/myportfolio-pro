<?php
/**
 * Trading History Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

$portfolio_id = isset($_GET['portfolio_id']) ? (int)$_GET['portfolio_id'] : 0;
$portfolio = $db->findOne('portfolios', ['id' => $portfolio_id, 'user_id' => $user['id']]);

if (!$portfolio) {
    header('Location: ' . BASE_URL . 'index.php?page=dashboard');
    exit;
}

$trades = $db->findAll('trades', ['portfolio_id' => $portfolio_id], ['order_by' => 'trade_date DESC']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trading History - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1><?php echo htmlspecialchars($portfolio['name']); ?> - Trading History</h1>
                <p class="text-muted">Track all your trades and transactions</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Total Trades</h6>
                        <h3><?php echo count($trades); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Buy Orders</h6>
                        <h3><?php echo count(array_filter($trades, fn($t) => $t['type'] === 'BUY')); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Sell Orders</h6>
                        <h3><?php echo count(array_filter($trades, fn($t) => $t['type'] === 'SELL')); ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card">
                    <div class="card-body">
                        <h6 class="card-title text-muted">Realized Gains</h6>
                        <h3 class="text-success">$<?php echo number_format(array_sum(array_map(fn($t) => $t['profit_loss'] ?? 0, $trades)), 2); ?></h3>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Trade History</h5>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTradeModal">Add Trade</button>
                    </div>
                    <div class="card-body">
                        <?php if (empty($trades)): ?>
                            <p class="text-muted">No trades recorded yet. Add your first trade to get started!</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Symbol</th>
                                            <th>Type</th>
                                            <th>Quantity</th>
                                            <th>Price</th>
                                            <th>Total</th>
                                            <th>Fees</th>
                                            <th>Profit/Loss</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($trades as $trade): ?>
                                            <tr>
                                                <td><?php echo date('Y-m-d', strtotime($trade['trade_date'])); ?></td>
                                                <td><strong><?php echo htmlspecialchars($trade['symbol']); ?></strong></td>
                                                <td>
                                                    <span class="badge badge-<?php echo $trade['type'] === 'BUY' ? 'info' : 'warning'; ?>">
                                                        <?php echo $trade['type']; ?>
                                                    </span>
                                                </td>
                                                <td><?php echo number_format($trade['quantity'], 2); ?></td>
                                                <td>$<?php echo number_format($trade['price'], 2); ?></td>
                                                <td>$<?php echo number_format($trade['quantity'] * $trade['price'], 2); ?></td>
                                                <td>$<?php echo number_format($trade['fees'] ?? 0, 2); ?></td>
                                                <td>
                                                    <?php 
                                                    $pl = $trade['profit_loss'] ?? 0;
                                                    $class = $pl >= 0 ? 'text-success' : 'text-danger';
                                                    ?>
                                                    <span class="<?php echo $class; ?>">$<?php echo number_format($pl, 2); ?></span>
                                                </td>
                                                <td>
                                                    <button class="btn btn-sm btn-outline-primary" data-trade-id="<?php echo $trade['id']; ?>">Edit</button>
                                                    <button class="btn btn-sm btn-outline-danger" data-trade-id="<?php echo $trade['id']; ?>">Delete</button>
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
    
    <!-- Add Trade Modal -->
    <div class="modal fade" id="addTradeModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Trade</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="tradeForm">
                    <div class="modal-body">
                        <input type="hidden" name="portfolio_id" value="<?php echo $portfolio_id; ?>">
                        <div class="mb-3">
                            <label class="form-label">Symbol</label>
                            <input type="text" class="form-control" name="symbol" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Type</label>
                            <select class="form-control" name="type" required>
                                <option value="BUY">Buy</option>
                                <option value="SELL">Sell</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Price</label>
                            <input type="number" class="form-control" name="price" step="0.01" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Fees</label>
                            <input type="number" class="form-control" name="fees" step="0.01" value="0">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Trade Date</label>
                            <input type="date" class="form-control" name="trade_date" required>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Trade</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>js/trading-history.js"></script>
</body>
</html>
