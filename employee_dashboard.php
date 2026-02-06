<?php
session_start();

header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['employee_name'])) {
  header("Location: login.php");
  exit();
}

$conn = new mysqli("localhost", "root", "", "car_rental");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$uploadMessage = '';
$showMessage = false;
$showSuccessScreen = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['car_entry_submit'])) {
  $brand = $_POST['brand'];
  $model = $_POST['model'];
  $seats = intval($_POST['seats']);
  $rent = floatval($_POST['rent']);
  $imagePath = "";

  if (isset($_FILES['car_image']) && $_FILES['car_image']['error'] === 0) {
    $imageName = time() . '_' . basename($_FILES['car_image']['name']);
    $targetDir = "Uploads/";
    if (!is_dir($targetDir)) mkdir($targetDir);
    $targetFile = $targetDir . $imageName;

    if (move_uploaded_file($_FILES['car_image']['tmp_name'], $targetFile)) {
      $imagePath = $targetDir . $imageName;
    }
  }

  $employeeName = $_SESSION['employee_name'];
  $createdAt = date("Y-m-d H:i:s");

  $stmt = $conn->prepare("INSERT INTO cars (employee_name, brand, model, seats, rent_per_hour, image_path, created_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("sssisss", $employeeName, $brand, $model, $seats, $rent, $imagePath, $createdAt);

  if ($stmt->execute()) {
    $showSuccessScreen = true;
  } else {
    $uploadMessage = "Failed to save car details.";
    $showMessage = true;
  }
  $stmt->close();
}

if (isset($_GET['delete']) && is_numeric($_GET['delete'])) {
  $carId = intval($_GET['delete']);
  $employeeName = $_SESSION['employee_name'];
$stmt = $conn->prepare("DELETE FROM cars WHERE id = ? AND employee_name = ?");
$stmt->bind_param("is", $carId, $employeeName);
$stmt->execute();
$stmt->close();

  header("Location: employee_dashboard.php?section=carEntry");
  exit();
}

$employeeName = $_SESSION['employee_name'];
$stmt = $conn->prepare("SELECT * FROM cars WHERE employee_name = ? ORDER BY id DESC");
$stmt->bind_param("s", $employeeName);
$stmt->execute();
$result = $stmt->get_result();
$cars = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Employee Dashboard | GrabIt</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
      font-family: 'Segoe UI', sans-serif;
      background: #f4f4f4;
      display: flex;
      min-height: 100vh;
    }
    .sidebar {
      background: #0b4d91;
      color: white;
      width: 250px;
      transition: width 0.3s ease;
      overflow: hidden;
      position: fixed;
      height: 100vh;
    }
    .sidebar.collapsed { width: 60px; }
    .sidebar-header {
      display: flex;
      align-items: center;
      padding: 20px;
      background: #002244;
      font-size: 20px;
      font-weight: bold;
    }
    .toggle-btn {
      font-size: 24px;
      cursor: pointer;
      margin-right: 10px;
      user-select: none;
    }
    .sidebar.collapsed .brand-text { display: none; }
    .nav-links { list-style: none; padding: 20px 0; }
    .nav-links li {
      padding: 15px 20px;
      cursor: pointer;
      transition: background 0.2s ease;
    }
    .nav-links li:hover { background: #0056b3; }
    .nav-links li span { margin-left: 10px; }
    .sidebar.collapsed .nav-links li span { display: none; }
    .main-content {
      margin-left: 250px;
      padding: 30px;
      transition: margin-left 0.3s ease;
      width: calc(100% - 250px);
    }
    .collapsed + .main-content { margin-left: 60px; width: calc(100% - 60px); }
    .dashboard-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      background: #002244;
      color: white;
      padding: 20px 30px;
      border-radius: 8px;
      margin-bottom: 30px;
    }
    .welcome-area { font-size: 20px; }
    .logout-btn a {
      padding: 10px 25px;
      background: #dc3545;
      color: white;
      text-decoration: none;
      font-size: 16px;
      border-radius: 8px;
      transition: 0.3s;
    }
    .logout-btn a:hover { background: #b02a37; }
    .car-entry-form {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      max-width: 800px;
      margin: auto;
      display: none;
    }
    .car-entry-form input, .car-entry-form label {
      display: block;
      width: 100%;
      margin-bottom: 15px;
      font-size: 16px;
    }


.uploaded-car {
  transition: transform 0.3s ease, box-shadow 0.3s ease;
  cursor: pointer;
}

.uploaded-car:hover {
  transform: scale(1.05);
  box-shadow: 0 6px 18px rgba(0, 0, 0, 0.2);
}

.modal-overlay {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.7);
  display: none;
  justify-content: center;
  align-items: center;
  z-index: 999;
}

