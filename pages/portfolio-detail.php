<?php
/**
 * Portfolio Detail Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

$portfolio_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$portfolio = $db->findOne('portfolios', ['id' => $portfolio_id, 'user_id' => $user['id']]);

if (!$portfolio) {
    header('Location: ' . BASE_URL . 'index.php?page=portfolios');
    exit;
}

$holdings = $db->findAll('holdings', ['portfolio_id' => $portfolio_id]);
$trades = $db->findAll('trades', ['portfolio_id' => $portfolio_id], ['order_by' => 'trade_date DESC', 'limit' => 10]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($portfolio['name']); ?> - MyPortfolioPro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <!-- Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <a href="<?php echo BASE_URL; ?>index.php?page=portfolios" class="btn btn-sm btn-outline-secondary">&larr; Back</a>
                        <h1 class="mt-3"><?php echo htmlspecialchars($portfolio['name']); ?></h1>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addTradeModal">+ Add Trade</button>
                </div>
            </div>
        </div>
        
        <!-- Portfolio Stats -->
        <div class="row mb-4">
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Total Invested</h6>
                    <h3>$<?php echo number_format($portfolio['total_invested'], 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Current Value</h6>
                    <h3>$<?php echo number_format($portfolio['current_value'], 2); ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Holdings</h6>
                    <h3><?php echo count($holdings); ?></h3>
                </div>
            </div>
            <div class="col-md-3 mb-3">
                <div class="card dashboard-stat">
                    <h6>Total Trades</h6>
                    <h3><?php echo count($trades); ?></h3>
                </div>
            </div>
        </div>
        
        <!-- Holdings Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Holdings</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($holdings)): ?>
                            <p class="text-muted">No holdings in this portfolio yet.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Symbol</th>
                                            <th>Quantity</th>
                                            <th>Avg Cost</th>
                                            <th>Current Price</th>
                                            <th>Current Value</th>
                                            <th>Gain/Loss</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($holdings as $holding): ?>
                                            <tr>
                                                <td><strong><?php echo htmlspecialchars($holding['symbol']); ?></strong></td>
                                                <td><?php echo number_format($holding['quantity'], 4); ?></td>
                                                <td>$<?php echo number_format($holding['average_cost'], 2); ?></td>
                                                <td>$<?php echo number_format($holding['current_price'], 2); ?></td>
                                                <td>$<?php echo number_format($holding['quantity'] * $holding['current_price'], 2); ?></td>
                                                <td>
                                                    <?php 
                                                    $value = $holding['quantity'] * $holding['current_price'];
                                                    $cost = $holding['quantity'] * $holding['average_cost'];
                                                    $gain = $value - $cost;
                                                    $class = $gain >= 0 ? 'text-success' : 'text-danger';
                                                    ?>
                                                    <span class="<?php echo $class; ?>">$<?php echo number_format($gain, 2); ?></span>
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
        
        <!-- Recent Trades -->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Recent Trades</h5>
                        <a href="<?php echo BASE_URL; ?>index.php?page=trading-history&portfolio_id=<?php echo $portfolio_id; ?>" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <?php if (empty($trades)): ?>
                            <p class="text-muted">No trades yet.</p>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($trades as $trade): ?>
                                            <tr>
                                                <td><?php echo date('Y-m-d', strtotime($trade['trade_date'])); ?></td>
                                                <td><?php echo htmlspecialchars($trade['symbol']); ?></td>
                                                <td><span class="badge badge-<?php echo $trade['type'] === 'BUY' ? 'info' : 'warning'; ?>"><?php echo $trade['type']; ?></span></td>
                                                <td><?php echo number_format($trade['quantity'], 4); ?></td>
                                                <td>$<?php echo number_format($trade['price'], 2); ?></td>
                                                <td>$<?php echo number_format($trade['quantity'] * $trade['price'], 2); ?></td>
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
                            <input type="number" class="form-control" name="quantity" step="0.0001" required>
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
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById('tradeForm').addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(this);
        const data = {
            portfolio_id: parseInt(formData.get('portfolio_id')),
            symbol: formData.get('symbol').toUpperCase(),
            type: formData.get('type'),
            quantity: parseFloat(formData.get('quantity')),
            price: parseFloat(formData.get('price')),
            fees: parseFloat(formData.get('fees') || 0),
            trade_date: formData.get('trade_date')
        };
        
        try {
            const response = await fetch('/api/trades.php?action=create', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(data)
            });
            const result = await response.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.error || 'Error adding trade');
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Error adding trade');
        }
    });
    
    // Set today's date as default
    document.querySelector('input[name="trade_date"]').value = new Date().toISOString().split('T')[0];
    </script>
</body>
</html>
