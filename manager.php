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
  <meta charset="UTF-8">
  <title>Manager - Staff Management</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 20px; background: #f4f4f4; }
    table { width: 100%; border-collapse: collapse; background: white; }
    th, td { padding: 12px; border: 1px solid #ccc; text-align: center; }
    th { background: #2c3e50; color: white; }
    input[type="number"] { width: 100px; padding: 5px; }
    button { padding: 6px 12px; margin-top: 5px; }
    .container { background: white; padding: 20px; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h2 { color: #2c3e50; }
  </style>
</head>
<body>
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
              LEFT JOIN full_time ft ON s.staff_id = ft.staff_id
              LEFT JOIN part_time pt ON s.staff_id = pt.staff_id
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
