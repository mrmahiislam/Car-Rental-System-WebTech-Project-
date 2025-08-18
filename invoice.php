<?php
session_start();

// Determine role-based dashboard path
$role = $_SESSION['role'] ?? null;
if (!$role) die("Error: Session role not set.");

switch ($role) {
  case 'employee':
    $dashboard = 'employee_dashboard.php';
    break;
  case 'admin':
    $dashboard = 'admin_dashboard.php';
    break;
  default:
    $dashboard = 'user_dashboard.php';
}

$conn = new mysqli("localhost", "root", "", "car_rental");
$id = intval($_GET['booking_id']);
$q = $conn->query("SELECT b.*, c.brand, c.model FROM bookings b JOIN cars c ON b.car_id = c.id WHERE b.id = {$id}");
if (!$b = $q->fetch_assoc()) die("Booking not found.");

$paid50 = $b['is_paid'] ? 'Yes' : 'No';
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8" />
  <title>Invoice #<?=$id?></title>
  <style>
    body {
      font-family: Segoe UI, Tahoma, Geneva, Verdana, sans-serif;
      background: #f9f9f9;
      padding: 30px;
      margin: 0;
    }
    .invoice {
      max-width: 600px;
      margin: auto;
      background: #fff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
      position: relative;
    }
    h2 {
      text-align: center;
      color: #333;
    }
    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }
    td, th {
      padding: 10px;
      border-bottom: 1px solid #ddd;
      text-align: left;
    }
    .total {
      font-weight: bold;
    }
    .status {
      margin: 20px 0;
      color: <?=$b['is_paid'] ? 'green' : 'red'?>;
      font-weight: 600;
      text-align: center;
    }
    p.thankyou {
      text-align: center;
      font-style: italic;
      margin-top: 40px;
    }
    .btn-container {
      display: flex;
      justify-content: center;
      gap: 20px;
      margin-top: 40px;
    }
    button {
      background: #2563eb;
      color: white;
      border: none;
      padding: 12px 24px;
      border-radius: 8px;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #1e40af;
    }
  </style>
</head>
<body>

  <div class="invoice" id="invoiceContent">
    <h2>Invoice #<?=$id?></h2>
    <table>
      <tr><th>Car</th><td><?=$b['brand']." ".$b['model']?></td></tr>
      <tr><th>Start</th><td><?=$b['start_time']?></td></tr>
      <tr><th>End</th><td><?=$b['end_time']?></td></tr>
      <tr><th>Total Hours</th><td><?=$b['total_hours']?></td></tr>
      <tr class="total"><th>Total Fare</th><td>Tk <?=number_format($b['total_fare'],2)?></td></tr>
      <tr><th>50% Paid?</th><td><?=$paid50?></td></tr>
    </table>
    <p class="status"><?= $b['is_paid'] ? '✅ Pre‑booking fee received.' : '⚠️ Awaiting pre‑booking payment.' ?></p>
    <p class="thankyou">Thank you for booking with us!</p>

    <div class="btn-container">
      <button id="downloadBtn">Download Invoice (PDF)</button>
      <button id="homeBtn">Home</button>
    </div>
  </div>

  <!-- jsPDF and AutoTable -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  <script>
    window.onload = function() {
      const { jsPDF } = window.jspdf;

      document.getElementById('downloadBtn').addEventListener('click', () => {
        const doc = new jsPDF();

        doc.setFontSize(20);
        doc.setTextColor("#2563eb");
        doc.text("Car Rental System", 105, 15, null, null, "center");

        doc.setDrawColor("#2563eb");
        doc.setLineWidth(1);
        doc.line(15, 22, 195, 22);

        doc.setFontSize(14);
        doc.setTextColor("#000");
        doc.text(`Invoice #<?=$id?>`, 15, 35);

        const columns = ["Item", "Details"];
        const rows = [
          ["Car", "<?=$b['brand']." ".$b['model']?>"],
          ["Start Time", "<?=$b['start_time']?>"],
          ["End Time", "<?=$b['end_time']?>"],
          ["Total Hours", "<?=$b['total_hours']?>"],
          ["Total Fare", "Tk <?=number_format($b['total_fare'],2)?>"],
          ["50% Paid?", "<?=$paid50?>"]
        ];

        doc.autoTable({
          startY: 40,
          head: [columns],
          body: rows,
          theme: 'grid',
          headStyles: { fillColor: '#2563eb' },
          styles: { fontSize: 12 }
        });

        let paidText = <?= $b['is_paid'] ? '`✅ Pre‑booking fee received.`' : '`⚠️ Awaiting pre‑booking payment.`' ?>;
        doc.setFontSize(12);
        doc.setTextColor(<?= $b['is_paid'] ? "'green'" : "'red'" ?>);
        doc.text(paidText, 15, doc.lastAutoTable.finalY + 15);

        doc.setTextColor("#000");
        doc.setFontSize(11);
        doc.text("Thank you for booking with us!", 105, doc.lastAutoTable.finalY + 30, null, null, "center");

        doc.save(`invoice_#<?=$id?>.pdf`);
      });

      document.getElementById('homeBtn').addEventListener('click', () => {
        window.location.href = '<?= $dashboard ?>';
      });
    };
  </script>

</body>
</html>
