<?php
require_once 'config.php';

session_destroy();
flashMessage("You have been logged out.");
redirect('login.php');
?>
