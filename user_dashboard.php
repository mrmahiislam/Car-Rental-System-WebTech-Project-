<?php
session_start();

// Prevent caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['user_name'])) {
  header("Location: login.php");
  exit();
}

// Database connection
$conn = new mysqli("localhost", "root", "", "car_rental");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// Fetch total users
$total_users_query = "SELECT COUNT(*) as total FROM users WHERE role = 'user'";
$result_users = $conn->query($total_users_query);
$row_users = $result_users->fetch_assoc();
$total_users = $row_users['total'];

// Fetch total cars
$total_cars_query = "SELECT COUNT(*) as total FROM cars";
$result_cars = $conn->query($total_cars_query);
$row_cars = $result_cars->fetch_assoc();
$total_cars = $row_cars['total'];

// Fetch total employees
$total_employees_query = "SELECT COUNT(*) as total FROM users WHERE role = 'employee'";
$result_employees = $conn->query($total_employees_query);
$row_employees = $result_employees->fetch_assoc();
$total_employees = $row_employees['total'];

// Fetch total buyers (users who booked cars)
$total_buyers_query = "SELECT COUNT(DISTINCT user_id) as total FROM bookings";
$result_buyers = $conn->query($total_buyers_query);
//$row_buyers = $result_buyers->fetch_assoc();
//$total_buyers = $row_buyers['total'];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>User Dashboard | GrabIt</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

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

    .sidebar.collapsed {
      width: 60px;
    }

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

    .sidebar.collapsed .brand-text {
      display: none;
    }

    .nav-links {
      list-style: none;
      padding: 20px 0;
    }

    .nav-links li {
      padding: 15px 20px;
      cursor: pointer;
      transition: background 0.2s ease;
    }

    .nav-links li:hover {
      background: #0056b3;
    }

    .nav-links li span {
      margin-left: 10px;
    }

    .sidebar.collapsed .nav-links li span {
      display: none;
    }

    .main-content {
      margin-left: 250px;
      padding: 30px;
      transition: margin-left 0.3s ease;
      width: 100%;
    }

    .collapsed + .main-content {
      margin-left: 60px;
    }

    .dashboard-header {
      background: #002244;
      color: white;
      padding: 15px 30px;
      display: flex;
      height: 82px; 
      justify-content: space-between;
      align-items: center;
      border-radius: 8px;
    }

    .welcome {
      font-size: 20px;
    }

    .metrics {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 30px;
    }

.car-card img {
  transition: transform 0.4s ease;
  cursor: pointer;
}

.car-card img:hover {
  transform: scale(1.08);
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}


    .metric-card {
      background: white;
      padding: 25px;
      border-radius: 12px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.1);
      text-align: center;
      transition: 0.3s;
    }

    .metric-card:hover {
      transform: translateY(-5px);
    }

    .metric-icon {
      font-size: 40px;
      margin-bottom: 10px;
      color: #0b4d91;
    }

    .metric-count {
      font-size: 32px;
      font-weight: bold;
      color: #333;
    }

    .metric-label {
      font-size: 16px;
      color: #666;
      margin-top: 5px;
    }

    .logout-btn {
      margin-top: 2px;
      text-align: center;
    }

    .logout-btn a {
      padding: 10px 25px;
      background: #dc3545;
      color: white;
      text-decoration: none;
      font-size: 16px;
      border-radius: 8px;
      transition: 0.3s;
    }

    .logout-btn a:hover {
      background: #b02a37;
    }

.carousel-wrapper {
  position: relative;
  width: 100%;
  max-width: 100%;
  margin-top: 30px;
  overflow: hidden;
  border-radius: 16px;
  height: 450px;
  box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
}

.carousel {
  display: flex;
  transition: transform 0.8s ease-in-out;
  height: 100%;
}

.carousel img {
  width: 100%;
  height: 100%;
  flex-shrink: 0;
  object-fit: cover;
  transition: transform 0.5s ease, box-shadow 0.5s ease;
  cursor: pointer;
}

