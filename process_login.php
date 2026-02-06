<?php
session_start();
header('Content-Type: application/json');

// DB connection
$conn = new mysqli('localhost', 'root', '', 'car_rental');

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Database connection failed.']);
    exit;
}

// Sanitize input
$email = trim($_POST['email']);
$password = trim($_POST['password']);
$role = $_POST['role'] ?? '';

if (empty($email) || empty($password) || empty($role)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

// ✅ Admin login (md5 password)
if ($role === 'admin') {
    $stmt = $conn->prepare("SELECT * FROM admins WHERE email = ?");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Admin email not found.']);
        exit;
    }

    $admin = $result->fetch_assoc();
    if (trim($admin['password']) !== md5($password)) {
        echo json_encode(['success' => false, 'message' => 'Incorrect admin password.']);
        exit;
    }

    $_SESSION['admin_id'] = $admin['id'];
    $_SESSION['admin_name'] = $admin['full_name'];
    echo json_encode(['success' => true, 'redirect' => 'admin_dashboard.php']);
    exit;
}

// ✅ Employee login (hashed password)
if ($role === 'employee') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'employee'");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'Employee account not found.']);
        exit;
    }

    $employee = $result->fetch_assoc();
    if (!password_verify($password, $employee['password'])) {
        echo json_encode(['success' => false, 'message' => 'Incorrect employee password.']);
        exit;
    }

    $_SESSION['employee_id'] = $employee['id'];
    $_SESSION['employee_name'] = $employee['name'];
    echo json_encode(['success' => true, 'redirect' => 'employee_dashboard.php']);
    exit;
}

// ✅ User login (hashed password)
// ✅ User login
// ✅ User login (hashed password)
if ($role === 'user') {
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = ? AND role = 'user'");
    $stmt->bind_param('s', $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode(['success' => false, 'message' => 'User account not found.']);
        exit;
    }

    $user = $result->fetch_assoc();

    if (!password_verify($password, $user['password'])) {
        echo json_encode(['success' => false, 'message' => 'Incorrect user password.']);
        exit;
    }

    $_SESSION['user_id'] = $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['role'] = 'User'; // ✅ This is what was missing
    echo json_encode(['success' => true, 'redirect' => 'user_dashboard.php']);
    exit;
}



// ❌ Invalid role
echo json_encode(['success' => false, 'message' => 'Invalid role or unsupported login type.']);
exit;
