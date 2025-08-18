<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Login | Car Rental</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <style>
    body {
      background: linear-gradient(to right, #0b4d91, #007bff);
      font-family: 'Segoe UI', sans-serif;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }

    .login-container {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 450px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #0b4d91;
    }

    .role-switch {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .switch {
      position: relative;
      display: flex;
      width: 100%;
      max-width: 360px;
      background: #e0e0e0;
      border-radius: 20px;
      overflow: hidden;
    }

    .switch input {
      display: none;
    }

    .switch label {
      flex: 1;
      line-height: 40px;
      text-align: center;
      font-weight: bold;
      color: #333;
      cursor: pointer;
      z-index: 2;
      position: relative;
      transition: color 0.3s ease;
    }

    .switch input:checked + label {
      color: white;
    }

    /* Header container */
.custom-header {
  width: 100%;
  background: #002244;
  color: white;
  display: flex;
  justify-content: space-between; /* Push Home left, Logo right */
  align-items: center;
  padding: 15px 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 999;
}

/* Wraps logo and home together like in register */
.left-container {
  display: flex;
  align-items: center;
  gap: 20px;
}

.logo-box {
  background: white;
  padding: 0px 10px;
  border-radius: 10px;
  margin-right: 60px;
  width: 80px;         /* Fixed width */
  height: 50px;         /* Fixed height */
  overflow: hidden;     /* Important: hides overflow */
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo {
  height: 50%;
  width: auto;
  transform: scale(4); /* Zoom the image inside the box */
  object-fit: cover;
}
/* Home link */
.home-link a {
  color: white;
  font-weight: bold;
  font-size: 16px;
  text-decoration: none;
}

/* Push form below the fixed header */
.login-container {
  margin-top: 80px;
}


    .slider {
      position: absolute;
      top: 0;
      left: 0;
      width: 33.33%;
      height: 100%;
      background: #0b4d91;
      border-radius: 20px;
      transition: left 0.3s ease;
      z-index: 1;
    }

    input[type="email"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      box-sizing: border-box;
    }

    .password-wrapper {
      position: relative;
      width: 100%;
      margin-bottom: 15px;
    }

    .password-wrapper input[type="password"],
    .password-wrapper input[type="text"] {
      width: 100%;
      padding: 12px;
      padding-right: 50px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      box-sizing: border-box;
    }

    .toggle-password {
      position: absolute;
      right: 15px;
      top: 50%;
      transform: translateY(-50%);
      cursor: pointer;
      font-size: 14px;
      color: #007bff;
      background: white;
      padding-left: 5px;
      z-index: 3;
    }

    .error {
      color: red;
      font-size: 14px;
      margin-bottom: 10px;
      text-align: center;
    }

    button {
      width: 100%;
      padding: 12px;
      background: #007bff;
      color: white;
      font-size: 16px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      transition: 0.3s ease;
    }

    button:hover {
      background: #0056b3;
    }

    .forgot-password {
      text-align: right;
      margin-top: 10px;
    }

    .forgot-password a {
      color: #007bff;
      text-decoration: none;
      font-size: 14px;
    }

    .register-link {
      text-align: center;
      margin-top: 15px;
      font-size: 14px;
    }

    .register-link a {
      color: #007bff;
      text-decoration: none;
    }
  </style>
</head>
<body>

<header class="custom-header">
  <div class="home-link">
    <a href="index.php">🏠 Home</a>
  </div>
  <div class="logo-box">
    <img src="Images/GrabItLogo.png" alt="Logo" class="logo" />
  </div>
</header>



  <div class="login-container">
    <h2>Login to Your Account</h2>

    <form id="loginForm">
      <div class="role-switch">
        <div class="switch">
          <input type="radio" id="admin" name="role" value="admin" checked>
          <label for="admin">Admin</label>
          <input type="radio" id="employee" name="role" value="employee">
          <label for="employee">Employee</label>
          <input type="radio" id="user" name="role" value="user">
          <label for="user">User</label>
          <div class="slider" id="roleSlider"></div>
        </div>
      </div>

      <input type="email" name="email" placeholder="Email Address" required />
      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="Password" required />
        <span class="toggle-password" onclick="togglePassword()">Show</span>
      </div>

      <div class="error" id="error-message"></div>

      <button type="submit">Login</button>

      <div class="forgot-password">
        <a href="forgot_password.php">Forgot Password?</a>
      </div>

      <div class="register-link">
        Don't have an account? <a href="register.php">Register here</a>
      </div>
    </form>
  </div>

  <script>
    function togglePassword() {
      const input = document.getElementById('password');
      const toggle = document.querySelector('.toggle-password');
      if (input.type === 'password') {
        input.type = 'text';
        toggle.innerText = 'Hide';
      } else {
        input.type = 'password';
        toggle.innerText = 'Show';
      }
    }

    function updateSlider() {
      const admin = document.getElementById('admin');
      const employee = document.getElementById('employee');
      const user = document.getElementById('user');
      const slider = document.getElementById('roleSlider');
      const labels = document.querySelectorAll('.switch label');

      labels.forEach(label => label.style.color = '#333');

      if (admin.checked) {
        slider.style.left = '0%';
        labels[0].style.color = 'white';
      } else if (employee.checked) {
        slider.style.left = '33.33%';
        labels[1].style.color = 'white';
      } else if (user.checked) {
        slider.style.left = '66.66%';
        labels[2].style.color = 'white';
      }
    }

    document.querySelectorAll('input[name="role"]').forEach(radio => {
      radio.addEventListener('change', updateSlider);
    });

    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const formData = new FormData(this);

      fetch('process_login.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
.then(data => {
  const errorDiv = document.getElementById('error-message');
  errorDiv.innerHTML = '';
  if (data.success) {
    window.location.href = data.redirect;
  } else {
    errorDiv.innerText = data.message;
  }
})

      .catch(() => {
        document.getElementById('error-message').innerText = 'An error occurred. Please try again.';
      });
    });

    // Initialize on load
    window.onload = updateSlider;
  </script>
</body>
</html>
