<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$database = "car_rental";

$conn = new mysqli($host, $user, $password, $database);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Register | Car Rental</title>
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



.custom-header .home-link a {
  color: white;
  font-weight: bold;
  font-size: 16px;
  text-decoration: none;
}

.custom-header {
  width: 100%;
  background: #002244;
  color: white;
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 15px 30px;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
  position: fixed;
  top: 0;
  left: 0;
  z-index: 999;
}


.custom-header .logo-box {
  background: white;
  color: #002244;
  padding: 8px 16px;
  border-radius: 10px;
  font-weight: bold;
  font-size: 16px;
}

/* Push the page content down below the header */
.login-container {
  margin-top: 100px;
}

.header {
  display: flex;
  justify-content: space-between; /* Moves items to left & right */
  align-items: center;
  padding: 20px 0px;
  background: #0b4d91;
  color: white;
  position: fixed;
  width: 100%;
  top: 0;
  z-index: 999;
}

.nav-left a {
  color: white;
  text-decoration: none;
  font-size: 18px;
}

.logo-box .logo {
  height: 40px; /* Adjust logo size */
}


    .register-container {
      background: white;
      padding: 40px;
      border-radius: 15px;
      box-shadow: 0 10px 30px rgba(0,0,0,0.2);
      width: 100%;
      max-width: 450px;
       margin-top: 65px;
    }

    h2 {
      text-align: center;
      margin-bottom: 20px;
      color: #0b4d91;
    }

    .switch-role {
      display: flex;
      justify-content: center;
      margin-bottom: 20px;
    }

    .switch {
      position: relative;
      width: 200px;
      height: 40px;
      background: #e0e0e0;
      border-radius: 20px;
      display: flex;
      align-items: center;
      justify-content: space-between;
      overflow: hidden;
    }

    .switch input[type="radio"] {
      display: none;
    }

    .switch label {
      flex: 1;
      text-align: center;
      z-index: 2;
      cursor: pointer;
      line-height: 40px;
      font-weight: bold;
      transition: color 0.3s;
    }

.logo-box {
  background: white;
  padding: 0px 10px;
  border-radius: 10px;
  margin-right: 60px;
  width: 60px;         /* Fixed width */
  height: 30px;         /* Fixed height */
  overflow: hidden;     /* Important: hides overflow */
  display: flex;
  align-items: center;
  justify-content: center;
}

