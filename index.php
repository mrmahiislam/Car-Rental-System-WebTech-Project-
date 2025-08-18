<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>GrabIt</title>
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">

  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Roboto', sans-serif;
      background-color: #f9f9f9;
      scroll-behavior: smooth;
    }

    header {
      background: linear-gradient(to right, #0b4d91, #007bff);
      color: white;
      padding: 40px 20px;
      text-align: center;
      box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }

    nav {
      background: #fff;
      box-shadow: 0 2px 5px rgba(0,0,0,0.1);
      text-align: center;
      padding: 12px 0;
      position: sticky;
      top: 0;
      z-index: 1000;
    }

    nav a {
      color: #0b4d91;
      text-decoration: none;
      margin: 0 20px;
      font-weight: bold;
      font-size: 16px;
      transition: color 0.3s ease;
    }

    nav a:hover {
      color: #007bff;
    }

.hero {
  position: relative;
  height: 75vh;
  display: flex;
  justify-content: center;
  align-items: center;
  color: #fff;
  text-align: center;
  overflow: hidden;
}

.hero-slideshow {
  position: absolute;
  top: 0; left: 0;
  width: 100%;
  height: 100%;
  background-position: center;
  background-size: cover;
  transition: background-image 1s ease-in-out;
  z-index: 0;
}

.hero::after {
  content: '';
  position: absolute;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.5);
  z-index: 1;
}

.hero-content {
  position: relative;
  z-index: 2;
}


    .hero h1 {
      font-size: 3.5rem;
      z-index: 1;
      text-shadow: 2px 2px 10px black;
    }

    .cta-buttons {
      margin-top: 20px;
      z-index: 1;
    }

    .cta-buttons a {
      background: #007bff;
      color: white;
      padding: 12px 25px;
      margin: 0 10px;
      border-radius: 30px;
      text-decoration: none;
      transition: background 0.3s ease;
    }

