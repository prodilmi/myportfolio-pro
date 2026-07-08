<?php
/**
 * Broker Integration Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

$brokers = $db->findAll('broker_connections', ['user_id' => $user['id']]);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Broker Integration - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Connected Brokers</h1>
                <p class="text-muted">Manage your broker connections for seamless portfolio tracking</p>
            </div>
        </div>
        
        <div class="row mb-4">
            <div class="col-12">
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addBrokerModal">+ Add Broker Connection</button>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Your Connected Brokers</h5>
                    </div>
                    <div class="card-body">
                        <?php if (empty($brokers)): ?>
                            <div class="alert alert-info">
                                <p class="mb-0">No brokers connected yet. Connect a broker to automatically sync your portfolio data.</p>
                            </div>
                        <?php else: ?>
                            <div class="row">
                                <?php foreach ($brokers as $broker): ?>
                                    <div class="col-md-4 mb-4">
                                        <div class="card broker-card">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between align-items-start mb-3">
                                                    <h5 class="card-title"><?php echo htmlspecialchars($broker['broker_name']); ?></h5>
                                                    <span class="badge badge-success">Connected</span>
                                                </div>
                                                <p class="card-text text-muted">
                                                    <strong>Account:</strong> <?php echo htmlspecialchars($broker['account_number']); ?>
                                                </p>
                                                <p class="card-text text-muted">
                                                    <strong>Connected:</strong> <?php echo date('Y-m-d', strtotime($broker['connected_at'])); ?>
                                                </p>
                                                <p class="card-text text-muted">
                                                    <strong>Last Sync:</strong> <?php echo $broker['last_sync'] ? date('Y-m-d H:i', strtotime($broker['last_sync'])) : 'Never'; ?>
                                                </p>
                                                <div class="mt-4">
                                                    <button class="btn btn-sm btn-primary" data-broker-id="<?php echo $broker['id']; ?>">Sync Now</button>
                                                    <button class="btn btn-sm btn-outline-danger" data-broker-id="<?php echo $broker['id']; ?>">Disconnect</button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h3 class="mb-3">Supported Brokers</h3>
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>Interactive Brokers</h6>
                                <button class="btn btn-sm btn-outline-primary mt-3">Connect</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>TD Ameritrade</h6>
                                <button class="btn btn-sm btn-outline-primary mt-3">Connect</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>Fidelity</h6>
                                <button class="btn btn-sm btn-outline-primary mt-3">Connect</button>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card text-center">
                            <div class="card-body">
                                <h6>E*TRADE</h6>
                                <button class="btn btn-sm btn-outline-primary mt-3">Connect</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add Broker Modal -->
    <div class="modal fade" id="addBrokerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Connect Broker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form id="brokerForm">
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label">Broker</label>
                            <select class="form-control" name="broker_name" required>
                                <option value="">Select a broker...</option>
                                <option value="Interactive Brokers">Interactive Brokers</option>
                                <option value="TD Ameritrade">TD Ameritrade</option>
                                <option value="Fidelity">Fidelity</option>
                                <option value="E*TRADE">E*TRADE</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Account Number</label>
                            <input type="text" class="form-control" name="account_number" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Key</label>
                            <input type="password" class="form-control" name="api_key" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">API Secret</label>
                            <input type="password" class="form-control" name="api_secret" required>
                        </div>
                        <div class="alert alert-info">
                            <small>Your API credentials are encrypted and never shared. We only use them to sync your portfolio data.</small>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Connect Broker</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>js/broker-integration.js"></script>
</body>
</html>