.carousel img:hover {
  transform: scale(1.05);
  box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.carousel-overlay {
  position: absolute;
  top: 0;
  left: 0;
  height: 100%;
  width: 100%;
  background: rgba(0, 0, 0, 0.35);
  backdrop-filter: blur(2px);
  z-index: 2;
  display: flex;
  align-items: center;
  justify-content: center;
}

.rent-link {
  position: absolute;
  z-index: 3;
  top: 50%;
  left: 50%;


  cursor: pointer;
  color: #fff;
  text-decoration: underline;
  font-weight: bold;


  transform: translate(-50%, -50%);
  font-size: 70px;
  color: white;
  font-weight: bold;
  text-decoration: none;
  background: none; /* Remove background to avoid blur */
  padding: 20px 40px;
  border-radius: 12px;
  transition: color 0.3s ease, text-shadow 0.3s ease;
}

.rent-link:hover {
  color:rgb(255, 255, 255); /* Optional hover color */
  text-shadow: 0 0 15px rgba(255, 255, 255, 0.5); /* Add glow instead of movement */
}


.carousel-nav {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  background-color: rgba(0, 0, 0, 0.4);
  border: none;
  padding: 14px;
  cursor: pointer;
  border-radius: 50%;
  z-index: 3;
  transition: background 0.3s ease;
}

.carousel-nav:hover {
  background-color: rgba(0, 0, 0, 0.8);
}

.carousel-nav svg {
  width: 30px;
  height: 30px;
}

a.book-now-btn {
  display: inline-block;
  margin-top: 12px;
  padding: 10px 20px;
  background-color: #28a745;
  color: white;
  text-align: center;
  border-radius: 6px;
  text-decoration: none;
  transition: background-color 0.3s ease;
}
  .popup-hover {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
  }

  .popup-hover:hover {
    transform: scale(1.08);
    box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
    z-index: 1;
  }
a.book-now-btn:hover {
  background-color:rgb(43, 249, 87);
}



html {
  scroll-behavior: smooth;
}
.carousel-nav.prev {
  left: 20px;
}

.carousel-nav.next {
  right: 20px;
}


  </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
  <div class="sidebar-header">
    <span class="toggle-btn" onclick="toggleSidebar()">☰</span>
    <span class="brand-text">GrabIt</span>
  </div>
  <ul class="nav-links">
<li onclick="showDashboard()" style="cursor: pointer;">
  <i class="icon">🏠</i>
  <span style="margin-left: 8px;">Dashboard</span>
</li>
    <li onclick="showAvailableCars()"><i class="icon">🚗</i><span>Available Cars</span></li>
    <li onclick="showMyBookings()" style="cursor:pointer;"><i class="icon">🕓</i><span>My Bookings</span></li>
  </ul>
</div>

<!-- Main Content -->
<div class="main-content" id="dashboardSection">
  <div class="dashboard-header">
    <div class="welcome">Welcome, <strong><?php echo htmlspecialchars($_SESSION['user_name']); ?></strong></div>
    <div class="logout-btn">
      <a href="logout.php">Logout</a>
    </div>
  </div>
  
  <div class="carousel-wrapper">
<div class="carousel-overlay">
  <span class="rent-link" onclick="showAvailableCars()">Rent Your Cars</span>
</div>

    <div class="carousel" id="carousel">
      <img src="images/car4.jpg" alt="Car 1">
      <img src="images/car2.jpg" alt="Car 2">
      <img src="images/car3.jpg" alt="Car 3">
      <img src="images/car1.jpg" alt="Car 4">
    </div>
    <button class="carousel-nav prev" onclick="changeSlide(-1)">
      <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M15.41 16.59L10.83 12l4.58-4.59L14 6l-6 6 6 6z"/></svg>
    </button>
    <button class="carousel-nav next" onclick="changeSlide(1)">
      <svg width="24" height="24" fill="white" viewBox="0 0 24 24"><path d="M8.59 16.59L13.17 12 8.59 7.41 10 6l6 6-6 6z"/></svg>
    </button>
  </div>

  <div id="dashboardExtras" style="display: block;">
 <!-- ✅ New All Brands & Top Rated Cars section -->
<section style="width: 100%; padding: 40px 20px; box-sizing: border-box; background-color: #f9f9f9;">

  <!-- All Brands -->
  <div style="padding: 20px 0;">
    <h2 style="font-size: 28px; font-weight: bold; margin-bottom: 30px; text-align: center;">All Brands</h2>
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 30px; justify-items: center;">
      <img src="Images/brands/car1.png" alt="Volkswagen" class="popup-hover" style="width: 150px; height: auto;">
      <img src="Images/brands/car2.png" alt="Toyota" class="popup-hover" style="width: 150px; height: auto;">
      <img src="Images/brands/car3.png" alt="Mercedes Benz" class="popup-hover" style="width: 250px; height: auto;">
      <img src="Images/brands/car4.png" alt="Honda" class="popup-hover" style="width: 150px; height: auto;">
      <img src="Images/brands/car5.png" alt="BMW" class="popup-hover" style="width: 150px; height: auto;">
      <img src="Images/brands/car6.png" alt="Ford" class="popup-hover" style="width: 150px; height: auto;">
      <img src="Images/brands/car7.png" alt="Nissan" class="popup-hover" style="width: 150px; height: auto;">
      <img src="Images/brands/car8.png" alt="Tata" class="popup-hover" style="width: 250px; height: auto;">
      <img src="Images/brands/car9.png" alt="Tesla" class="popup-hover" style="width: 150px; height: auto;">
    </div>
  </div>

  <!-- Top Rated Cars -->
  <div style="margin-top: 60px;">
    <h2 style="font-size: 28px; font-weight: bold; margin-bottom: 20px; text-align: center;">Top Rated Cars</h2>
    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(220px, 1fr)); gap: 20px;">
      <!-- Car 1 -->
      <div class="popup-hover" style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <img src="Images/topcars/car1.jpg" alt="Tesla Model S" style="width: 100%; border-radius: 8px;">
        <h3 style="margin-top: 10px;">Tesla Model S</h3>
        <p style="color: #ffa500;">★★★★☆ (4.5)</p>
        <p style="font-style: italic;">"Amazing experience, super smooth and futuristic!" – John D.</p>
      </div>
      <!-- Car 2 -->
      <div class="popup-hover" style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <img src="Images/topcars/car2.jpg" alt="BMW M3" style="width: 100%; border-radius: 8px;">
        <h3 style="margin-top: 10px;">BMW M3</h3>
        <p style="color: #ffa500;">★★★★★ (5.0)</p>
        <p style="font-style: italic;">"Absolute performance beast. Loved it!" – Sarah W.</p>
      </div>

      <!-- Car 4 -->
      <div class="popup-hover" style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <img src="Images/topcars/car3.jpg" alt="Ford Mustang" style="width: 100%; border-radius: 8px;">
        <h3 style="margin-top: 10px;">Ford Mustang</h3>
        <p style="color: #ffa500;">★★★★☆ (4.3)</p>
        <p style="font-style: italic;">"Raw power and an American classic." – David R.</p>
      </div>
      <!-- Car 5 -->
      <div class="popup-hover" style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <img src="Images/topcars/car4.jpg" alt="Mercedes C-Class" style="width: 100%; border-radius: 8px;">
        <h3 style="margin-top: 10px;">Mercedes C-Class</h3>
        <p style="color: #ffa500;">★★★★★ (4.9)</p>
        <p style="font-style: italic;">"Feels like flying on the road." – Emma L.</p>
      </div>
      <!-- Car 6 -->
      <div class="popup-hover" style="border: 1px solid #ccc; border-radius: 10px; padding: 15px; background-color: #fff; box-shadow: 0 2px 5px rgba(0,0,0,0.1);">
        <img src="Images/topcars/car5.jpg" alt="Chevy Camaro" style="width: 100%; border-radius: 8px;">
        <h3 style="margin-top: 10px;">Chevrolet Camaro</h3>
        <p style="color: #ffa500;">★★★★☆ (4.4)</p>
        <p style="font-style: italic;">"Great roar, smooth drive." – Mike G.</p>
      </div>
    </div>
  </div>

