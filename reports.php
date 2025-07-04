<?php
require_once 'oracle.php';
session_start();

if (!isset($_SESSION['staff_id'])) {
    header("Location: login.php");
    exit();
}

$type = $_GET['type'] ?? 'daily'; // Default view
switch ($type) {
    case 'monthly':
        $group_by = "TO_CHAR(PAY_DATE, 'MM/YYYY')";
        $label = "Month";
        break;
    case 'yearly':
        $group_by = "TO_CHAR(PAY_DATE, 'YYYY')";
        $label = "Year";
        break;
    default:
        $group_by = "TO_CHAR(PAY_DATE, 'DD/MM/YYYY')";
        $label = "Date";
        break;
}

$query = "
    SELECT $group_by AS PERIOD, SUM(PAY_AMOUNT) AS TOTAL_SALES
    FROM payment
    WHERE PAY_STATUS = 'COMPLETED'
    GROUP BY $group_by
    ORDER BY MIN(PAY_DATE)
";

$statement = oci_parse($db_conn, $query);
oci_execute($statement);

// Store data in arrays for graph
$periods = [];
$totals = [];
while ($row = oci_fetch_assoc($statement)) {
    $periods[] = $row['PERIOD'];
    $totals[] = $row['TOTAL_SALES'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: #f1f5f9;
        }

        .container {
            margin-left: 250px; /* Assuming sidebar width */
            padding: 30px;
        }

        h2 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            background-color: #fff;
            border-collapse: collapse;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
            margin-top: 20px;
        }

        th, td {
            padding: 15px;
            border-bottom: 1px solid #eee;
        }

        th {
            background-color: #f0f0f0;
            text-align: left;
            color: #444;
            font-weight: 600;
        }

        td {
            color: #333;
        }

        .chart-container {
            margin-top: 40px;
            background: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.05);
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="container">
        <h2><i class="fas fa-chart-bar"></i> Sales Report</h2>

        <form method="get">
            <label for="type">View by:</label>
            <select name="type" id="type" onchange="this.form.submit()">
                <option value="daily" <?= $type === 'daily' ? 'selected' : '' ?>>Daily</option>
                <option value="monthly" <?= $type === 'monthly' ? 'selected' : '' ?>>Monthly</option>
                <option value="yearly" <?= $type === 'yearly' ? 'selected' : '' ?>>Yearly</option>
            </select>
        </form>

        <table>
            <tr>
                <th><?= htmlspecialchars($label) ?></th>
                <th>Total Sales (RM)</th>
            </tr>
            <?php foreach ($periods as $index => $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p) ?></td>
                    <td><?= number_format($totals[$index], 2) ?></td>
                </tr>
            <?php endforeach; ?>
        </table>

        <div class="chart-container">
            <canvas id="salesChart"></canvas>
        </div>
    </div>

    <script>
        const ctx = document.getElementById('salesChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: <?= json_encode($periods) ?>,
                datasets: [{
                    label: 'Total Sales (RM)',
                    data: <?= json_encode($totals) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Sales Overview by <?= ucfirst($type) ?>',
                        font: { size: 18 }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (RM)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '<?= $label ?>'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>
