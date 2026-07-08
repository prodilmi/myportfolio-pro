<?php
/**
 * Notifications Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}

$notifications = $db->findAll('notifications', ['user_id' => $user['id']], ['order_by' => 'created_at DESC', 'limit' => 50]);
$unread_count = count(array_filter($notifications, fn($n) => !$n['read']));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h1>Notifications</h1>
                        <p class="text-muted">Stay updated with your portfolio</p>
                    </div>
                    <div>
                        <button class="btn btn-sm btn-outline-secondary" id="markAllRead">Mark all as read</button>
                    </div>
                </div>
            </div>
        </div>
        
        <?php if ($unread_count > 0): ?>
            <div class="row mb-4">
                <div class="col-12">
                    <div class="alert alert-info">
                        You have <strong><?php echo $unread_count; ?></strong> unread notification<?php echo $unread_count !== 1 ? 's' : ''; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        
        <div class="row">
            <div class="col-lg-8">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Recent Notifications</h5>
                    </div>
                    <div class="card-body p-0">
                        <?php if (empty($notifications)): ?>
                            <div class="p-4 text-center text-muted">
                                <p>No notifications yet. You're all caught up!</p>
                            </div>
                        <?php else: ?>
                            <div class="notification-list">
                                <?php foreach ($notifications as $notification): ?>
                                    <div class="notification-item p-4 border-bottom <?php echo !$notification['read'] ? 'bg-light' : ''; ?>" data-notification-id="<?php echo $notification['id']; ?>">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-bold"><?php echo htmlspecialchars($notification['title']); ?></h6>
                                                <p class="mb-2 text-muted"><?php echo htmlspecialchars($notification['message']); ?></p>
                                                <small class="text-muted"><?php echo date('Y-m-d H:i', strtotime($notification['created_at'])); ?></small>
                            </div>
                            <div>
                                <?php if (!$notification['read']): ?>
                                    <span class="badge bg-primary">New</span>
                                <?php endif; ?>
                                <button class="btn btn-sm btn-link" data-notification-id="<?php echo $notification['id']; ?>" onclick="deleteNotification(<?php echo $notification['id']; ?>)">Delete</button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Notification Settings</h5>
            </div>
            <div class="card-body">
                <form id="notificationSettings">
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="priceAlert" name="price_alert" checked>
                        <label class="form-check-label" for="priceAlert">
                            Price Alerts
                        </label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="performanceAlert" name="performance_alert" checked>
                        <label class="form-check-label" for="performanceAlert">
                            Performance Alerts
                        </label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="portfolioAlert" name="portfolio_alert" checked>
                        <label class="form-check-label" for="portfolioAlert">
                            Portfolio Updates
                        </label>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="newsAlert" name="news_alert" checked>
                        <label class="form-check-label" for="newsAlert">
                            Market News
                        </label>
                    </div>
                    <hr>
                    <div class="mb-3">
                        <label class="form-label">Email Frequency</label>
                        <select class="form-control" name="email_frequency">
                            <option value="instant">Instant</option>
                            <option value="daily">Daily Digest</option>
                            <option value="weekly">Weekly Digest</option>
                            <option value="never">Never</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Save Settings</button>
                </form>
            </div>
        </div>
    </div>
    </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo ASSETS_URL; ?>js/notifications.js"></script>
</body>
</html>
