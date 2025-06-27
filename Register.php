<?php 
require_once 'oracle.php';
session_start();

$successMessage = "";
$errorMessage = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get POST values safely
    $staff_name     = $_POST['staff_name'] ?? '';
    $staff_age      = $_POST['staff_age'] ?? '';
    $staff_phone    = $_POST['staff_phone'] ?? '';
    $staff_gender   = $_POST['staff_gender'] ?? '';
    $staff_username = $_POST['staff_username'] ?? '';
    $staff_password = $_POST['staff_password'] ?? '';
    $staff_type = $_POST['staff_type'] ?? '';
    $manager_id     = !empty($_POST['manager_id']) ? $_POST['manager_id'] : null;

    if (!$staff_name || !$staff_age || !$staff_username || !$staff_password || !$staff_type) {
        $errorMessage = "Please fill in all required fields.";
    } else {
        $sql = "INSERT INTO Staff (
                    STAFF_ID, STAFF_NAME, STAFF_AGE, STAFF_PHONE, STAFF_GENDER,
                    STAFF_USERNAME, STAFF_PASSWORD, MANAGER_ID, STAFF_TYPE
                ) VALUES (
                    staff_seq.NEXTVAL, :name, :age, :phone, :gender,
                    :username, :password, :manager, :staff_type
                )";

        $stid = oci_parse($db_conn, $sql);

        // Bind values
        oci_bind_by_name($stid, ':name', $staff_name);
        oci_bind_by_name($stid, ':age', $staff_age);
        oci_bind_by_name($stid, ':phone', $staff_phone);
        oci_bind_by_name($stid, ':gender', $staff_gender);
        oci_bind_by_name($stid, ':username', $staff_username);
        oci_bind_by_name($stid, ':password', $staff_password); // You can hash here if needed
        oci_bind_by_name($stid, ':manager', $manager_id);
        oci_bind_by_name($stid, ':staff_type', $staff_type);

         if (oci_execute($stid)) {
            // Get the inserted staff_id
            $queryID = "SELECT staff_seq.CURRVAL AS ID FROM dual";
            $idStmt = oci_parse($db_conn, $queryID);
            oci_execute($idStmt);
            $row = oci_fetch_assoc($idStmt);
            $new_staff_id = $row['ID'];

            // Insert into subtype table
            if ($staff_type === 'FT') {
                $insertSub = oci_parse($db_conn, "INSERT INTO FULL_TIME_STAFF (STAFF_ID) VALUES (:id)");
            } else {
                $insertSub = oci_parse($db_conn, "INSERT INTO PART_TIME_STAFF (STAFF_ID) VALUES (:id)");
            }
            oci_bind_by_name($insertSub, ':id', $new_staff_id);
            oci_execute($insertSub);

            oci_free_statement($idStmt);
            oci_free_statement($insertSub);

            echo "<script>alert('Staff registered successfully!'); window.location.href='Login.php';</script>";
        } else {
            $e = oci_error($stid);
            $errorMessage = "Registration failed: " . $e['message'];
        }

        oci_free_statement($stid);
        oci_close($db_conn);
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <title>Register</title>
 
    <link rel="preconnect" href="https://fonts.gstatic.com">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;600&display=swap" rel="stylesheet">
    <!--Stylesheet-->
    <style media="screen">
      *,
*:before,
*:after{
    padding: 0;
    margin: 0;
    box-sizing: border-box;
}
body{
    background-color: #080710;
}
.background{
    width: 430px;
    height: 520px;
    position: absolute;
    transform: translate(-50%,-50%);
    left: 50%;
    top: 50%;
}
.background .shape{
    height: 200px;
    width: 200px;
    position: absolute;
    border-radius: 50%;
}
.shape:first-child{
    background: linear-gradient(
        #1845ad,
        #23a2f6
    );
    left: -80px;
    top: -80px;
}
.shape:last-child{
    background: linear-gradient(
        to right,
        #ff512f,
        #f09819
    );
    right: -30px;
    bottom: -80px;
}
form{
    height: auto; /* Changed from fixed height */
    width: 800px;
    background-color: rgba(255,255,255,0.13);
    position: absolute;
    transform: translate(-50%,-50%);
    top: 50%;
    left: 50%;
    border-radius: 10px;
    backdrop-filter: blur(10px);
    border: 2px solid rgba(255,255,255,0.1);
    box-shadow: 0 0 40px rgba(8,7,16,0.6);
    padding: 30px 35px; /* Reduced padding */
}
form *{
    font-family: 'Poppins',sans-serif;
    color: #ffffff;
    letter-spacing: 0.5px;
    outline: none;
    border: none;
}
form h3{
    font-size: 32px;
    font-weight: 500;
    line-height: 42px;
    text-align: center;
    margin-bottom: 20px; /* Added margin below title */
}

label{
    display: block;
    margin-top: 15px; /* Reduced from 30px */
    font-size: 16px;
    font-weight: 500;
}
input{
    display: block;
    height: 45px; /* Slightly reduced height */
    width: 100%; /* Changed from 150% */
    background-color: rgba(255,255,255,0.07);
    border-radius: 3px;
    padding: 0 10px;
    margin-top: 5px; /* Reduced from 8px */
    font-size: 14px;
    font-weight: 300;
}
::placeholder{
    color: #e5e5e5;
}
button{
    margin-top: 20px; /* Adjusted margin */
    width: 100%;
    background-color: #ffffff;
    color: #080710;
    padding: 12px 0; /* Slightly reduced padding */
    font-size: 18px;
    font-weight: 600;
    border-radius: 5px;
    cursor: pointer;
}
.form-row {
    display: flex;
    gap: 15px; /* Reduced from 20px */
    margin-bottom: 5px; /* Added small bottom margin */
}
.input-group {
    flex: 1;
}
#signin-links{
    margin-top: 15px;
    text-align: center;
}
    </style>
</head>
<body>
    <div class="background">
        <div class="shape"></div>
        <div class="shape"></div>
    </div>
    <form method="post">
        <h3>Staff Register</h3>

        <div class="form-row">
            <div class="input-group">
                <label for="name">Name</label>
                <input type="text" name="staff_name" id="name" placeholder="Full Name" required>
            </div>
            <div class="input-group">
                <label for="age">Age</label>
                <input type="number" name="staff_age" id="age" placeholder="Age" required>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="phone">Phone</label>
                <input type="text" name="staff_phone" id="phone" placeholder="Phone Number">
            </div>
            <div class="input-group">
                <label for="gender">Gender</label>
                <input type="text" name="staff_gender" id="gender" placeholder="Male / Female">
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="username">Username</label>
                <input type="text" name="staff_username" id="username" placeholder="Enter username" required>
            </div>
            <div class="input-group">
                <label for="password">Password</label>
                <input type="password" name="staff_password" id="password" placeholder="Enter password" required>
            </div>
        </div>

        <div class="form-row">
            <div class="input-group">
                <label for="manager">Manager ID (optional)</label>
                <input type="number" name="manager_id" id="manager" placeholder="Manager ID">
            </div>
            <div class="input-group">
                <label for="staff_type">Staff Type</label>
                <select name="staff_type" id="staff_type" required>
                    <option value="">-- Select Type --</option>
                    <option value="FT">FT - Full Time Staff</option>
                    <option value="PT">PT - Part Time Staff</option>
                </select>
            </div>
        </div>
    
        <button type="submit">Register</button>
        <div id="signin-links">
            Already have an account? <a href="Login.php">Login</a>
        </div>
    </form>
</body>
</html>