.modal-content {
  position: relative;
  padding: 30px;
  background: white;
  border-radius: 12px;
  box-shadow: 0 5px 20px rgba(0,0,0,0.2);
  max-width: 600px;
  width: 90%;
}

.modal-content h3 {
  margin-top: 0;
  color: #333;
}

.modal-content img {
  width: 100%;
  height: auto;
  margin-bottom: 20px;
  border-radius: 10px;
  object-fit: cover;
}

.modal-close {
  position: absolute;
  top: 7px;
  right: 7px;
  background:rgb(255, 23, 27);
  color: #fff;
  border: none;
  padding: 8px 14px;
  font-size: 17px;
  border-radius: 6px;
  cursor: pointer;
  box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
  z-index: 10;
}



    .car-entry-form input[type="submit"] {
      background: #0b4d91;
      color: white;
      border: none;
      padding: 12px;
      border-radius: 6px;
      cursor: pointer;
    }
    .car-entry-form input[type="submit"]:hover {
      background: #083d73;
    }
    .message {
      text-align: center;
      color: green;
      font-weight: bold;
      margin-top: 10px;
    }
    .success-screen {
      text-align: center;
      padding: 40px;
      font-size: 24px;
      font-weight: bold;
      color: green;
    }
    .car-table {
      margin-top: 30px;
      width: 100%;
      overflow-x: auto;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      background: white;
      box-shadow: 0 5px 10px rgba(0,0,0,0.1);
    }
    th, td {
      border: 1px solid #ccc;
      padding: 12px;
      text-align: center;
    }
    th {
      background: #0b4d91;
      color: white;
    }
    td img {
      width: 100px;
      height: 60px;
      object-fit: cover;
      border-radius: 5px;
    }
    .delete-btn {
      background: red;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 5px;
      cursor: pointer;
    }


    @keyframes fadeIn {
  from {opacity: 0; transform: scale(0.9);}
  to {opacity: 1; transform: scale(1);}
}


    @media screen and (max-width: 768px) {
      .main-content {
        margin-left: 60px;
        width: calc(100% - 60px);
      }
    }
  </style>
</head>
<body>

<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
    <span class="brand-text">GrabIt</span>
  </div>
  <ul class="nav-links">
    <li onclick="showSection('dashboard')"><i class="icon">🏠</i><span>Dashboard</span></li>
    <li onclick="showSection('carEntry')"><i class="icon">🚗</i><span>Car Entry</span></li>
    <li onclick="showRentHistory()"><i class="icon">📋</i><span>Rent History</span></li>
  </ul>
</div>

<div class="main-content" id="main">
  <div class="dashboard-header">
    <div class="welcome-area">
      Welcome, <strong><?php echo htmlspecialchars($_SESSION['employee_name']); ?></strong>
    </div>
    <div class="logout-btn">
      <a href="logout.php">Logout</a>
    </div>
    
  </div>

  <?php
// Total number of cars uploaded by the employee
$totalCars = count($cars);

// Get latest car uploaded by employee
$latestCarName = count($cars) > 0 ? $cars[0]['brand'] . ' ' . $cars[0]['model'] : 'N/A';
?>

<!-- Summary Cards Section -->
<div id="dashboardSummary" class="summary-cards" style="display: block; flex-wrap: wrap; gap: 20px; height: 180px; width: 100%;">

<div class="card" style="background: linear-gradient(135deg, #6bf062 0%, #16932d 100%); padding: 25px; border-radius: 15px; flex: 1 1 300px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
  <h3 style="font-size: 35px; margin-bottom: 10px; color: black;">🚗 Total Cars for Rent</h3>
  <p style="font-size: 30px; font-weight: bold; color: #fff;"><?php echo $totalCars; ?></p>
</div>



<div class="card" style="background: linear-gradient(135deg,rgb(255, 138, 150) 0%,rgb(87, 7, 56) 100%); padding: 25px; border-radius: 15px; flex: 1 1 300px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); display: flex; flex-direction: column; justify-content: center; align-items: center; height: 100%;">
  <h3 style="font-size: 35px; margin-bottom: 10px; color: black;">✨ Latest Car Added</h3>
  <p style="font-size: 25px; font-weight: bold; color: #fff;"><?php echo htmlspecialchars($latestCarName); ?></p>
</div>


</div>


<div id="uploadedImagesSection">

<div class="modal-overlay" id="carModal">
  <div class="modal-content" id="modalContent">
    <button class="modal-close" onclick="closeModal()">X Close</button>
    <img id="modalImage" src="" alt="Car Image">
    <h3 id="modalTitle"></h3>
    <p><strong>Model:</strong> <span id="modalModel"></span></p>
    <p><strong>Seats:</strong> <span id="modalSeats"></span></p>
    <p><strong>Rent/hour:</strong> <span id="modalRent"> </span>Tk</p>
  </div>
