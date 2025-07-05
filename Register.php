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
        background-color: #FFF5E1; /* Light creamy background */
        background-image: url('https://transparenttextures.com/patterns/food.png'); /* Subtle food pattern */
        font-family: 'Comic Sans MS', cursive, sans-serif;
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
        background: radial-gradient(
            #FFD700, /* Golden yellow */
            #DAA520  /* Golden rod */
        );
        left: -80px;
        top: -80px;
        opacity: 0.8;
    }
    .shape:last-child{
        background: radial-gradient(
            #8B0000, /* Dark red */
            #FF4500  /* Orange red */
        );
        right: -30px;
        bottom: -80px;
        opacity: 0.8;
    }
    form{
        height: auto;
        width: 800px;
        background-color: rgba(139, 0, 0, 0.8); /* Dark red background */
        position: absolute;
        transform: translate(-50%,-50%);
        top: 50%;
        left: 50%;
        border-radius: 15px;
        border: 5px dashed #FFD700; /* Golden dashed border */
        box-shadow: 0 0 30px rgba(139, 0, 0, 0.8);
        padding: 25px 35px;
    }
    form *{
        font-family: 'Comic Sans MS', cursive, sans-serif;
        color: #FFD700; /* Golden text */
        letter-spacing: 0.5px;
        outline: none;
        border: none;
        text-shadow: 1px 1px 2px #000;
    }
    form h3{
        font-size: 36px;
        font-weight: bold;
        line-height: 42px;
        text-align: center;
        margin-bottom: 25px;
        color: #FFFFFF;
        text-shadow: 2px 2px 4px #000;
        font-family: 'Impact', fantasy, sans-serif;
    }

    label{
        display: block;
        margin-top: 15px;
        font-size: 18px;
        font-weight: bold;
    }
    input{
        display: block;
        height: 50px;
        width: 100%;
        background-color: rgba(255, 215, 0, 0.2); /* Golden tint */
        border-radius: 8px;
        padding: 0 15px;
        margin-top: 8px;
        font-size: 16px;
        font-weight: 500;
        border: 2px solid #FFD700;
    }
    ::placeholder{
        color: #FFEBCD; /* Blanched almond */
        opacity: 0.7;
    }
    button{
        margin-top: 25px;
        width: 100%;
        background: linear-gradient(
            to bottom,
            #FFD700,
            #DAA520
        );
        color: #8B0000;
        padding: 15px 0;
        font-size: 20px;
        font-weight: bold;
        border-radius: 10px;
        cursor: pointer;
        border: 2px solid #FFFFFF;
        box-shadow: 0 4px 0 #8B0000;
        transition: all 0.2s;
    }
    button:hover{
        background: linear-gradient(
            to bottom,
            #FFDF00,
            #FFA500
        );
        transform: translateY(2px);
        box-shadow: 0 2px 0 #8B0000;
    }
    .form-row {
        display: flex;
        gap: 15px;
        margin-bottom: 10px;
    }
    .input-group {
        flex: 1;
    }
    #signin-links{
        margin-top: 20px;
        text-align: center;
        font-size: 16px;
    }
    #signin-links a {
        color: #FFFFFF;
        text-decoration: none;
        font-weight: bold;
        border-bottom: 2px dotted #FFD700;
    }
    #signin-links a:hover {
        color: #FFD700;
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