</section>

</div>
  <!-- Available Cars Section INSIDE main-content -->
  <div id="availableCarsSection" style="display: none;">
    <h2 style="text-align: center; padding: 20px;">Available Cars</h2>

  <?php
    // Inline Database Connection
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "car_rental";

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Fetch cars from 'cars' table
   //$car_query = "SELECT * FROM cars ORDER BY created_at DESC";

   $car_query = "
SELECT * FROM cars 
WHERE id NOT IN (
  SELECT car_id FROM bookings WHERE paid50 = 1
) 
ORDER BY created_at DESC
";

    $result = $conn->query($car_query);

   if ($result->num_rows > 0) {

    echo '<div style="width: 100%; box-sizing: border-box; padding: 20px;">
            <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">';

    while ($car = $result->fetch_assoc()) {
        echo '
        <div class="car-card" style="background: #fff; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); overflow: hidden; transition: 0.3s;">
  <img src="' . htmlspecialchars($car['image_path']) . '" alt="Car Image" style="width: 100%; height: 200px; object-fit: cover;">
  <div style="padding: 16px;">
    <h3 style="margin-bottom: 8px;">' . htmlspecialchars($car['brand']) . ' ' . htmlspecialchars($car['model']) . '</h3>
    <p><strong>Seats:</strong> ' . htmlspecialchars($car['seats']) . '</p>
    <p><strong>Rent per hour:</strong> ' . htmlspecialchars($car['rent_per_hour']) . ' Tk</p>
    <p style="font-size: 0.85em; color: #777;"><strong>Car Owner:</strong> ' . htmlspecialchars($car['employee_name']) . '</p>
<a href="booking.php?car_id=' . $car['id'] . '" style="display: inline-block; margin-top: 12px; padding: 10px 20px; background-color: #28a745; color: white; text-align: center; border-radius: 6px; text-decoration: none; transition: background-color 0.3s ease;">Book Now</a>

  </div>
</div> ';
    }

    echo '</div></div>';
} else {
    echo "<p style='text-align: center; padding: 20px;'>No cars available yet.</p>";
}

    $conn->close();
    ?>
  </div> <!-- End of #availableCarsSection -->