</div>

    <!-- Uploaded car image cards go here -->
<div style="margin-top: 40px; padding: 20px; background-color: #fff; border-radius: 12px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
    <h2 style="font-size: 28px; margin-bottom: 30px; color: #222; font-weight: 600;">Images of Rented Cars</h2>
    <div style="display: flex; flex-wrap: wrap; gap: 25px; justify-content: flex-start;">
        <?php
        $employee_name = $_SESSION['employee_name'] ?? '';

        $sql = "SELECT brand, model, seats, rent_per_hour, image_path FROM cars WHERE employee_name = ?";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $employee_name);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
           while ($row = $result->fetch_assoc()) {
    $imagePath = htmlspecialchars($row['image_path']);
    echo '<div class="uploaded-car" style="flex: 0 0 calc(50% - 25px); max-width: calc(50% - 25px); border: 2px solid #ddd; border-radius: 14px; overflow: hidden;" onclick=\'showCarDetails(' . json_encode($row) . ')\' >';
    echo '<img src="' . $imagePath . '" alt="Car Image" style="width: 100%; height: 300px; object-fit: cover;">';
    echo '</div>';
}

        } else {
            echo "<p style='color: #666; font-size: 18px;'>No cars uploaded yet.</p>";
        }

        $stmt->close();
        ?>
    </div>
</div>

</div>


  <div id="carEntrySection" class="car-entry-form">
    <?php if ($showSuccessScreen): ?>
      <div class="success-screen">
        Car uploaded successfully!<br />
        Redirecting in <span id="countdown">3</span> seconds...
      </div>
      <script>
        let timeLeft = 3;
        const countdownElem = document.getElementById('countdown');
        const interval = setInterval(() => {
          timeLeft--;
          countdownElem.textContent = timeLeft;
          if (timeLeft <= 0) location.href = 'employee_dashboard.php?section=carEntry';
        }, 1000);
      </script>
    <?php else: ?>

    <h2>Enter Car Details</h2>
    <form method="POST" enctype="multipart/form-data" onsubmit="return validateForm()">
      <label style="margin-top: 10px; font-weight: bold;">Car Brand:</label>
      <input type="text" name="brand" required placeholder="Enter Car Brand" style="padding: 8px; border: 1px solid #ccc; border-radius: 6px;" />

      <label style="margin-top: 10px; font-weight: bold;">Car Model:</label>
      <input type="text" name="model" required placeholder="Enter Car Model" style="padding: 12px; border: 1px solid #ccc; border-radius: 6px;" />

      <label style="margin-top: 10px; font-weight: bold;">No. of Seats (max 12):</label>
      <input type="number" name="seats" min="1" max="12" required placeholder="Enter Number of Seats" style="padding: 12px; border: 1px solid #ccc; border-radius: 6px;" />

      <label style="margin-top: 10px; font-weight: bold;">Hourly Rent (BDT):</label>
      <input type="number" step="0.01" name="rent" required placeholder="Enter Hourly Rent" style="padding: 12px; border: 1px solid #ccc; border-radius: 6px;" />

      <label style="margin-top: 5px; font-weight: bold;">Upload Car Image:</label>
      <input type="file" name="car_image" accept="image/*" required style="padding: 10px; border: 1px solid #ccc; border-radius: 6px;" />

      <input type="submit" name="car_entry_submit" value="Rent Car" />
    </form>

    <?php if (!empty($uploadMessage)) echo "<div class='message'>$uploadMessage</div>"; ?>

    <div class="car-table">
      <h3 style="text-align: center; margin-top: 100px; font-weight: bold; font-size: 35px; margin-bottom: 25px;">Rented Cars List</h3>
      <table>
        <thead>
          <tr>
            <th>Brand</th>
            <th>Model</th>
            <th>Seats</th>
            <th>Rent/hr</th>
            <th>Image</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
        <?php foreach ($cars as $car): ?>
          <tr>
            <td><?php echo htmlspecialchars($car['brand']); ?></td>
            <td><?php echo htmlspecialchars($car['model']); ?></td>
            <td><?php echo $car['seats']; ?></td>
            <td><?php echo number_format($car['rent_per_hour'], 2); ?> Tk</td>
            <td><img src="<?php echo htmlspecialchars($car['image_path']); ?>" alt="Car"></td>
            <td><button class="delete-btn" onclick="confirmDelete(<?php echo $car['id']; ?>)">Delete</button></td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    
    <?php endif; ?>
  </div>

  <!-- Rent History Section -->
