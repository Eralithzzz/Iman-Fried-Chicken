<?php
require_once 'oracle.php';
session_start();

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['staff_username'] ?? '';
    $password = $_POST['staff_password'] ?? '';

    $sql = "SELECT * FROM Staff WHERE staff_username = :username AND staff_password = :password";
    $stmt = oci_parse($db_conn, $sql);

    oci_bind_by_name($stmt, ':username', $username);
    oci_bind_by_name($stmt, ':password', $password); 

    oci_execute($stmt);

    $row = oci_fetch_assoc($stmt);
    if ($row) {
        // Set session variables
        $_SESSION['staff_id'] = $row['STAFF_ID'];
        $_SESSION['staff_name'] = $row['STAFF_NAME'];
        
        echo "<script>alert('Login successfully!'); window.location.href='Staff Page.php';</script>";
       
        exit();
    } else {
        $error = "Invalid username or password.";
    }

    oci_free_statement($stmt);
    oci_close($db_conn);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>

    <title>Login</title>
 
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
      <form method="POST" action="Login.php">
        <h3>Login Here</h3>
        <label for="username">Username</label>
        <input type="text" name="staff_username" placeholder="Username" id="username" required>

        <label for="password">Password</label>
        <input type="password" name="staff_password" placeholder="Password" id="password" required>
        <br>

        <button type="submit">Login</button>

        <div id="signin-links">
            <span style="color: #fff;">New Staff? </span><a href="Register.php">Register</a>
        </div>
    </form>
</body>
</html>
