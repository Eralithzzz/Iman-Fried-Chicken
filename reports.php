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
            font-family: 'Comic Sans MS', cursive, sans-serif;
        }

        body {
            margin: 0;
            padding: 0;
            background: #FFF5E1 url('https://transparenttextures.com/patterns/food.png');
        }

        .container {
            margin-left: 250px; /* Assuming sidebar width */
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

        label {
            font-weight: bold;
            margin-right: 10px;
            color: #8B0000;
        }

        select {
            padding: 8px 12px;
            border: 2px solid #DAA520;
            border-radius: 5px;
            background: rgba(255, 255, 255, 0.9);
            font-weight: bold;
        }

        table {
            width: 100%;
            background-color: rgba(255, 255, 255, 0.9);
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

        .chart-container {
            margin-top: 30px;
            background: rgba(255, 255, 255, 0.9);
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            border: 2px solid #DAA520;
            max-width: 600px; /* Smaller chart container */
            margin-left: auto;
            margin-right: auto;
        }

        @media (max-width: 768px) {
            .container {
                margin-left: 0;
                padding: 15px;
            }
            
            .chart-container {
                max-width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'sidebar.php'; ?>

    <div class="container">
        <h2><i class="fas fa-chart-pie"></i>Sales Report</h2>

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
            <canvas id="salesChart" height="250"></canvas> <!-- Smaller chart height -->
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
                    backgroundColor: 'rgba(218, 165, 32, 0.7)', /* Golden color */
                    borderColor: 'rgba(139, 0, 0, 1)', /* Dark red border */
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    title: {
                        display: true,
                        text: 'Sales by <?= ucfirst($type) ?>',
                        font: { 
                            size: 16,
                            family: "'Impact', fantasy, sans-serif"
                        },
                        color: '#8B0000'
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Amount (RM)',
                            color: '#8B0000',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(139, 0, 0, 0.1)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: '<?= $label ?>',
                            color: '#8B0000',
                            font: {
                                weight: 'bold'
                            }
                        },
                        grid: {
                            color: 'rgba(139, 0, 0, 0.1)'
                        }
                    }
                }
            }
        });
    </script>
</body>
</html>