<?php
session_start();

// Also prevent caching:
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

if (!isset($_SESSION['admin_name'])) {
  header("Location: login.php");
  exit();
}

// Connect to database
$conn = new mysqli("localhost", "root", "", "car_rental");
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// DELETE handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_car_id'])) {
    $delete_car_id = intval($_POST['delete_car_id']);
    $stmt = $conn->prepare("DELETE FROM cars WHERE id = ?");
    $stmt->bind_param("i", $delete_car_id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// DELETE user handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_user_id'])) {
    $delete_user_id = intval($_POST['delete_user_id']);
    // Optionally prevent deleting admin or yourself here
    
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $delete_user_id);
    $stmt->execute();
    $stmt->close();

    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Fetch metrics from database
$total_users = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'user'")->fetch_assoc()['count'];
$total_employees = $conn->query("SELECT COUNT(*) AS count FROM users WHERE role = 'employee'")->fetch_assoc()['count'];
$total_cars = $conn->query("SELECT COUNT(*) AS count FROM cars")->fetch_assoc()['count'];
$total_bookings = $conn->query("SELECT COUNT(*) AS count FROM bookings")->fetch_assoc()['count'];


// Fetch Employees
$employeesResult = $conn->query("SELECT id, name, email FROM users WHERE role = 'employee' ORDER BY name ASC");

// Fetch Users
$usersResult = $conn->query("SELECT id, name, email FROM users WHERE role = 'user' ORDER BY name ASC");


// Generate HTML table for employees
$employeeTableHtml = '<table style="width:100%; border-collapse: collapse; margin-top: 20px;">
  <thead>
    <tr style="background:#0b4d91; color:#fff;">
      <th style="padding: 12px 15px;">Name</th>
      <th style="padding: 12px 15px;">Email</th>
      <th style="padding: 12px 15px;">Action</th>
    </tr>
  </thead>
  <tbody>';

while ($emp = $employeesResult->fetch_assoc()) {
  $employeeTableHtml .= '<tr style="background:#fff; border-bottom:1px solid #ddd;">
    <td style="padding: 12px 15px;">'.htmlspecialchars($emp['name']).'</td>
    <td style="padding: 12px 15px;">'.htmlspecialchars($emp['email']).'</td>
    <td style="padding: 12px 15px;">
      <button class="delete-account-btn" data-userid="'.$emp['id'].'" style="background:#dc3545; color:#fff; border:none; padding:6px 12px; border-radius:5px; cursor:pointer;">Delete</button>
    </td>
  </tr>';
}
$employeeTableHtml .= '</tbody></table>';

// Generate HTML table for users
$usersTableHtml = '<table style="width:100%; border-collapse: collapse; margin-top: 20px;">
  <thead>
    <tr style="background:#0b4d91; color:#fff;">
      <th style="padding: 12px 15px;">Name</th>
      <th style="padding: 12px 15px;">Email</th>
      <th style="padding: 12px 15px;">Action</th>
    </tr>
  </thead>
  <tbody>';

while ($user = $usersResult->fetch_assoc()) {
  $usersTableHtml .= '<tr style="background:#fff; border-bottom:1px solid #ddd;">
    <td style="padding: 12px 15px;">'.htmlspecialchars($user['name']).'</td>
    <td style="padding: 12px 15px;">'.htmlspecialchars($user['email']).'</td>
    <td style="padding: 12px 15px;">
      <button class="delete-account-btn" data-userid="'.$user['id'].'" style="background:#dc3545; color:#fff; border:none; padding:6px 12px; border-radius:5px; cursor:pointer;">Delete</button>
    </td>
  </tr>';
}
$usersTableHtml .= '</tbody></table>';

$paidBookingsQuery = "
    SELECT b.id, u.name AS user_name, c.brand, c.model, b.start_time, b.end_time, b.total_fare
    FROM bookings b
    JOIN users u ON b.user_id = u.id
    JOIN cars c ON b.car_id = c.id
    WHERE b.is_paid = 1
    ORDER BY b.start_time DESC
";
$paidBookingsResult = $conn->query($paidBookingsQuery);

