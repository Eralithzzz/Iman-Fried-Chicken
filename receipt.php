<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Receipt</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Poppins', sans-serif;
      margin: 0;
      padding: 0;
    }

    body {
      background: linear-gradient(135deg, #f5f7fa, #c3cfe2);
      padding: 60px 20px;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
    }

    .receipt {
      background: #ffffff;
      padding: 40px;
      border-radius: 16px;
      max-width: 600px;
      width: 100%;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
      text-align: center;
      animation: fadeInUp 0.6s ease-in-out;
    }

    .receipt h1 {
      color: #2ecc71;
      font-size: 28px;
      margin-bottom: 20px;
      position: relative;
    }

    .checkmark {
      font-size: 60px;
      color: #2ecc71;
      margin-bottom: 20px;
    }

    .info {
      background-color: #f9f9f9;
      padding: 15px 20px;
      border-radius: 8px;
      margin: 12px 0;
      font-size: 16px;
      text-align: left;
    }

    .info strong {
      display: inline-block;
      width: 150px;
      color: #34495e;
    }

    .back-btn {
      margin-top: 30px;
      background: #e74c3c;
      color: white;
      padding: 12px 25px;
      border: none;
      border-radius: 8px;
      text-decoration: none;
      font-size: 16px;
      transition: background 0.3s;
    }

    .back-btn:hover {
      background: #c0392b;
    }

    @keyframes fadeInUp {
      0% {
        opacity: 0;
        transform: translateY(40px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }
  </style>
</head>
<body>
  <div class="receipt">
    <div class="checkmark">✔️</div>
    <h1>Thank You for Your Order!</h1>
    <div class="info"><strong>Order ID:</strong> <span id="order-id"></span></div>
    <div class="info"><strong>Payment ID:</strong> <span id="pay-id"></span></div>
    <div class="info"><strong>Total:</strong> RM <span id="total"></span></div>
    <div class="info"><strong>Payment Method:</strong> <span id="method"></span></div>
    
    <a href="index.php" class="back-btn">Back to Menu</a>
  </div>

  <script>
    const params = new URLSearchParams(window.location.search);
    document.getElementById('order-id').textContent = params.get('order_id');
    document.getElementById('pay-id').textContent = params.get('pay_id');
    document.getElementById('total').textContent = params.get('total');
    document.getElementById('method').textContent = params.get('method');
  </script>
</body>
</html>