header {
  position: relative;
  background: linear-gradient(to right, #0b4d91, #007bff);
  color: white;
  padding: 20px 20px;
  box-shadow: 0 4px 10px rgba(0,0,0,0.1);
  text-align: center; /* center the text */
}

.logo {
  position: absolute;
  left: 20px;
  top: 50%;
  transform: translateY(-50%);
  height: 80px; /* adjust size as needed */
}

.header-text h1 {
  margin: 0;
  font-size: 3rem;
}

.header-text p {
  margin: 6px 0 0 0;
  font-size: 1rem;
  font-weight: normal;
}


    .cta-buttons a:hover {
      background: #0056b3;
    }

    .section {
      padding: 60px 20px;
      text-align: center;
    }

    .section h2 {
      font-size: 2.5rem;
      margin-bottom: 20px;
      color: #0b4d91;
    }

    .section p {
      font-size: 1.1rem;
      color: #444;
      max-width: 700px;
      margin: 0 auto;
    }

    .car-gallery {
      margin-top: 40px;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
      gap: 25px;
      padding: 0 20px;
    }

    .car-card {
      background: white;
      border-radius: 12px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      overflow: hidden;
      transition: transform 0.3s ease;
    }

    .car-card:hover {
      transform: translateY(-5px);
    }

    .car-card img {
      width: 100%;
      height: 180px;
      object-fit: cover;
    }

    .car-card h3 {
      margin: 15px 0;
      font-size: 1.3rem;
    }

    footer {
      background: #0b4d91;
      color: white;
      padding: 20px 0;
      text-align: center;
      margin-top: 50px;
    }

    @media(max-width: 600px) {
      .hero h1 {
        font-size: 2rem;
      }

      .cta-buttons a {
        display: block;
        margin: 10px auto;
      }
    }
  </style>
</head>
<body>

  <header>
    <img src="Images/GrabItLogoWhite.png" alt="GrabIt Logo" class="logo" />
 <div class="header-text">
    <h1>GrabIt</h1>
    <p>Drive Your Dreams with Comfort & Style</p>
  </div>
  </header>

  <nav>
    <a href="index.php">Home</a>
    <a href="login.php">Login</a>
    <a href="register.php">Register</a>
    <a href="#cars">Our Cars</a>
    <a href="#contact">Contact</a>
  </nav>

<section class="hero">
  <div class="hero-slideshow" id="heroSlideshow"></div>
  <div class="hero-content">
    <h1>Find the Perfect Car for Your Journey</h1>
    <div class="cta-buttons">
      <a href="register.php">Get Started</a>
      <a href="login.php">Login</a>
    </div>
  </div>
</section>


  <section class="section" id="cars">
    <h2>Popular Rentals</h2>
    <p>Choose from a wide range of vehicles – Sedans, SUVs, Luxury cars and more.</p>

    <div class="car-gallery">
      <div class="car-card">
        <img src="images/car11.jpg" alt="Car 1" />
        <h3>Honda Civic 2023</h3>
      </div>
      <div class="car-card">
        <img src="images/car2.jpg" alt="Car 2" />
        <h3>Toyota Fortuner</h3>
      </div>
      <div class="car-card">
        <img src="images/car3.jpg" alt="Car 3" />
        <h3>BMW X5 M-Sport</h3>
      </div>
      <div class="car-card">
        <img src="images/car14.jpg" alt="Car 4" />
        <h3>Range Rover Evoque</h3>
      </div>
    </div>
  </section>

<section class="section" id="contact" style="background-color: #f1f5f9;">
  <h2>Contact Us</h2>
  <p style="max-width: 600px; margin: 0 auto; color: #555;">
    We'd love to hear from you! Whether you have questions, feedback, or need support — we're here to help.
  </p>

  <div style="
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 30px;
    margin-top: 40px;
  ">
<!-- Email -->
<div style="
  background: white;
  border-radius: 12px;
  box-shadow: 0 4px 12px rgba(0,0,0,0.08);
  width: 280px;
  height: 160px;
  display: flex;
  align-items: center;
  justify-content: center;
  text-align: center;
">
  <div style="
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
  ">
    <h3 style="color: #0b4d91; margin-bottom: 8px;">📧 Email</h3>
    <p style="color: #333; margin: 0;">helpdesk.grabit@outlook.com</p>
  </div>
</div>



    <!-- Phone -->
    <div style="
      background: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      width: 280px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      height: 160px;
    ">
      <h3 style="color: #0b4d91; margin-bottom: 10px;">📞 Phone</h3>
      <p style="color: #333;">+8801315945106</p>
    </div>

    <!-- Address -->
    <div style="
      background: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      width: 280px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      height: 160px;
    ">
      <h3 style="color: #0b4d91; margin-bottom: 10px;">📍 Address</h3>
      <p style="color: #333;">Mirpur-1<br>Dhaka, Bangladesh</p>
    </div>

    <!-- Hours -->
    <div style="
      background: white;
      padding: 25px 30px;
      border-radius: 12px;
      box-shadow: 0 4px 12px rgba(0,0,0,0.08);
      width: 280px;
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      text-align: center;
      height: 160px;
    ">
      <h3 style="color: #0b4d91; margin-bottom: 10px;">🕒 Hours</h3>
      <p style="color: #333;">Mon–Sat<br>9:00 AM – 6:00 PM</p>
    </div>
  </div>
</section>



  <footer>
    &copy; <?php echo date("Y"); ?> Car Rental System. All rights reserved.
  </footer>

  <script>
  const images = [
    'images/car-banner.png',
    'images/car1.jpg',
    'images/car2.jpg',
    'images/car3.jpg',
    'images/car4.jpg'
  ];

  let currentIndex = 0;
  const slideshowDiv = document.getElementById('heroSlideshow');

  function changeBackground() {
    slideshowDiv.style.backgroundImage = `url('${images[currentIndex]}')`;
    currentIndex = (currentIndex + 1) % images.length;
  }

  // Initialize
  changeBackground();

  // Change every 5 seconds (5000 ms)
  setInterval(changeBackground, 5000);
</script>


</body>
</html>
