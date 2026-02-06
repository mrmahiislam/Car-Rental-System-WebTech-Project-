<?php
session_start();

// Direct database connection since you don't use a separate file
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "car_rental";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_GET['id'])) {
    $car_id = $_GET['id'];

    // Get the image path first
    $query = "SELECT image_path FROM cars WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $car_id);
    $stmt->execute();
    $stmt->bind_result($image_path);
    $stmt->fetch();
    $stmt->close();

    // Delete the image file if it exists
    if (!empty($image_path)) {
        $file = 'Uploads/' . $image_path;
        if (file_exists($file)) {
            unlink($file);
        }
    }

    // Now delete the car record
    $delete = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $delete->bind_param("i", $car_id);

    if ($delete->execute()) {
        $_SESSION['message'] = "Car and image deleted successfully.";
    } else {
        $_SESSION['message'] = "Failed to delete car.";
    }

    $delete->close();
} else {
    $_SESSION['message'] = "No car ID provided.";
}

$conn->close();
header("Location: employee_dashboard.php");
exit();
?>
