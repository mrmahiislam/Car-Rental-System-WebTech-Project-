<?php
session_start();

// Destroy the session and unset everything
session_unset();
session_destroy();

// Also prevent caching of the next page
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

// Redirect to login
header("Location: index.php");
exit();
?>
