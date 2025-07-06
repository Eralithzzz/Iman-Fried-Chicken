<?php
require_once 'oracle.php';
session_start();

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

// Get selected date from GET request
$selectedDate = $_GET['date'] ?? '';

// Base query with optional date filter
$query = "
    SELECT 
        TO_CHAR(o.order_date, 'DD/MM/YYYY') AS ORDER_DATE,
        od.ORDERDETAILS_ID,
        od.ORDER_ID,
        m.MENU_NAME,
        od.ORDER_QUANTITY,
        od.ORDER_PRICE,
        od.ORDER_AMOUNT
    FROM orderdetails od
    JOIN menu m ON od.MENU_ID = m.MENU_ID
    JOIN orders o ON od.ORDER_ID = o.ORDER_ID
";

if (!empty($selectedDate)) {
    // Format date for Oracle comparison (DD/MM/YYYY)
    $query .= " WHERE TRUNC(o.order_date) = TO_DATE(:selected_date, 'YYYY-MM-DD') ";
}

$query .= " ORDER BY o.order_date DESC, od.ORDERDETAILS_ID DESC ";

$statement = oci_parse($db_conn, $query);

if (!empty($selectedDate)) {
    oci_bind_by_name($statement, ":selected_date", $selectedDate);
}

oci_execute($statement);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Details</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: #FFF5E1 url('https://transparenttextures.com/patterns/food.png');
        }

        .container {
            margin-left: 250px;
            padding: 20px;
        }

        h2 {
            color: #8B0000;
            margin-bottom: 15px;
            font-family: 'Impact', fantasy, sans-serif;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.2);
        }

        h2 i {
            color: #DAA520;
        }

        form {
            margin-bottom: 20px;
            background: rgba(255, 215, 0, 0.2);
            padding: 15px;
            border-radius: 10px;
            border: 2px dashed #DAA520;
            display: inline-block;
        }

        input[type="date"] {
            padding: 8px 12px;
            border: 2px solid #DAA520;
            border-radius: 5px;
            font-weight: bold;
            background: rgba(255, 255, 255, 0.95);
        }

        button {
            padding: 8px 15px;
            border: none;
            background-color: #DAA520;
            color: white;
            font-weight: bold;
            border-radius: 5px;
            cursor: pointer;
            margin-left: 10px;
        }

        button:hover {
            background-color: #B8860B;
        }

        table {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.95);
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            margin-top: 20px;
            border: 2px solid #DAA520;
            border-radius: 10px;
            overflow: hidden;
        }

        th, td {
            padding: 12px;
            border-bottom: 1px solid #DAA520;
        }

        th {
            background-color: #8B0000;
            color: #FFD700;
            font-weight: 600;
        }

        td {
            color: #333;
            font-weight: bold;
        }

        tr:nth-child(even) {
            background-color: rgba(255, 215, 0, 0.1);
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 15px;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="container">
        <h2><i class="fas fa-receipt"></i> Order Details</h2>

        <form method="get">
            <label for="date">Choose Date:</label>
            <input type="date" name="date" id="date" value="<?= htmlspecialchars($selectedDate) ?>">
            <button type="submit"><i class="fas fa-search"></i> View</button>
        </form>

        <table>
            <tr>
                <th>Date</th>
                <th>OrderDetails ID</th>
                <th>Order ID</th>
                <th>Menu Name</th>
                <th>Quantity</th>
                <th>Price (RM)</th>
                <th>Amount (RM)</th>
            </tr>
            <?php
            $hasResults = false;
            while ($row = oci_fetch_assoc($statement)):
                $hasResults = true;
            ?>
                <tr>
                    <td><?= htmlspecialchars($row['ORDER_DATE']) ?></td>
                    <td><?= htmlspecialchars($row['ORDERDETAILS_ID']) ?></td>
                    <td><?= htmlspecialchars($row['ORDER_ID']) ?></td>
                    <td><?= htmlspecialchars($row['MENU_NAME']) ?></td>
                    <td><?= htmlspecialchars($row['ORDER_QUANTITY']) ?></td>
                    <td><?= number_format($row['ORDER_PRICE'], 2) ?></td>
                    <td><?= number_format($row['ORDER_AMOUNT'], 2) ?></td>
                </tr>
            <?php endwhile; ?>

            <?php if (!$hasResults): ?>
                <tr>
                    <td colspan="7" style="text-align: center; color: #8B0000;">No order details found for this date.</td>
                </tr>
            <?php endif; ?>
        </table>
    </div>
</body>
</html>
