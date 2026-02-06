<?php
session_start();
include 'db_connect.php'; // Your DB connection file

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = md5(trim($_POST['password'])); // Must match how you stored it

    $stmt = $conn->prepare("SELECT id, full_name FROM admins WHERE email = ? AND password = ?");
    $stmt->bind_param("ss", $email, $password);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows === 1) {
        $stmt->bind_result($admin_id, $full_name);
        $stmt->fetch();

        $_SESSION['admin_id'] = $admin_id;
        $_SESSION['admin_name'] = $full_name;

        header("Location: admin_dashboard.php");
        exit();
    } else {
        $_SESSION['error'] = "Invalid admin credentials.";
        header("Location: admin_login.php");
        exit();
    }
} else {
    header("Location: admin_login.php");
    exit();
}
?>
