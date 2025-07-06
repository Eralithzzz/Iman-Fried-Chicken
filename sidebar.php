<?php
// Start session and connect DB if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'oracle.php';

// Determine if this staff is a manager
$isManager = false;
if (isset($_SESSION['staff_id'])) {
    $sid = $_SESSION['staff_id'];
    $check = oci_parse($db_conn, "SELECT COUNT(*) AS CNT FROM staff WHERE manager_id = :sid");
    oci_bind_by_name($check, ":sid", $sid);
    oci_execute($check);
    $res = oci_fetch_assoc($check);
    $isManager = ($res['CNT'] > 0);
}
?>

<div class="sidebar">
  <div class="logo">
    <img src="images/logo2.png" alt="IFC Logo" class="logo-img">
    <span class="logo-name">IFC Staff</span>
  </div>
  <ul class="nav-links">
    <li>
      <a href="Staff Page.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'Staff Page.php') ? 'active' : ''; ?>">
        <i class="fas fa-utensils"></i>
        <span class="link-name">Menu</span>
      </a>
    </li>
    <li>
      <a href="reports.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'reports.php') ? 'active' : ''; ?>">
        <i class="fas fa-chart-line"></i>
        <span class="link-name">Reports</span>
      </a>
    </li>
    
    <?php if ($isManager): ?>
    <li>
      <a href="manager.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manager.php') ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i>
        <span class="link-name">Manager</span>
      </a>
    </li>
    
    <?php endif; ?>
    <li>
  <a href="#" id="logout-btn">
    <i class="fas fa-sign-out-alt"></i>
    <span class="link-name">Logout</span>
  </a>
</li>
  </ul>
</div>

<style>
  /* Fried Chicken Shop Sidebar */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 80px;
  background: #8B0000; /* Dark red like ketchup */
  background-image: linear-gradient(
    to bottom,
    #8B0000,
    #A52A2A
  );
  padding: 20px 0;
  transition: width 0.3s ease;
  overflow: hidden;
  z-index: 100;
  border-right: 3px dashed #FFD700; /* Golden dashed border */
  box-shadow: 5px 0 15px rgba(0, 0, 0, 0.3);
}

.sidebar:hover {
  width: 220px;
}

.sidebar .logo {
  color: #FFD700; /* Golden text */
  text-align: center;
  margin-bottom: 30px;
  padding-bottom: 15px;
  border-bottom: 2px solid rgba(255, 215, 0, 0.3);
}

.sidebar .logo-name {
  font-size: 22px;
  font-weight: 700;
  display: none;
  font-family: 'Impact', fantasy, sans-serif;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.sidebar:hover .logo-name {
  display: inline;
}

.sidebar .nav-links {
  list-style: none;
  padding: 0;
  margin: 0;
}

.sidebar .nav-links li {
  margin-bottom: 15px;
  position: relative;
}

.sidebar .nav-links li::before {
  content: "🍗";
  position: absolute;
  left: -25px;
  top: 50%;
  transform: translateY(-50%);
  opacity: 0;
  transition: opacity 0.3s ease;
}

.sidebar:hover .nav-links li::before {
  opacity: 1;
}

.sidebar .nav-links a {
  color: #FFEBCD; /* Blanched almond text */
  text-decoration: none;
  display: flex;
  align-items: center;
  padding: 12px 15px;
  transition: all 0.3s ease;
  border-radius: 8px;
  margin: 0 10px;
  background: rgba(255, 215, 0, 0.1);
}

.sidebar .nav-links a:hover,
.sidebar .nav-links a.active {
  background: linear-gradient(
    to right,
    rgba(255, 215, 0, 0.3),
    rgba(218, 165, 32, 0.3)
  );
  color: white;
  transform: translateX(5px);
  box-shadow: 0 5px 15px rgba(255, 215, 0, 0.2);
}

.sidebar .nav-links i {
  font-size: 22px;
  width: 30px;
  text-align: center;
  color: #FFD700;
}

.sidebar .nav-links .link-name {
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.3s ease;
  margin-left: 15px;
  font-weight: 600;
  font-family: 'Comic Sans MS', cursive, sans-serif;
}

.sidebar:hover .nav-links .link-name {
  opacity: 1;
}

/* Logo image styling */
.logo-img {
  width: 50px;
  height: 50px;
  object-fit: cover;
  border-radius: 50%;
  border: 2px solid #FFD700;
  display: block;
  margin: 0 auto 15px auto;
  transition: all 0.3s ease;
  background: white;
  padding: 2px;
}

.sidebar:hover .logo-img {
  transform: scale(1.1);
  box-shadow: 0 0 15px #FFD700;
}

/* Main Content */
.main-content {
  margin-left: 80px;
  padding: 20px;
  transition: margin-left 0.3s ease;
  background-color: #FFF5E1; /* Light creamy background */
  background-image: url('https://transparenttextures.com/patterns/food.png');
}

.sidebar:hover ~ .main-content {
  margin-left: 220px;
}

/* Add some chicken-themed decorations */
.sidebar::after {
  content: "";
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  width: 60px;
  height: 60px;
  background-image: url('data:image/svg+xml;utf8,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><text x="50%" y="50%" font-family="Arial" font-size="40" fill="%23FFD700" text-anchor="middle" dominant-baseline="middle">🍗</text></svg>');
  opacity: 0.2;
  transition: all 0.3s ease;
}

.sidebar:hover::after {
  opacity: 0.1;
  transform: translateX(-50%) scale(1.5);
}
</style>

<script>
document.getElementById('logout-btn').addEventListener('click', function(e) {
  e.preventDefault();
  
  // Ask for confirmation
  if (confirm('Are you sure you want to logout?')) {
    // Send an AJAX request to logout
    fetch('logout.php', {
      method: 'POST',
      credentials: 'same-origin'
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        window.location.href = 'login.php'; // Redirect to login page
      } else {
        alert('Logout failed. Please try again.');
      }
    })
    .catch(error => {
      console.error('Error:', error);
      alert('An error occurred during logout.');
    });
  }
});
</script>