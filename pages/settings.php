<?php
/**
 * Settings Page
 */

use App\Core\Auth;

$auth = new Auth($db);
$user = $auth->getUser();

if (!$user) {
    header('Location: ' . BASE_URL . 'index.php?page=login');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Settings</h1>
                <p class="text-muted">Manage your account and preferences</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-lg-3 mb-4">
                <div class="list-group">
                    <a href="#account" class="list-group-item list-group-item-action active" data-bs-toggle="list">Account</a>
                    <a href="#preferences" class="list-group-item list-group-item-action" data-bs-toggle="list">Preferences</a>
                    <a href="#security" class="list-group-item list-group-item-action" data-bs-toggle="list">Security</a>
                </div>
            </div>
            <div class="col-lg-9">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Settings Content</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Settings features coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
