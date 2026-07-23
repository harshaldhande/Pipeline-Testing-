<?php
require_once 'config.php';
requireLogin();

$filter = $_GET['status'] ?? '';
$user_id = getCurrentUserId();

// Build query
$query = "SELECT * FROM tasks WHERE user_id = ?";
$params = [$user_id];
$types = "i";

if ($filter == 'Pending' || $filter == 'Completed') {
    $query .= " AND status = ?";
    $params[] = $filter;
    $types .= "s";
}

$conn = getDBConnection();
$stmt = mysqli_prepare($conn, $query);

if ($stmt) {
    mysqli_stmt_bind_param($stmt, $types, ...$params);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);
    mysqli_stmt_close($stmt);
} else {
    $tasks = [];
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task_name'])) {
    $name = trim($_POST['task_name']);
    $status = $_POST['status'] ?? 'Pending';
    $priority = $_POST['priority'] ?? 'Medium';
    
    if (!empty($name)) {
        $stmt = mysqli_prepare($conn, 
            "INSERT INTO tasks (name, status, priority, user_id) VALUES (?, ?, ?, ?)");
        mysqli_stmt_bind_param($stmt, "sssi", $name, $status, $priority, $user_id);
        
        if (mysqli_stmt_execute($stmt)) {
            flashMessage("Task added successfully!");
        } else {
            flashMessage("Failed to add task.", 'error');
        }
        mysqli_stmt_close($stmt);
        
        redirect('index.php');
    }
}

$title = "My Tasks";
ob_start();
?>
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card mb-3">
            <div class="card-header bg-primary text-white py-2">
                <h5 class="mb-0">Add New Task</h5>
            </div>
            <div class="card-body p-3">
                <form method="POST">
                    <div class="input-group">
                        <input type="text" class="form-control form-control-sm" name="task_name" placeholder="Task description" required>
                        <select class="form-select form-select-sm" name="status" style="max-width: 120px;">
                            <option value="Pending">Pending</option>
                            <option value="Completed">Completed</option>
                        </select>
                        <select class="form-select form-select-sm" name="priority" style="max-width: 120px;">
                            <option value="Low">Low</option>
                            <option value="Medium" selected>Medium</option>
                            <option value="High">High</option>
                        </select>
                        <button class="btn btn-primary btn-sm" type="submit">
                            <i class="bi bi-plus-circle"></i> Add
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center py-2">
                <h5 class="mb-0">My Tasks</h5>
                <div class="filter-btns">
                    <a href="index.php" class="btn btn-sm btn-outline-dark <?php echo empty($filter) ? 'active' : ''; ?>">All</a>
                    <a href="index.php?status=Pending" class="btn btn-sm btn-outline-warning <?php echo $filter == 'Pending' ? 'active' : ''; ?>">Pending</a>
                    <a href="index.php?status=Completed" class="btn btn-sm btn-outline-success <?php echo $filter == 'Completed' ? 'active' : ''; ?>">Completed</a>
                </div>
            </div>
            <div class="card-body p-3">
                <?php if (!empty($tasks)): ?>
                <ul class="list-group list-group-flush">
                    <?php foreach ($tasks as $task): ?>
                    <li class="list-group-item task-item d-flex justify-content-between align-items-center py-2 <?php echo $task['status'] == 'Completed' ? 'completed-task' : ''; ?>">
                        <div>
                            <h6 class="mb-1"><?php echo htmlspecialchars($task['name']); ?></h6>
                            <small class="text-muted">
                                <span class="badge bg-<?php 
                                    echo $task['priority'] == 'High' ? 'danger' : 
                                          ($task['priority'] == 'Medium' ? 'warning' : 'info'); 
                                ?>">
                                    <?php echo $task['priority']; ?>
                                </span>
                                Created: <?php echo date('Y-m-d H:i', strtotime($task['created_at'])); ?>
                                <?php if ($task['completed_at']): ?>
                                | Completed: <?php echo date('Y-m-d H:i', strtotime($task['completed_at'])); ?>
                                <?php endif; ?>
                            </small>
                        </div>
                        <div class="task-actions">
                            <?php if ($task['status'] == 'Pending'): ?>
                            <a href="complete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-success" title="Mark Complete">
                                <i class="bi bi-check-circle"></i>
                            </a>
                            <?php endif; ?>
                            <a href="edit_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-primary" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="delete_task.php?id=<?php echo $task['id']; ?>" class="btn btn-sm btn-danger" title="Delete" onclick="return confirm('Are you sure?')">
                                <i class="bi bi-trash"></i>
                            </a>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php else: ?>
                <div class="text-center py-3">
                    <i class="bi bi-check2-circle" style="font-size: 2rem; color: #6c757d;"></i>
                    <h6 class="mt-2">No tasks found</h6>
                    <p class="mb-0">Add your first task using the form above</p>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?php
$content = ob_get_clean();
require 'base.html.php';
?>