if (!$paidBookingsResult) {
    die("Error in paid bookings query: " . $conn->error);
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Dashboard | GrabIt</title>
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

.bookings-table {
  width: 100%;
  border-collapse: separate;  /* keep spacing for rounded corners */
  border-spacing: 0 12px;      /* vertical spacing */
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.bookings-table thead tr {
  background-color: #0b4d91;
  color: #fff;
  font-weight: 600;
  border-radius: 8px;  /* NOTE: This might not work fully on tr */
}

/* To round only the first and last headers' corners */
.bookings-table thead th:first-child {
  border-top-left-radius: 8px;
}

.bookings-table thead th:last-child {
  border-top-right-radius: 8px;
}

.bookings-table thead th {
  padding: 14px 20px;
  font-size: 16px;
  text-align: center; /* Center header text */
  border-top: 1px solid #0b4d91;
}

.bookings-table tbody tr {
  background-color: #fff;
  box-shadow: 0 4px 8px rgba(0,0,0,0.05);
  transition: transform 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
  border-radius: 8px;  /* NOTE: might not round well on tr, but keep */
}

.bookings-table tbody tr:hover {
  transform: translateY(-5px);
  box-shadow: 0 8px 20px rgba(11,77,145,0.2);
}

/* Center all table body data */
.bookings-table tbody td {
  padding: 14px 20px;
  font-size: 15px;
  color: #333;
  vertical-align: middle;
  text-align: center;  /* Center cell content */
}

/* Highlight first column */
.bookings-table tbody td:first-child {
  font-weight: 700;
  color: #0b4d91;
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
      justify-content: space-between;
      align-items: center;
      border-radius: 8px;
      min-height: 80px;
    }

    .welcome {
      font-size: 18px;
    }

    .metrics {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
      gap: 20px;
      margin-top: 30px;
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
      margin-top: 0px;
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

html, body {
  height: 100%;
  margin: 0;
}

.switch-wrapper {
  display: flex;
  justify-content: center;  /* center horizontally */
  align-items: center;      /* center vertically */
  width: 100%;
}

#manageAccountsSection h2 {
  margin-bottom: 30px;  /* reduce from default ~20px */
}

#accountTabs {
  position: relative;
  width: 300px; /* or your width */
  /* Remove ash background */
  background: transparent;  /* or same as slider's blue */
  border-radius: 25px;
  overflow: hidden;
  display: flex;
  box-sizing: border-box;
   /* remove any border if any */
  /* Add this for the thin black border */
  border: 1px solid black;
}

#accountTabs button {
  position: relative;
  z-index: 2;
  flex: 1;
  border: none;
  background: transparent;
  padding: 10px 30px;
  cursor: pointer;
  font-weight: 600;
  font-size: 16px;
  color: #0b4d91; /* Blue text by default */
  transition: color 0.3s ease;
  outline: none;
  border-radius: 30px;
}

#accountTabs button.active {
  color: white;
}

#accountTabs .slider {
  position: absolute;
  top: 0px;
  bottom: 3px;
  left: 0;
  width: 50%; /* start half */
   height: 40px; 
  background-color: #0b4d91;
  border-radius: 30px;
  transition: left 0.3s ease;
  z-index: 1;
}
.gif-frame {
  height: 430px;          /* your fixed height */
  border: 3px solid #0b4d91;
  border-radius: 15px;
  padding: 10px;
  box-shadow: 0 4px 10px rgba(11, 77, 145, 0.3);
  display: flex;
  justify-content: space-between;
  align-items: center;
  background: white;
  overflow: hidden;
  gap: 0px;              /* space between gifs */
}

.gif-frame img {
  width: 49.5%;             /* almost half each */
  height: 100%;
  object-fit: cover;
  border-radius: 12px;
}



    /* Container for the buttons */
.account-tabs {
  position: relative;
  display: inline-flex;
  border-bottom: 3px solid #ddd;
  justify-content: center;
  margin-bottom: 20px;
   gap: 12px; 
    margin: 5px 0 10px 0;
}

#accountsList {
  margin-top: 40px;  /* smaller top margin */
}

/* Tab buttons */
.account-tabs button {
  background: none;
  border: none;
  padding: 12px 30px;
  font-size: 18px;
  font-weight: 600;
  color: #555;
  cursor: pointer;
  position: relative;
  transition: color 0.3s ease;
  outline: none;
}

/* Hover color */
.account-tabs button:hover {
  color: #0b4d91;
}

/* Active tab style */
.account-tabs button.active {
  color: #0b4d91;
  font-weight: 700;
}

/* The sliding underline */
.account-tabs::after {
  content: '';
  position: absolute;
  bottom: 0;
  height: 3px;
  background-color: #0b4d91;
  border-radius: 2px;
  transition: all 0.3s ease;
  width: 0;
  left: 0;
}

