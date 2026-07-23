<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'todo_user');
define('DB_PASS', 'password');
define('DB_NAME', 'mytododb');

// Session configuration
session_start();

// Helper functions
function isPasswordComplex($password) {
    return (strlen($password) >= 8 && 
            preg_match('/[A-Z]/', $password) &&
            preg_match('/[0-9]/', $password));
}

function getDBConnection() {
    static $conn = null;
    if ($conn === null) {
        $conn = mysqli_connect(DB_HOST, DB_USER, DB_PASS, DB_NAME);
        if (!$conn) {
            die("Connection failed: " . mysqli_connect_error());
        }
        mysqli_set_charset($conn, "utf8mb4");
    }
    return $conn;
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function flashMessage($message, $type = 'success') {
    if (!isset($_SESSION['flash_messages'])) {
        $_SESSION['flash_messages'] = [];
    }
    $_SESSION['flash_messages'][] = ['message' => $message, 'type' => $type];
}

function getFlashMessages() {
    if (isset($_SESSION['flash_messages'])) {
        $messages = $_SESSION['flash_messages'];
        unset($_SESSION['flash_messages']);
        return $messages;
    }
    return [];
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        flashMessage("Please log in to access this page.", 'error');
        redirect('login.php');
    }
}

function getCurrentUserId() {
    return $_SESSION['user_id'] ?? null;
}

function getCurrentUsername() {
    return $_SESSION['username'] ?? null;
}
?>
