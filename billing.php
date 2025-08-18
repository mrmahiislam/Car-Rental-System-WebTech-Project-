<?php
session_start();
$conn = new mysqli("localhost", "root", "", "car_rental");

$booking_id = $_GET['booking_id'] ?? null;
if (!$booking_id) {
  die("Invalid booking.");
}

$paid = false;

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['pay_now'])) {
  $stmt = $conn->prepare("UPDATE bookings SET is_paid = 1 WHERE id = ?");
  $stmt->bind_param("i", $booking_id);
  $stmt->execute();

  if (!$stmt) {
    die("Payment update failed: " . $conn->error);
  }

  header("Location: billing.php?booking_id=$booking_id&paid=1");
  exit;
}


if (isset($_GET['paid']) && $_GET['paid'] == '1') {
  $paid = true;
}

// Get booking info
$result = $conn->query("SELECT b.*, c.brand, c.model FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = $booking_id");
$booking = $result->fetch_assoc();
if (!$booking) {
  die("Booking not found.");
}

$car = $booking['brand'] . ' ' . $booking['model'];
$total = number_format($booking['total_fare'], 2);
$half = number_format($booking['total_fare'] / 2, 2);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Billing - <?php echo htmlspecialchars($car); ?></title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: #ecf1f8;
      padding: 40px;
      text-align: center;
    }
    .bill-box {
      background: #fff;
      max-width: 500px;
      margin: auto;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 6px 20px rgba(0, 0, 0, 0.1);
    }
    h2 {
      color: #222;
    }
    .info {
      margin: 20px 0;
      font-size: 18px;
      color: #444;
    }
    .btn {
      display: inline-block;
      padding: 12px 20px;
      margin-top: 25px;
      margin: 10px 8px;
      background-color: #2563eb;
      color: white;
      border: none;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.3s ease;
    }
    .btn:hover {
      background-color: #1e40af;
    }
    .success-msg {
      font-size: 20px;
      font-weight: bold;
      color: green;
      animation: pop 0.6s ease-out;
    }
    @keyframes pop {
      0% { transform: scale(0.5); opacity: 0; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>

<div class="bill-box">
  <h2>Booking Confirmation</h2>
  <div class="info"><strong>Car:</strong> <?php echo htmlspecialchars($car); ?></div>
  <div class="info"><strong>Total Fare:</strong> Tk <?php echo $total; ?></div>
  <div class="info"><strong>Amount Due Now (50%):</strong> Tk <?php echo $half; ?></div>

  <?php if (!$paid): ?>
    <form method="POST">
      <input type="hidden" name="pay_now" value="1">
      <button type="submit" class="btn">Pay Now (Pay 50%)</button>
    </form>
  <?php else: ?>
    <div class="success-msg">✅ Payment Successful!</div>
    <script>
      setTimeout(() => {
        document.querySelector('.success-msg').insertAdjacentHTML('afterend', `
         <a class="btn" href="invoice.php?booking_id=<?php echo $booking_id; ?>">Get Invoice</a>
          <a class="btn" href="user_dashboard.php">Home</a>
        `);
      }, 3000);
    </script>
  <?php endif; ?>
</div>

</body>
</html>
