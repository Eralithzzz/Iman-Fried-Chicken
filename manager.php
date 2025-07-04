<?php
require_once 'oracle.php';
session_start();

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$currentManagerId = $_SESSION['staff_id'];

// Check if this staff is a manager
$checkManager = oci_parse($db_conn, "SELECT COUNT(*) AS CNT FROM staff WHERE manager_id = :mid");
oci_bind_by_name($checkManager, ":mid", $currentManagerId);
oci_execute($checkManager);
$row = oci_fetch_assoc($checkManager);

if ($row['CNT'] == 0) {
    echo "Access denied. You are not a manager.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
      <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta charset="UTF-8">
  <title>Manager - Staff Management</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #f8f9fa, #e0f7fa);
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      animation: fadeIn 1s ease-in;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    h1 {
      color: #e74c3c;
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 30px;
    }
    .menu-form {
      background: #fefefe;
      padding: 25px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: border 0.3s;
    }
    input:focus, select:focus {
      border-color: #3498db;
      outline: none;
    }
    button {
      background-color: #2ecc71;
      color: white;
      border: none;
      padding: 12px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    button:hover {
      background-color: #27ae60;
    }
    button.delete {
      background-color: #e74c3c;
    }
    button.delete:hover {
      background-color: #c0392b;
    }
    #menuTable {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background-color: #fafafa;
      color: #555;
    }
    .availability-toggle {
      cursor: pointer;
      font-weight: bold;
    }
    .availability-toggle.unavailable {
      color: #e74c3c;
    }
    .availability-toggle.available {
      color: #2ecc71;
    }
    .spinner {
      border: 4px solid rgba(0, 0, 0, 0.1);
      border-radius: 50%;
      border-top: 4px solid #3498db;
      width: 30px;
      height: 30px;
      animation: spin 1s linear infinite;
      margin: 20px auto;
    }
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }
    img {
      border-radius: 10px;
      transition: transform 0.3s;
    }
    img:hover {
      transform: scale(1.1);
    }
    .edit-btn, .delete-btn {
      padding: 6px 12px;
      margin: 2px;
      border-radius: 6px;
      font-size: 14px;
    }
  </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>
  <div class="container">
    <h2>Manage Your Staff</h2>
    <form method="post">
      <table>
        <thead>
          <tr>
            <th>Name</th>
            <th>Type</th>
            <th>Salary (Monthly)</th>
            <th>Hourly Rate</th>
            <th>Action</th>
          </tr>
        </thead>
        <tbody>
          <?php
            $query = oci_parse($db_conn, "
              SELECT s.staff_id, s.staff_name, s.staff_type,
                     ft.monthly_salary, pt.hourly_rate
              FROM staff s
              LEFT JOIN full_time_staff ft ON s.staff_id = ft.staff_id
              LEFT JOIN part_time_staff pt ON s.staff_id = pt.staff_id
              WHERE s.manager_id = :mid
            ");
            oci_bind_by_name($query, ":mid", $currentManagerId);
            oci_execute($query);

            while ($staff = oci_fetch_assoc($query)) {
              echo "<tr>";
              echo "<td>{$staff['STAFF_NAME']}</td>";
              echo "<td>{$staff['STAFF_TYPE']}</td>";
              echo "<td>";
              if ($staff['STAFF_TYPE'] === 'FT') {
                $salary = $staff['MONTHLY_SALARY'] ?? 0;
                echo "<input type='number' step='0.01' name='salary[{$staff['STAFF_ID']}]' value='{$salary}'>";
              } else {
                echo "-";
              }
              echo "</td>";
              echo "<td>";
              if ($staff['STAFF_TYPE'] === 'PT') {
                $hourly = $staff['HOURLY_RATE'] ?? 0;
                echo "<input type='number' step='0.01' name='hourly[{$staff['STAFF_ID']}]' value='{$hourly}'>";
              } else {
                echo "-";
              }
              echo "</td>";
              echo "<td><button type='submit' name='update' value='{$staff['STAFF_ID']}'>Update</button></td>";
              echo "</tr>";
            }
          ?>
        </tbody>
      </table>
    </form>
  </div>

<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $staffId = $_POST['update'];
    $salary = $_POST['salary'][$staffId] ?? null;
    $hourly = $_POST['hourly'][$staffId] ?? null;

    // Determine staff type
    $typeQuery = oci_parse($db_conn, "SELECT staff_type FROM staff WHERE staff_id = :id");
    oci_bind_by_name($typeQuery, ":id", $staffId);
    oci_execute($typeQuery);
    $typeResult = oci_fetch_assoc($typeQuery);

    if ($typeResult) {
        $type = $typeResult['STAFF_TYPE'];
        
        if ($type === 'FT' && $salary !== null) {
            // Update full-time table
            $stmt = oci_parse($db_conn, "UPDATE full_time_staff SET monthly_salary = :sal WHERE staff_id = :id");
            oci_bind_by_name($stmt, ":sal", $salary);
            oci_bind_by_name($stmt, ":id", $staffId);
            oci_execute($stmt);
        }

        if ($type === 'PT' && $hourly !== null) {
            // Update part-time table
            $stmt = oci_parse($db_conn, "UPDATE part_time_staff SET hourly_rate = :hr WHERE staff_id = :id");
            oci_bind_by_name($stmt, ":hr", $hourly);
            oci_bind_by_name($stmt, ":id", $staffId);
            oci_execute($stmt);
        }

        echo "<script>alert('Staff details updated successfully.'); window.location.href='manager.php';</script>";
    }
}
?>
</body>
</html>