<!-- My Bookings Section -->
<div id="myBookingsSection" style="display: none; padding: 30px;">
  <h2 style="text-align: center; padding-bottom: 20px;">My Bookings</h2>
  <?php
    $conn = new mysqli("localhost", "root", "", "car_rental");
    if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
    }

    $user_id = $_SESSION['user_id'];

    $sql = "SELECT b.*, c.brand, c.model, c.image_path 
            FROM bookings b 
            JOIN cars c ON b.car_id = c.id 
            WHERE b.user_id = ? AND b.paid50 = 1 
            ORDER BY b.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
      echo '<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px;">';
      while ($booking = $result->fetch_assoc()) {
        echo '
        <div class="car-card" style="background: #fff; border-radius: 16px; padding: 15px; box-shadow: 0 4px 10px rgba(0,0,0,0.1);">
          <img src="' . htmlspecialchars($booking['image_path']) . '" alt="Car Image" style="width: 100%; height: 200px; object-fit: cover; border-radius: 12px;">
          <h3 style="margin-top: 10px;">' . htmlspecialchars($booking['brand']) . ' ' . htmlspecialchars($booking['model']) . '</h3>
          <p><strong>Start:</strong> ' . htmlspecialchars($booking['start_time']) . '</p>
          <p><strong>End:</strong> ' . htmlspecialchars($booking['end_time']) . '</p>
          <p><strong>Total Fare:</strong> Tk ' . number_format($booking['total_fare'], 2) . '</p>
          <form method="GET" action="invoice.php">
  <input type="hidden" name="booking_id" value="' . $booking['id'] . '">
  <button type="submit" style="margin-top: 10px; padding: 10px 20px; background-color: #007bff; color: white; border: none; border-radius: 6px; cursor: pointer;">View Invoice</button>
</form>

        </div>';
      }
      echo '</div>';
    } else {
      echo '<p style="text-align:center; padding: 20px;">You have no bookings till now.</p>';
    }
    $conn->close();
  ?>
</div>

  
</div> <!-- ✅ Correct place to close .main-content -->


<script>
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const main = document.querySelector(".main-content");  
    sidebar.classList.toggle("collapsed");
    main.classList.toggle("collapsed");
  }

  function showMyBookings() {
  document.querySelector(".carousel-wrapper").style.display = "none";
  const extras = document.getElementById("dashboardExtras");
  if (extras) extras.style.display = "none";

  document.getElementById("availableCarsSection").style.display = "none";
  document.getElementById("myBookingsSection").style.display = "block";
}


  function showAvailableCars() {
    // Hide everything else if needed (carousel, etc.)
    document.querySelector(".carousel-wrapper").style.display = "none";

    // ✅ Hide All Brands & Top Rated Cars section
    const extras = document.getElementById("dashboardExtras");
    if (extras) {
      extras.style.display = "none";
    }

    // Show Available Cars section
    document.getElementById("availableCarsSection").style.display = "block";
    document.getElementById("myBookingsSection").style.display = "none";
  }

  let currentIndex = 0;
  const carousel = document.getElementById("carousel");
  const totalImages = carousel.children.length;

  function updateCarousel() {
    carousel.style.transform = `translateX(-${currentIndex * 100}%)`;
  }

  function changeSlide(direction) {
    currentIndex = (currentIndex + direction + totalImages) % totalImages;
    updateCarousel();
  }

  setInterval(() => {
    changeSlide(1);
  }, 10000);

  function showDashboard() {
    // Show carousel again
    document.querySelector(".carousel-wrapper").style.display = "block";

    // ✅ Show All Brands & Top Rated Cars section
    const extras = document.getElementById("dashboardExtras");
    if (extras) {
      extras.style.display = "block";
    }

    // Hide Available Cars section
    document.getElementById("availableCarsSection").style.display = "none";
    document.getElementById("myBookingsSection").style.display = "none";
  }
</script>


</body>
</html>
