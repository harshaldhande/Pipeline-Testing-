<?php
require_once 'config.php';

if (isLoggedIn()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!isPasswordComplex($password)) {
        flashMessage("Password must be at least 8 characters with at least one uppercase letter and one number", 'error');
    } else {
        $conn = getDBConnection();
        $hashed_pw = password_hash($password, PASSWORD_DEFAULT);
        
        try {
            $stmt = mysqli_prepare($conn, "INSERT INTO users (username, password_hash) VALUES (?, ?)");
            mysqli_stmt_bind_param($stmt, "ss", $username, $hashed_pw);
            
            if (mysqli_stmt_execute($stmt)) {
                flashMessage("Registration successful! Welcome, {$username}!", 'success');
                redirect('login.php');
            } else {
                if (mysqli_errno($conn) == 1062) { // Duplicate entry
                    flashMessage("Username '{$username}' already exists. Please choose another.", 'error');
                } else {
                    flashMessage("Registration failed. Please try again.", 'error');
                }
            }
            mysqli_stmt_close($stmt);
        } catch (Exception $e) {
            flashMessage("Registration failed. Please try again.", 'error');
        }
    }
}

$title = "Register";
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
    
    .password-hint {
        font-size: 0.85rem;
        color: #6c757d;
    }
</style>

<div class="login-container">
    <div class="login-card card">
        <div class="card-header login-header bg-primary text-white">
            <h4 class="mb-0 text-center"><i class="bi bi-person-plus me-2"></i>Register</h4>
        </div>
        <div class="card-body login-body">
            <form method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control" id="username" name="username" required 
                               placeholder="Choose a username">
                    </div>
                </div>
                <div class="mb-4">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" required 
                               placeholder="Create a password">
                    </div>
                    <div class="password-hint mt-1">
                        <small>
                            <i class="bi bi-info-circle me-1"></i>
                            Must be at least 8 characters with uppercase and number
                        </small>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 py-2 mb-3">
                    <i class="bi bi-person-plus me-2"></i> Register
                </button>
            </form>
            <div class="text-center mt-3">
                <p class="mb-0">Already have an account? 
                    <a href="login.php" class="text-decoration-none fw-semibold">Login here</a>
                </p>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require 'base.html.php';
?>