<div id="rentHistorySection" style="display: none; padding: 30px;">
  <h2 style="text-align: center; padding-bottom: 20px;">Rent History</h2>
  <?php
    $conn = new mysqli("localhost", "root", "", "car_rental");
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    // Make sure employee is logged in and session holds employee_name
    $employee_name = $_SESSION['employee_name'];

    // Fetch bookings where the car belongs to this employee, and payment is done
    $sql = "SELECT b.*, c.brand, c.model, c.image_path, u.name AS user_name 
            FROM bookings b
            JOIN cars c ON b.car_id = c.id
            JOIN users u ON b.user_id = u.id
            WHERE c.employee_name = ? AND b.paid50 = 1
            ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $employee_name);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">';
      while ($booking = $result->fetch_assoc()) {
        echo '
        <div class="car-card" style="background: #fff; border-radius: 16px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          <img src="' . htmlspecialchars($booking['image_path']) . '" alt="Car Image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px;">
          <h3 style="margin-top: 10px;">' . htmlspecialchars($booking['brand']) . ' ' . htmlspecialchars($booking['model']) . '</h3>
          <p><strong>Rented By:</strong> ' . htmlspecialchars($booking['user_name']) . '</p>
          <p><strong>Start:</strong> ' . htmlspecialchars($booking['start_time']) . '</p>
          <p><strong>End:</strong> ' . htmlspecialchars($booking['end_time']) . '</p>
          <p><strong>Total Fare:</strong> Tk ' . number_format($booking['total_fare'], 2) . '</p>
         
        </div>';
      }
      echo '</div>';
    } else {
      echo '<p style="text-align:center; padding: 20px;">No rent history found for your cars.</p>';
    }
    $conn->close();
  ?>
</div>
</div>




<script>


function showCarDetails(car) {
  document.getElementById('modalImage').src = car.image_path;
  document.getElementById('modalTitle').innerText = car.brand;
  document.getElementById('modalModel').innerText = car.model;
  document.getElementById('modalSeats').innerText = car.seats;
  document.getElementById('modalRent').innerText = car.rent_per_hour;
  document.getElementById('carModal').style.display = 'flex';
}

function showRentHistory() {
  document.getElementById("dashboardSummary").style.display = "none";
  document.getElementById("carEntrySection").style.display = "none";
  document.getElementById("uploadedImagesSection").style.display = "none";
  document.getElementById("rentHistorySection").style.display = "block";

  // Optional: hide carousel or extras if exist
  const carousel = document.querySelector(".carousel-wrapper");
  if (carousel) carousel.style.display = "none";
  const extras = document.getElementById("dashboardExtras");
  if (extras) extras.style.display = "none";
}


    function openModal(id) {
  document.getElementById(id).style.display = 'flex';
}

function closeModal() {
  document.getElementById('carModal').style.display = 'none';
}

  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main");
    sidebar.classList.toggle("collapsed");
    main.classList.toggle("collapsed");
  }

function showSection(section) {
  const carEntry = document.getElementById("carEntrySection");
  const dashboardSummary = document.getElementById("dashboardSummary");
  const uploadedImagesSection = document.getElementById("uploadedImagesSection");
  const rentHistory = document.getElementById("rentHistorySection");

  if (section === 'carEntry') {
    carEntry.style.display = 'block';
    dashboardSummary.style.display = 'none';
    if (uploadedImagesSection) uploadedImagesSection.style.display = 'none';
    if (rentHistory) rentHistory.style.display = 'none';
  } else if (section === 'dashboard') {
    carEntry.style.display = 'none';
    dashboardSummary.style.display = 'flex'; // restore flex layout
    if (uploadedImagesSection) uploadedImagesSection.style.display = 'flex';
    if (rentHistory) rentHistory.style.display = 'none';
  } else {
    // Hide all sections by default
    carEntry.style.display = 'none';
    dashboardSummary.style.display = 'none';
    if (uploadedImagesSection) uploadedImagesSection.style.display = 'none';
    if (rentHistory) rentHistory.style.display = 'none';
  }
}




  function confirmDelete(id) {
    if (confirm("Are you sure you want to delete this car?")) {
      window.location.href = "?delete=" + id;
    }
  }

  function validateForm() {
    const inputs = document.querySelectorAll("form input[required]");
    for (let input of inputs) {
      if (!input.value) {
        alert("Please fill in all required fields.");
        input.focus();
        return false;
      }
    }
    return true;
  }

  const urlParams = new URLSearchParams(window.location.search);
  const section = urlParams.get('section');
  if (section) {
    showSection(section);
  } else {
    showSection('dashboard');
  }
</script>
</body>
</html>