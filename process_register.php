<?php
header('Content-Type: application/json');

// DB config
$host = "localhost";
$user = "root";
$password = "";
$database = "car_rental";

// Connect to DB
$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'errors' => ["Database connection failed."]]);
    exit;
}

// Read POST data
$name     = trim($_POST['name']);
$email    = trim($_POST['email']);
$phone    = trim($_POST['phone']);
$dob      = $_POST['dob'];
$city     = trim($_POST['city']);
$password = $_POST['password'];
$role     = $_POST['role'];

// Basic server-side validation
$errors = [];

if (!$name || !$email || !$phone || !$dob || !$city || !$password || !$role) {
    $errors[] = "All fields are required.";
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors[] = "Invalid email format.";
}

if (!preg_match("/^(013|014|015|016|017|018|019)[0-9]{8}$/", $phone)) {
    $errors[] = "Invalid phone number.";
}

if (strlen($password) < 8) {
    $errors[] = "Password must be at least 8 characters.";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Prepare SQL
$stmt = $conn->prepare("INSERT INTO users (name, email, phone, dob, city, password, role) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $name, $email, $phone, $dob, $city, $hashedPassword, $role);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'errors' => ["Database insert failed: " . $stmt->error]]);
}

$stmt->close();
$conn->close();
?>
