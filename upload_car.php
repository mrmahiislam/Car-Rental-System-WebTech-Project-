<?php
session_start();
if (!isset($_SESSION['employee_id'])) {
    die("Unauthorized");
}

$conn = new mysqli("localhost", "root", "", "car_rental");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$car_name = $_POST['car_name'];
$price = $_POST['price'];
$details = $_POST['details'];
$employee_id = $_SESSION['employee_id'];

// Handle image upload
$target_dir = "uploads/";
if (!is_dir($target_dir)) {
    mkdir($target_dir);
}
$image_path = $target_dir . basename($_FILES["car_image"]["name"]);
move_uploaded_file($_FILES["car_image"]["tmp_name"], $image_path);

// Insert into DB
$stmt = $conn->prepare("INSERT INTO rented_cars (employee_id, car_name, price, details, image_path) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("isdss", $employee_id, $car_name, $price, $details, $image_path);
$stmt->execute();
$stmt->close();
$conn->close();

header("Location: employee_dashboard.php");
exit;
?>
