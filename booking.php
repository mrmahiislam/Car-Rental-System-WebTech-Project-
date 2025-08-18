<?php
session_start();
$conn = new mysqli("localhost", "root", "", "car_rental");

if (isset($_GET['car_id'])) {
  $car_id = $_GET['car_id'];
  $result = $conn->query("SELECT * FROM cars WHERE id = $car_id");
  $car = $result->fetch_assoc();
} else {
  die("Car not specified.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $start = $_POST['start_time'];
  $end = $_POST['end_time'];
  $car_id = $_POST['car_id'];
  $user_id = $_SESSION['user_id'] ?? 1; // fallback for demo

  $start_dt = new DateTime($start);
  $end_dt = new DateTime($end);
  $diff = $start_dt->diff($end_dt);
  $hours = ceil(($diff->days * 24) + ($diff->h) + ($diff->i / 60));
  $total_fare = $hours * $car['rent_per_hour'];

  $stmt = $conn->prepare("INSERT INTO bookings (user_id, car_id, start_time, end_time, total_hours, total_fare) VALUES (?, ?, ?, ?, ?, ?)");
  $stmt->bind_param("iissid", $user_id, $car_id, $start, $end, $hours, $total_fare);
  $stmt->execute();
$booking_id = $stmt->insert_id;
echo "<script>
  alert('Booking Confirmed! Redirecting you to payment...');
  window.location.href = 'billing.php?booking_id={$booking_id}';
</script>";
exit;

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Book <?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></title>
  <style>
    body {
      background:rgb(13, 88, 163);
      font-family: 'Segoe UI', sans-serif;
      padding: 40px;
      margin: 0;
    }
    .booking-container {
      max-width: 500px;
      background:rgb(255, 255, 255);
      margin: auto;
      border-radius: 16px;
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
      padding: 30px;
      transition: all 0.3s ease;
    }
    .car-details img {
      width: 100%;
      height: auto;
      border-radius: 10px;
      object-fit: cover;
    }
    .info h2 {
      margin: 12px 0 5px;
      font-size: 24px;
      color: #333;
    }
    .info p {
      margin: 5px 0;
      color: #555;
      font-size: 15px;
    }
    .input-group {
      margin: 20px 0;
    }
    .input-group label {
      font-weight: 600;
      display: block;
      margin-bottom: 6px;
      color: #444;
    }
    .input-group input {
      padding: 10px;
      width: 100%;
      border-radius: 8px;
      border: 1px solid #ccc;
      box-sizing: border-box;
      font-size: 15px;
    }
    .summary {
      background:rgb(174, 210, 247);
      padding: 15px;
      border-radius: 10px;
      margin-top: 20px;
    }
    .summary p {
      margin: 5px 0;
      color: #333;
      font-size: 15px;
    }
.btn {
  display: block;            /* Makes the button take full width like a block */
  margin-left: 160px;         /* Moves it to the right */
  margin-top: 25px;
  width: fit-content;        /* Shrinks the button width to its content */
  padding: 12px 24px;
  background: #2563eb;
  color: white;
  border: none;
  border-radius: 8px;
  font-size: 16px;
  cursor: pointer;
  transition: background 0.3s ease;
}

    .btn:hover {
      background: #1e40af;
    }
  </style>
</head>
<body>

<div class="booking-container">
  <form method="POST">
    <div class="car-details">
      <img src="<?php echo htmlspecialchars($car['image_path']); ?>" alt="Car Image">
    </div>

    <div class="info">
      <h2><?php echo htmlspecialchars($car['brand'] . ' ' . $car['model']); ?></h2>
      <p><strong>Seats:</strong> <?php echo $car['seats']; ?></p>
      <p><strong>Rent per Hour:</strong> <span id="rent"><?php echo $car['rent_per_hour']; ?> </span> Tk</p>
    </div>

    <div class="input-group">
      <label for="start_time">Start Date & Time</label>
      <input type="datetime-local" id="start_time" name="start_time" required>
    </div>

    <div class="input-group">
      <label for="end_time">End Date & Time</label>
      <input type="datetime-local" id="end_time" name="end_time" required>
    </div>

    <div class="summary">
      <p><strong>Total Time:</strong> <span id="totalHours">0</span> hours</p>
      <p><strong>Total Fare:</strong> <span id="totalFare">0</span> Tk</p>
    </div>

    <input type="hidden" name="car_id" value="<?php echo $car['id']; ?>">
    <button type="submit" class="btn" onclick="return confirm('Do you want to confirm this booking?')">Confirm Booking</button>
  </form>
</div>

<script>
  const rent = parseFloat(document.getElementById('rent').textContent);
  const startInput = document.getElementById('start_time');
  const endInput = document.getElementById('end_time');
  const totalHoursEl = document.getElementById('totalHours');
  const totalFareEl = document.getElementById('totalFare');

  function calculateFare() {
    const start = new Date(startInput.value);
    const end = new Date(endInput.value);
    if (start && end && end > start) {
      const ms = end - start;
      const hours = Math.ceil(ms / (1000 * 60 * 60));
      totalHoursEl.textContent = hours;
      totalFareEl.textContent = (hours * rent).toFixed(2);
    } else {
      totalHoursEl.textContent = "0";
      totalFareEl.textContent = "0";
    }
  }

  startInput.addEventListener('change', calculateFare);
  endInput.addEventListener('change', calculateFare);
</script>

</body>
</html>