.logo {
  height: 50%;
  width: auto;
  transform: scale(2.5); /* Zoom the image inside the box */
  object-fit: cover;
}





    .switch .slider {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%;
      width: 50%;
      background: #0b4d91;
      border-radius: 20px;
      transition: left 0.3s ease;
      z-index: 1;
    }

    .switch .active {
      color: white !important;
    }

    input[type="text"], input[type="email"], input[type="password"], input[type="date"] {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
    }

    .password-wrapper {
      position: relative;
    }

    .toggle-password {
      position: absolute;
      top: 32%;
      right: 1px;
      transform: translateY(-50%);
      font-size: 14px;
      color: #007bff;
      cursor: pointer;
      user-select: none;
    }

    .terms {
      display: flex;
      align-items: center;
      font-size: 14px;
      margin-bottom: 10px;
    }

    .terms input {
      margin-right: 8px;
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

    .login-link {
      text-align: center;
      margin-top: 12px;
      font-size: 14px;
    }

    .login-link a {
      color: #007bff;
      text-decoration: none;
    }

  </style>
<header class="custom-header">
  <div class="home-link">
    <a href="index.php">🏠 Home</a>
  </div>
  <div class="logo-box">
    <img src="Images/GrabItLogo.png" alt="Logo" class="logo" />
  </div>
</header>




  <div class="register-container">
    <h2>Create an Account</h2>

    <form id="registerForm">
      <div class="switch-role">
        <div class="switch">
          <input type="radio" id="role_employee" name="role" value="employee" checked>
          <input type="radio" id="role_user" name="role" value="user">

          <label for="role_employee" id="label_employee">Employee</label>
          <label for="role_user" id="label_user">User</label>

          <div class="slider" id="slider"></div>
        </div>
      </div>

      <input type="text" name="name" placeholder="Full Name" required />
      <input type="email" name="email" placeholder="Email Address" required />
      <input type="text" name="phone" placeholder="Phone Number" required />
      <input type="date" name="dob" id="dob" required max="">
      <input type="text" name="city" placeholder="City" required />

      <div class="password-wrapper">
        <input type="password" name="password" id="password" placeholder="Password" required />
        <span class="toggle-password" onclick="togglePassword('password', this)">Show</span>
      </div>

      <div class="password-wrapper">
        <input type="password" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required />
        <span class="toggle-password" onclick="togglePassword('confirm_password', this)">Show</span>
      </div>

      <div class="terms">
        <input type="checkbox" name="terms" required>
        <label>I accept the Terms and Conditions</label>
      </div>

      <div id="error-message" class="error"></div>

      <button type="submit">Register</button>
    </form>

    <div class="login-link">
      Already have an account? <a href="login.php">Login here</a>
    </div>
  </div>

<script>
  // Show/hide password toggle
  function togglePassword(id, el) {
    const input = document.getElementById(id);
    if (input.type === 'password') {
      input.type = 'text';
      el.innerText = 'Hide';
    } else {
      input.type = 'password';
      el.innerText = 'Show';
    }
  }

  // Role toggle
  function selectRole(role) {
    const slider = document.getElementById('slider');
    const empLabel = document.getElementById('label_employee');
    const userLabel = document.getElementById('label_user');

    if (role === 'employee') {
      document.getElementById('role_employee').checked = true;
      slider.style.left = '0%';
      empLabel.classList.add('active');
      userLabel.classList.remove('active');
    } else {
      document.getElementById('role_user').checked = true;
      slider.style.left = '50%';
      userLabel.classList.add('active');
      empLabel.classList.remove('active');
    }
  }

  // Role click handlers
  document.getElementById('label_employee').addEventListener('click', () => selectRole('employee'));
  document.getElementById('label_user').addEventListener('click', () => selectRole('user'));

  // Set max DOB to today - 18 years
const dobInput = document.getElementById('dob');
const today = new Date();
today.setFullYear(today.getFullYear() - 18);
dobInput.max = today.toISOString().split('T')[0];


  // Initialize on load
  window.onload = () => {
    const role = document.getElementById('role_user').checked ? 'user' : 'employee';
    selectRole(role);
  };

  // ✅ Basic Client-side validation
function validateForm(formData) {
  const errorDiv = document.getElementById('error-message');
  errorDiv.innerHTML = '';

  const name = formData.get('name')?.trim();
  const email = formData.get('email')?.trim();
  const phone = formData.get('phone')?.trim();
  const dob = formData.get('dob')?.trim();
  const city = formData.get('city')?.trim();
  const password = formData.get('password')?.trim();
  const confirmPassword = formData.get('confirm_password')?.trim();
  const termsAccepted = formData.get('terms');

  // Step-by-step validation:
  if (!name || !email || !phone || !dob || !city || !password || !confirmPassword) {
    errorDiv.innerHTML = "All fields are required.";
    return false;
  }

  // Age check (18+)
  const birthDate = new Date(dob);
  const today = new Date();
  let age = today.getFullYear() - birthDate.getFullYear();
  const m = today.getMonth() - birthDate.getMonth();
  if (m < 0 || (m === 0 && today.getDate() < birthDate.getDate())) {
    age--;
  }
  if (isNaN(age) || age < 18) {
    errorDiv.innerHTML = "You must be at least 18 years old.";
    return false;
  }

  // Email format check
  const emailRegex = /^[a-zA-Z0-9._%+-]+@(gmail\.com|outlook\.com|hotmail\.com|yahoo\.com)$/;
  if (!emailRegex.test(email)) {
    errorDiv.innerHTML = "Email must be a valid Gmail, Outlook, Hotmail, or Yahoo address.";
    return false;
  }

  // Phone number check (BD format)
  const phoneRegex = /^(013|014|015|016|017|018|019)[0-9]{8}$/;
  if (!phoneRegex.test(phone)) {
    errorDiv.innerHTML = "Phone number must be 11 digits";
    return false;
  }

  // Password length
  if (password.length < 8) {
    errorDiv.innerHTML = "Password must be at least 8 characters long.";
    return false;
  }

  // Password match
  if (password !== confirmPassword) {
    errorDiv.innerHTML = "Passwords do not match.";
    return false;
  }

  // Terms checkbox
  if (!termsAccepted) {
    errorDiv.innerHTML = "You must accept the Terms and Conditions.";
    return false;
  }

  return true;
}




  // ✅ Form submit handler
  document.getElementById('registerForm').addEventListener('submit', function (e) {
  e.preventDefault();

  const formData = new FormData(this);
  const isValid = validateForm(formData);

  if (!isValid) return;

  fetch('process_register.php', {
    method: 'POST',
    body: formData
  })
    .then(res => {
      if (!res.ok) {
        throw new Error(`HTTP error! Status: ${res.status}`);
      }
      return res.json(); // Try to parse as JSON
    })
    .then(data => {
  const errorDiv = document.getElementById('error-message');
  errorDiv.innerHTML = '';
  
if (data.success) {
  document.body.innerHTML = `
    <style>
      body {
        margin: 0;
        height: 100vh;
        background: linear-gradient(135deg, #1e3c72, #2a5298);
        display: flex;
        justify-content: center;
        align-items: center;
        font-family: 'Segoe UI', sans-serif;
        color: white;
        overflow: hidden;
      }

      .success-box {
        background: rgba(255, 255, 255, 0.1);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 20px;
        padding: 40px 30px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
        text-align: center;
        max-width: 400px;
        width: 90%;
        color: #ffffff;
        animation: fadeIn 0.8s ease-in-out;
      }

      .success-box h2 {
        margin-bottom: 15px;
        font-size: 26px;
      }

      .success-box p {
        font-size: 16px;
        margin-top: 10px;
      }

      #countdown {
        font-weight: bold;
        font-size: 18px;
        color: #ffd700;
      }

      @keyframes fadeIn {
        from { opacity: 0; transform: scale(0.95); }
        to { opacity: 1; transform: scale(1); }
      }
    </style>

    <div class="success-box">
      <h2 style="color: white;">🎉 Registration Successful!</h2>

      <p>Redirecting to login in <span id="countdown">3</span> seconds...</p>
    </div>
  `;

  let seconds = 3;
  const countdownEl = document.getElementById('countdown');
  const interval = setInterval(() => {
    seconds--;
    countdownEl.textContent = seconds;
    if (seconds <= 0) {
      clearInterval(interval);
      window.location.href = 'login.php';
    }
  }, 1000);
}


 else {
    errorDiv.innerHTML = data.errors.join('<br>');
  }
});

});

</script>

</body>
</html>
