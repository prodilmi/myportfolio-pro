<?php
/**
 * Portfolio Page
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
    <title>Portfolio - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body>
    <?php include 'components/navbar.php'; ?>
    
    <div class="container-fluid py-4">
        <div class="row mb-4">
            <div class="col-12">
                <h1>Portfolio Management</h1>
                <p class="text-muted">Manage your investment portfolios</p>
            </div>
        </div>
        
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Portfolio Content</h5>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Portfolio management features coming soon...</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'components/footer.php'; ?>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
