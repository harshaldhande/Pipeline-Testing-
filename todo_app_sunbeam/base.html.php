<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Todo App - <?php echo $title ?? 'My Tasks'; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
        }
        
        .container {
            flex: 1 0 auto;
            padding-bottom: 20px;
        }
        
        footer {
            flex-shrink: 0;
            width: 100%;
        }
        
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #4d44db;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        body {
            background-color: #f5f5f5;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }
        
        .task-item {
            transition: background-color 0.2s ease;
        }
        
        .task-item:hover {
            background-color: #f8f9fa;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }
        
        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }
        
        .completed-task {
            text-decoration: line-through;
            color: #6c757d;
        }
        
        .task-actions .btn {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }
        
        .filter-btns .btn {
            margin-right: 5px;
        }
        
        /* Enhanced Logo styles - PROPER SIZE */
        .navbar-logo {
            height: 32px; /* Slightly larger */
            width: auto;
            margin-right: 8px;
            border-radius: 4px;
            object-fit: contain;
            display: block; /* Ensure it's visible */
        }
        
        .brand-container {
            display: flex;
            align-items: center;
            height: 100%;
        }
        
        .brand-text {
            font-size: 1.1rem;
            font-weight: bold;
            color: #fff !important;
            padding-left: 8px;
            border-left: 1px solid rgba(255, 255, 255, 0.3);
        }
        
        /* Make navbar COMPACT - REDUCED HEIGHT */
        .navbar {
            min-height: 45px; /* Reduced from 55px */
            padding: 6px 0; /* Reduced padding */
        }
        
        .navbar-nav .nav-link {
            font-size: 0.9rem; /* Slightly smaller */
            padding: 0.3rem 0.6rem !important; /* Reduced padding */
        }
        
        /* Footer COMPACT */
        footer {
            padding: 8px 0 !important; /* Reduced from 15px */
        }
        
        .footer-text {
            font-size: 0.85rem; /* Slightly smaller */
            margin: 0;
        }
        
        /* Notification System - IMPROVED */
        .notification-container {
            position: fixed;
            top: 65px; /* Adjusted for shorter navbar */
            right: 20px;
            z-index: 9999;
            max-width: 380px;
            width: 100%;
            pointer-events: none;
        }
        
        .notification {
            pointer-events: auto;
            margin-bottom: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
            border-radius: 8px;
            overflow: hidden;
            border-left: 5px solid transparent;
            animation: slideInRight 0.4s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards, fadeOut 0.5s ease 2.7s forwards;
            transform: translateX(120%);
            opacity: 0;
        }
        
        .notification.success {
            border-left-color: #28a745;
            background-color: #28a745;
        }
        
        .notification.danger {
            border-left-color: #dc3545;
            background-color: #dc3545;
        }
        
        .notification.warning {
            border-left-color: #ffc107;
            background-color: #ffc107;
        }
        
        .notification.info {
            border-left-color: #17a2b8;
            background-color: #17a2b8;
        }
        
        .notification.primary {
            border-left-color: var(--primary-color);
            background-color: var(--primary-color);
        }
        
        @keyframes slideInRight {
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(100%);
                margin-bottom: -50px;
                height: 0;
                padding: 0;
                border: 0;
            }
        }
        
        .notification-content {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            color: white;
        }
        
        .notification-message {
            flex-grow: 1;
            margin-right: 12px;
            font-size: 0.95rem;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .notification-icon {
            font-size: 1.2rem;
        }
        
        .notification-close {
            background: rgba(255, 255, 255, 0.2);
            border: none;
            font-size: 1.1rem;
            line-height: 1;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            cursor: pointer;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background-color 0.2s;
            flex-shrink: 0;
        }
        
        .notification-close:hover {
            background: rgba(255, 255, 255, 0.3);
        }
        
        /* Progress bar for notifications */
        .notification-progress {
            height: 4px;
            background: rgba(255, 255, 255, 0.2);
            overflow: hidden;
        }
        
        .notification-progress-bar {
            height: 100%;
            background: rgba(255, 255, 255, 0.8);
            animation: progressBar 3s linear forwards;
            transform-origin: left;
        }
        
        @keyframes progressBar {
            from {
                transform: scaleX(1);
            }
            to {
                transform: scaleX(0);
            }
        }
        
        /* For smaller screens */
        @media (max-width: 768px) {
            .notification-container {
                top: 60px;
                right: 10px;
                left: 10px;
                max-width: none;
            }
            
            .navbar-logo {
                height: 28px;
                margin-right: 6px;
            }
            
            .brand-text {
                font-size: 1rem;
                padding-left: 6px;
            }
            
            .navbar {
                min-height: 40px;
            }
        }
        
        /* Extra small screens */
        @media (max-width: 576px) {
            .notification-container {
                top: 55px;
            }
            
            .navbar-logo {
                height: 24px;
                margin-right: 4px;
            }
            
            .brand-text {
                font-size: 0.9rem;
                padding-left: 4px;
            }
            
            .notification {
                margin-bottom: 8px;
            }
            
            .notification-content {
                padding: 12px 14px;
            }
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-3 py-1"> <!-- Reduced mb and py -->
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <div class="brand-container">
                    <!-- Logo Image from images folder -->
                    <?php if (file_exists('images/sunbeam.jpg')): ?>
                    <img src="images/sunbeam.jpg" 
                         alt="Sunbeam Institute Logo" 
                         class="navbar-logo">
                    <?php elseif (file_exists('images/logo.jpg')): ?>
                    <img src="images/logo.jpg" 
                         alt="Sunbeam Institute Logo" 
                         class="navbar-logo">
                    <?php elseif (file_exists('images/logo.png')): ?>
                    <img src="images/logo.png" 
                         alt="Sunbeam Institute Logo" 
                         class="navbar-logo">
                    <?php else: ?>
                    <!-- Fallback icon if no logo image found -->
                    <i class="bi bi-check2-circle text-white" style="font-size: 1.5rem; margin-right: 8px;"></i>
                    <?php endif; ?>
                    <!-- App Name -->
                    <span class="brand-text">TodoApp</span>
                </div>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <span class="nav-link text-light py-1">Hello, <?php echo htmlspecialchars(getCurrentUsername()); ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1" href="logout.php">Logout</a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link py-1" href="login.php">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link py-1" href="register.php">Register</a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Notification Container -->
    <div class="notification-container" id="notificationContainer"></div>

    <div class="container">
        <?php echo $content ?? ''; ?>
    </div>

    <footer class="mt-auto bg-dark text-white">
        <div class="container text-center py-2"> <!-- Reduced py -->
            <p class="footer-text mb-0">© <?php echo date('Y'); ?> SunBeam Infotech Pvt. Ltd. - TodoApp. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
    // Notification system
    document.addEventListener('DOMContentLoaded', function() {
        const notificationContainer = document.getElementById('notificationContainer');
        let notificationQueue = [];
        let isShowingNotification = false;
        
        // Function to show a notification with icon
        function showNotification(message, type = 'success', icon = null) {
            // Add to queue
            notificationQueue.push({ message, type, icon });
            
            // If not currently showing a notification, show the next one
            if (!isShowingNotification) {
                showNextNotification();
            }
        }
        
        // Function to show the next notification in the queue
        function showNextNotification() {
            if (notificationQueue.length === 0) {
                isShowingNotification = false;
                return;
            }
            
            isShowingNotification = true;
            const { message, type, icon } = notificationQueue.shift();
            
            // Create notification element
            const notification = document.createElement('div');
            notification.className = `notification ${type}`;
            
            // Determine icon based on type if not provided
            let iconToUse = icon;
            if (!iconToUse) {
                switch(type) {
                    case 'success': iconToUse = 'bi-check-circle-fill'; break;
                    case 'danger': iconToUse = 'bi-exclamation-circle-fill'; break;
                    case 'warning': iconToUse = 'bi-exclamation-triangle-fill'; break;
                    case 'info': iconToUse = 'bi-info-circle-fill'; break;
                    default: iconToUse = 'bi-bell-fill'; break;
                }
            }
            
            notification.innerHTML = `
                <div class="notification-content">
                    <div class="notification-message">
                        <i class="bi ${iconToUse} notification-icon"></i>
                        <span>${message}</span>
                    </div>
                    <button class="notification-close" onclick="this.closest('.notification').remove(); checkQueue();">
                        <i class="bi bi-x"></i>
                    </button>
                </div>
                <div class="notification-progress">
                    <div class="notification-progress-bar"></div>
                </div>
            `;
            
            // Add to container
            notificationContainer.appendChild(notification);
            
            // Auto remove after 3 seconds
            setTimeout(() => {
                if (notification.parentNode) {
                    notification.remove();
                }
                showNextNotification();
            }, 3000);
        }
        
        // Make checkQueue available globally for the close button
        window.checkQueue = function() {
            setTimeout(showNextNotification, 100);
        };
        
        // Check for flash messages from PHP
        <?php
        $flashMessages = getFlashMessages();
        if (!empty($flashMessages)) {
            echo "// Flash messages from PHP\n";
            foreach ($flashMessages as $flash) {
                $alertClass = $flash['type'] == 'error' ? 'danger' : 
                             ($flash['type'] == 'warning' ? 'warning' : 
                             ($flash['type'] == 'info' ? 'info' : 'success'));
                $message = addslashes($flash['message']);
                echo "showNotification('{$message}', '{$alertClass}');\n";
            }
        }
        ?>
        
        // Show welcome message if just logged in
        <?php if (isset($_SESSION['welcome_message'])): ?>
        showNotification('<?php echo addslashes($_SESSION['welcome_message']); ?>', 'success', 'bi-person-check');
        <?php unset($_SESSION['welcome_message']); ?>
        <?php endif; ?>
    });
    </script>
</body>
</html>
