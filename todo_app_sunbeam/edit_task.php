<?php
require_once 'config.php';
requireLogin();

$task_id = $_GET['id'] ?? 0;
$user_id = getCurrentUserId();

$conn = getDBConnection();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = trim($_POST['task_name']);
    
    if (!empty($new_name)) {
        // Get old task name for comparison
        $stmt = mysqli_prepare($conn, "SELECT name FROM tasks WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_bind_result($stmt, $old_name);
        mysqli_stmt_fetch($stmt);
        mysqli_stmt_close($stmt);
        
        // Update the task
        $stmt = mysqli_prepare($conn, 
            "UPDATE tasks SET name = ? WHERE id = ? AND user_id = ?");
        mysqli_stmt_bind_param($stmt, "sii", $new_name, $task_id, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            if ($old_name !== $new_name) {
                flashMessage("Task updated from '{$old_name}' to '{$new_name}'!", 'success');
            } else {
                flashMessage("Task saved!", 'success');
            }
        } else {
            flashMessage("Failed to update task.", 'error');
        }
        mysqli_stmt_close($stmt);
        
        redirect('index.php');
    }
}

// Get task details
$stmt = mysqli_prepare($conn, "SELECT * FROM tasks WHERE id = ? AND user_id = ?");
mysqli_stmt_bind_param($stmt, "ii", $task_id, $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$task = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$task) {
    flashMessage("Task not found or access denied.", 'error');
    redirect('index.php');
}

$title = "Edit Task";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">Edit Task</h4>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label for="task_name" class="form-label">Task Description</label>
                        <input type="text" class="form-control" id="task_name" name="task_name" 
                               value="<?php echo htmlspecialchars($task['name']); ?>" required>
                    </div>
                    <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                        <a href="index.php" class="btn btn-secondary me-md-2">
                            <i class="bi bi-arrow-left"></i> Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require 'base.html.php';
?>
