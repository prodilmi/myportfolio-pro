<?php
/**
 * Login Page
 */

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

$auth = new Auth($db);
$response = new Response();
error = '';

if (Request::isPost()) {
    $username = Request::getPost('username');
    $password = Request::getPost('password');
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        if ($auth->login($username, $password)) {
            $response->redirect(BASE_URL . 'index.php?page=dashboard');
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-5">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">MyPortfolioPro</h2>
                        <h5 class="text-center text-muted mb-4">Portfolio Management System</h5>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Login</button>
                        </form>
                        
                        <hr class="my-4">
                        <p class="text-center text-muted">
                            Don't have an account? <a href="<?php echo BASE_URL; ?>index.php?page=register">Register here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
