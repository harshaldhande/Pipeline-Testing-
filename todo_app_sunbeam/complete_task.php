<?php
require_once 'config.php';
requireLogin();

$task_id = $_GET['id'] ?? 0;
$user_id = getCurrentUserId();

$conn = getDBConnection();

// First get task name for notification
$stmt = mysqli_prepare($conn, "SELECT name FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $task_name);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);

// Now update the task
$stmt = mysqli_prepare($conn, 
    "UPDATE tasks SET status = 'Completed', completed_at = NOW() WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);

if (mysqli_stmt_execute($stmt)) {
    flashMessage("Task '{$task_name}' completed successfully!", 'success');
} else {
    flashMessage("Failed to complete task.", 'error');
}
mysqli_stmt_close($stmt);

redirect('index.php');
?>
