<?php
/**
 * Register Page
 */

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;

$auth = new Auth($db);
$response = new Response();
$error = '';
$success = '';

if (Request::isPost()) {
    $username = Request::getPost('username');
    $email = Request::getPost('email');
    $password = Request::getPost('password');
    $password_confirm = Request::getPost('password_confirm');
    $first_name = Request::getPost('first_name');
    $last_name = Request::getPost('last_name');
    
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'Username, email, and password are required';
    } elseif ($password !== $password_confirm) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters';
    } else {
        $result = $auth->register($username, $email, $password, $first_name, $last_name);
        if ($result === true) {
            $success = 'Registration successful! You can now login.';
        } else {
            $error = $result;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MyPortfolioPro</title>
    <link href="<?php echo ASSETS_URL; ?>css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo ASSETS_URL; ?>css/style.css" rel="stylesheet">
</head>
<body class="auth-page">
    <div class="container">
        <div class="row justify-content-center align-items-center min-vh-100">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-body p-5">
                        <h2 class="text-center mb-4">MyPortfolioPro</h2>
                        <h5 class="text-center text-muted mb-4">Create Your Account</h5>
                        
                        <?php if ($error): ?>
                            <div class="alert alert-danger" role="alert">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                            <div class="alert alert-success" role="alert">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="first_name" class="form-label">First Name</label>
                                    <input type="text" class="form-control" id="first_name" name="first_name">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="last_name" class="form-label">Last Name</label>
                                    <input type="text" class="form-control" id="last_name" name="last_name">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email" required>
                            </div>
                            <div class="mb-3">
                                <label for="password" class="form-label">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                                <small class="text-muted">Minimum 8 characters</small>
                            </div>
                            <div class="mb-3">
                                <label for="password_confirm" class="form-label">Confirm Password</label>
                                <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Register</button>
                        </form>
                        
                        <hr class="my-4">
                        <p class="text-center text-muted">
                            Already have an account? <a href="<?php echo BASE_URL; ?>index.php?page=login">Login here</a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo ASSETS_URL; ?>js/bootstrap.bundle.min.js"></script>
</body>
</html>