/* Container must be relative for ::after */
.account-tabs {
  position: relative;
}

/* JavaScript will dynamically update left & width of this underline */

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
  <li onclick="showSection('dashboard')"><i class="icon">🏠</i><span>Dashboard</span></li>
  <li onclick="showSection('carList')"><i class="icon">🚗</i><span>Car List</span></li>
  <li onclick="showSection('bookings')"><i class="icon">📅</i><span>Bookings</span></li>
  <li onclick="showSection('manageAccounts')"><i class="icon">🧑‍🦰</i><span>Manage Accounts</span></li>
</ul>

</div>

<!-- Main Content -->
<div class="main-content" id="main">
  <div class="dashboard-header">
    <div class="welcome">Welcome, <strong><?php echo htmlspecialchars($_SESSION['admin_name']); ?></strong></div>
    <div class="logout-btn">
      <a href="logout.php">Logout</a>
    </div>
  </div>

  <div id="dashboardSection" class="metrics">
    <div class="metric-card">
      <div class="metric-icon">👤</div>
      <div class="metric-count"><?php echo $total_users; ?></div>
      <div class="metric-label">Total Users</div>
    </div>

    <div class="metric-card">
      <div class="metric-icon">🚗</div>
      <div class="metric-count"><?php echo $total_cars; ?></div>
      <div class="metric-label">Total Cars</div>
    </div>

    <div class="metric-card">
      <div class="metric-icon">👔</div>
      <div class="metric-count"><?php echo $total_employees; ?></div>
      <div class="metric-label">Total Employees</div>
    </div>

    <div class="metric-card">
      <div class="metric-icon">📃</div>
      <div class="metric-count"><?php echo $total_bookings; ?></div>
      <div class="metric-label">Total Car Bookings</div>
    </div>
  
  </div>



<!-- Car List Section (hidden by default) -->
<div id="carListSection" style="display:none; margin-top: 40px;">
  <h2 style="text-align:center; margin-bottom: 20px; color:#0b4d91;">Car List</h2>

  <div style="
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
    padding: 0 20px;
  ">
    <?php
      // Reconnect DB here if needed or reuse $conn
      $carQuery = "SELECT * FROM cars ORDER BY created_at DESC";
      $carResult = $conn->query($carQuery);
      if ($carResult->num_rows > 0) {
        while ($car = $carResult->fetch_assoc()) {
          echo '
          <div class="car-card" style="
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
            overflow: hidden;
            padding: 0;
            transition: transform 0.3s ease;
          ">
            <img src="'.htmlspecialchars($car['image_path']).'" alt="Car Image" style="
              width: 100%;
              height: 200px;
              object-fit: cover;
              border-top-left-radius: 16px;
              border-top-right-radius: 16px;
            ">
            <div style="padding: 16px;">
              <h3 style="margin-bottom: 10px; font-size: 1.2rem; color:#0b4d91;">'
                .htmlspecialchars($car['brand']).' '.htmlspecialchars($car['model']).'
              </h3>
              <p><strong>Seats:</strong> '.htmlspecialchars($car['seats']).'</p>
              <p><strong>Rent per hour:</strong> '.htmlspecialchars($car['rent_per_hour']).' Tk</p>
              <p style="font-size:0.85em; color:#777;"><strong>Car Owner:</strong> '.htmlspecialchars($car['employee_name']).'</p>

              <button class="delete-btn" data-carid="'. $car['id'] .'" style="
                margin-top: 12px;
                padding: 10px 20px;
                background-color: #dc3545;
                color: #fff;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                transition: background-color 0.3s;
              ">
                Delete Car
              </button>
            </div>
          </div>';
        }
      } else {
        echo "<p style='text-align:center;'>No cars available.</p>";
      }
    ?>
  </div>
