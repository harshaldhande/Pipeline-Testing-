<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    $conn = getDBConnection();
    $stmt = mysqli_prepare($conn, "SELECT id, username, password_hash FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);
    mysqli_stmt_close($stmt);
    
    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['welcome_message'] = "Welcome back, " . htmlspecialchars($user['username']) . "!";
        redirect('index.php');
    } else {
        flashMessage("Invalid username or password. Please try again.", 'error');
    }
}

$title = "Login";
ob_start();
?>
<style>
    /* Additional styles for centering */
    .login-container {
        display: flex;
        align-items: center;
        justify-content: center;
        min-height: calc(100vh - 150px); /* Account for header and footer */
    }
    
    .login-card {
        width: 100%;
        max-width: 400px;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        border: none;
    }
    
    .login-header {
        border-radius: 10px 10px 0 0 !important;
        padding: 1.2rem 1.5rem;
    }
    
    .login-body {
        padding: 1.5rem;
    }
</style>

<div class="login-container">
    <div class="login-card card">
        <div class="card-header login-header bg-primary text-white">
            <h4 class="mb-0 text-center"><i class="bi bi-box-arrow-in-right me-2"></i>Login</h4>
        </div>
        <div class="card-body login-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required 
                               placeholder="Enter your username">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Enter your password">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                    <i class="bi bi-box-arrow-in-right me-2"></i> Login
                </button>
            </form>
            <div class="text-center mt-3">
                <p class="mb-0">Don't have an account? 
                    <a href="register.php" class="text-decoration-none fw-semibold">Register here</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require 'base.html.php';
?>
