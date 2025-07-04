<!-- sidebar.php -->
<!-- sidebar.php -->
<div class="sidebar">
  <div class="logo">
  <img src="images/nasil.jpg" alt="IFC Logo" class="logo-img">
  <span class="logo-name">IFC Staff</span>
</div>
  <ul class="nav-links">
    <li>
      <a href="menu_management.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'menu_management.php') ? 'active' : ''; ?>">
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
    <li>
      <a href="orders.php" class="<?php echo (basename($_SERVER['PHP_SELF']) == 'manager.php') ? 'active' : ''; ?>">
        <i class="fas fa-receipt"></i>
        <span class="link-name">Manager</span>
      </a>
    </li>
    
  </ul>
</div>

<style>
  /* Sidebar Base Styles */
.sidebar {
  position: fixed;
  top: 0;
  left: 0;
  height: 100%;
  width: 80px;
  background: #111;
  padding: 20px 0;
  transition: width 0.3s ease;
  overflow: hidden;
  z-index: 100;
}

.sidebar:hover {
  width: 220px;
}

.sidebar .logo {
  color: white;
  text-align: center;
  margin-bottom: 30px;
}

.sidebar .logo-name {
  font-size: 18px;
  font-weight: 700;
  display: none;
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
  margin-bottom: 20px;
}

.sidebar .nav-links a {
  color: white;
  text-decoration: none;
  display: flex;
  align-items: center;
  padding: 10px 15px;
  transition: background 0.2s ease;
}

.sidebar .nav-links a:hover,
.sidebar .nav-links a.active {
  background: #1abc9c;
  border-radius: 8px;
}

.sidebar .nav-links i {
  font-size: 20px;
  width: 30px;
  text-align: center;
}

.sidebar .nav-links .link-name {
  white-space: nowrap;
  opacity: 0;
  transition: opacity 0.3s ease;
  margin-left: 10px;
}

.sidebar:hover .nav-links .link-name {
  opacity: 1;
}

/* Main Content */
.main-content {
  margin-left: 80px;
  padding: 20px;
  transition: margin-left 0.3s ease;
}

.sidebar:hover ~ .main-content {
  margin-left: 220px;
}

.logo-img {
  width: 60px;              /* Set width */
  height: 60px;             /* Set height */
  object-fit: cover;        /* Crop image to fit */
  border-radius: 50%;       /* Makes it circular */
  border: 2px solid #1abc9c; /* Optional: adds a nice colored border */
  display: block;
  margin: 0 auto 10px auto; /* Center and add spacing below */
}


.sidebar:hover .logo-img {
  width: 50px;
  height: 50px;
}

</style>