</div>
.
<!-- Bookings Section -->
<div id="bookingsSection" style="display:none; margin-top: 40px;">
  <h2 style="text-align:center; margin-bottom: 20px; color:#0b4d91;">Paid Bookings</h2>
  <?php if ($paidBookingsResult->num_rows > 0): ?>
    <table class="bookings-table">
      <thead>
        <tr>
          <th>Booking ID</th>
          <th>User</th>
          <th>Car</th>
          <th>Start Date</th>
          <th>End Date</th>
          <th>Total Fare (Tk)</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($booking = $paidBookingsResult->fetch_assoc()): ?>
          <tr>
            <td><?php echo htmlspecialchars($booking['id']); ?></td>
            <td><?php echo htmlspecialchars($booking['user_name']); ?></td>
            <td><?php echo htmlspecialchars($booking['brand'] . ' ' . $booking['model']); ?></td>
            <td><?php echo htmlspecialchars($booking['start_time']); ?></td>
            <td><?php echo htmlspecialchars($booking['end_time']); ?></td>
            <td><?php echo htmlspecialchars($booking['total_fare']); ?></td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="text-align:center;">No paid bookings found.</p>
  <?php endif; ?>
</div>

<!-- Manage Accounts Section -->
<div id="manageAccountsSection" style="display:none; margin-top: 40px;">

  <h2 style="text-align:center; margin-bottom: 20px;">Manage Accounts</h2>

  <!-- Tabs or buttons to switch between Employees and Users -->
<div class="switch-wrapper">
  <div id="accountTabs">
    <button id="showEmployeesBtn" class="active" onclick="switchAccountTab('employee', this)">Employees</button>
    <button id="showUsersBtn" onclick="switchAccountTab('user', this)">Users</button>
    <div class="slider"></div>
  </div>
</div>


  <!-- Container for the accounts table -->
  <div id="accountsList">
    <!-- JS will load employee or user accounts here -->
  </div>

</div>

<!-- GIF Frame -->
<!-- GIF Frame -->
<div id="dashboardGif" class="gif-frame" style="margin: 30px auto 0; max-width: 1390px;">
  <img src="Images/AdminCar.gif" alt="Animated GIF 1" />
  <img src="Images/AdminCar2.gif" alt="Animated GIF 2" />
</div>





</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  // Sidebar toggle if you already have it
  function toggleSidebar() {
    const sidebar = document.getElementById("sidebar");
    const main = document.getElementById("main");
    sidebar.classList.toggle("collapsed");
    main.classList.toggle("collapsed");
  }
  window.toggleSidebar = toggleSidebar;  // expose to global because onclick calls it in HTML

  // Keep the **old** showSection here but rename so it doesn’t override
  function showSectionInside(section) {
    const dashboard = document.querySelector('.metrics');
    const carList = document.getElementById('carListSection');
    // Hide all
    dashboard.style.display = 'none';
    carList.style.display = 'none';

    if (section === 'dashboard') {
      dashboard.style.display = 'grid';
    } else if (section === 'carList') {
      carList.style.display = 'block';
    }
  }
  window.showSectionInside = showSectionInside; // keep separate

  // Now expose the **outside** function to global scope to override the old one
  window.showSection = function(section) {
    const dashboard = document.getElementById('dashboardSection');
    const carList = document.getElementById('carListSection');
    const bookings = document.getElementById('bookingsSection');
    const manageAccounts = document.getElementById('manageAccountsSection');
    const gif = document.getElementById('dashboardGif');

    // Hide all sections initially
    dashboard.style.display = 'none';
    carList.style.display = 'none';
    bookings.style.display = 'none';
    if(manageAccounts) manageAccounts.style.display = 'none';
    if (gif) gif.style.display = 'none';
    // Show requested section
  if(section === 'dashboard') {
    if(dashboard) dashboard.style.display = 'grid';
    if(gif) gif.style.display = 'flex';  // flex because .gif-frame uses flexbox
  }
  else if(section === 'carList') {
    if(carList) carList.style.display = 'block';
  }
  else if(section === 'bookings') {
    if(bookings) bookings.style.display = 'block';
  } else if (section === 'manageAccounts') {
      if(manageAccounts) {
        manageAccounts.style.display = 'block';
        // Default to show employees first
        showAccounts('employee');
      }
    }
  };

  

  // Existing delete buttons for cars
  const deleteButtons = document.querySelectorAll('.delete-btn');
  const modal = document.getElementById('deleteModal');
  const confirmBtn = document.getElementById('confirmDeleteBtn');
  const cancelBtn = document.getElementById('cancelDeleteBtn');

  let carIdToDelete = null;
  let userIdToDelete = null;
  let deletingCar = false;

  deleteButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      deletingCar = true;
      carIdToDelete = btn.getAttribute('data-carid');
      modal.style.display = 'flex';
      modal.querySelector('h3').textContent = "Warning!";
      modal.querySelector('p').innerHTML = "Are you sure? <br><small>(It will delete the car from the rent list)</small>";
    });
  });

  cancelBtn.addEventListener('click', () => {
    modal.style.display = 'none';
    carIdToDelete = null;
    userIdToDelete = null;
    deletingCar = false;
  });

  confirmBtn.addEventListener('click', () => {
    if(deletingCar && carIdToDelete) {
      // Submit form POST with delete_car_id
      const form = document.createElement('form');
      form.method = 'POST';
      form.style.display = 'none';

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'delete_car_id';
      input.value = carIdToDelete;

      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    } else if (userIdToDelete) {
      // Submit form POST with delete_user_id
      const form = document.createElement('form');
      form.method = 'POST';
      form.style.display = 'none';

      const input = document.createElement('input');
      input.type = 'hidden';
      input.name = 'delete_user_id';
      input.value = userIdToDelete;

      form.appendChild(input);
      document.body.appendChild(form);
      form.submit();
    }
  });

  // Close modal if click outside modal content
  window.addEventListener('click', (e) => {
    if (e.target === modal) {
      modal.style.display = 'none';
      carIdToDelete = null;
      userIdToDelete = null;
      deletingCar = false;
    }
  });

  // ===== Manage Accounts JS =====

    const employeeTable = `<?php echo addslashes($employeeTableHtml ?? ''); ?>`;
  const usersTable = `<?php echo addslashes($usersTableHtml ?? ''); ?>`;

  window.showAccounts = function(role) {
    const container = document.getElementById('accountsList');
    if (!container) return;
    if (role === 'employee') {
      container.innerHTML = employeeTable;
    } else if (role === 'user') {
      container.innerHTML = usersTable;
    }
    attachAccountDeleteHandlers();
  };

  function animateSlider(activeBtn) {
  const tabsContainer = document.getElementById('accountTabs');
  const slider = tabsContainer.querySelector('.slider');
  if (!slider) return;

  const buttons = tabsContainer.querySelectorAll('button');
  const index = Array.from(buttons).indexOf(activeBtn);
  if (index === -1) return;

  // slider width is 50% because 2 buttons
  slider.style.left = (index * 50) + '%';
}


 

  window.switchAccountTab = function(role, btn) {
  // Show the correct accounts table
  showAccounts(role);

  // Manage active classes on buttons
  const tabsContainer = document.getElementById('accountTabs');
  const buttons = tabsContainer.querySelectorAll('button');
  buttons.forEach(b => b.classList.remove('active'));
  btn.classList.add('active');

  // Animate slider and underline for visual feedback
  animateSlider(btn);
  animateUnderline(btn);
};

  // Initialize underline on DOM load and show employees by default
  const tabsContainer = document.getElementById('accountTabs');
  const activeBtn = tabsContainer.querySelector('button.active') || tabsContainer.querySelector('button');
  if (activeBtn) {
    window.switchAccountTab('employee', activeBtn);
  }

  function attachAccountDeleteHandlers() {
    const buttons = document.querySelectorAll('.delete-account-btn');
    buttons.forEach(btn => {
      btn.addEventListener('click', () => {
        userIdToDelete = btn.getAttribute('data-userid');
        deletingCar = false;
        modal.style.display = 'flex';
        modal.querySelector('h3').textContent = 'Warning!';
        modal.querySelector('p').innerHTML = 'Are you sure you want to delete this account? <br><small>(This action cannot be undone)</small>';
      });
    });
  }

// Show dashboard + GIF on page load
  showSection('dashboard');

  // Expose the function globally if you use it elsewhere
  window.showSection = showSection;
  
});
</script>




<!-- Delete Confirmation Modal -->
<div id="deleteModal" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.6); 
    justify-content: center; align-items: center; z-index: 9999;">
  <div style="background: white; border-radius: 10px; padding: 20px; width: 320px; text-align: center; box-shadow: 0 0 10px #000;">
    <h3 style="color: #dc3545;">Warning!</h3>
    <p>Are you sure? <br><small>(It will delete the car from the rent list)</small></p>
    <div style="margin-top: 20px;">
      <button id="confirmDeleteBtn" style="padding: 10px 20px; background-color: #dc3545; color: white; border: none; border-radius: 6px; margin-right: 10px; cursor: pointer;">Yes</button>
      <button id="cancelDeleteBtn" style="padding: 10px 20px; background-color: #6c757d; color: white; border: none; border-radius: 6px; cursor: pointer;">No</button>
    </div>
  </div>
</div>

</body>
</html>
