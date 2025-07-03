<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Order Receipt</title>
  <style>
    body { font-family: Arial, sans-serif; padding: 40px; background-color: #f5f5f5; }
    .receipt { background: white; padding: 30px; border-radius: 10px; max-width: 600px; margin: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
    h1 { color: #e74c3c; }
    .info { margin: 15px 0; }
    .back-btn { margin-top: 30px; background: #2ecc71; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; text-decoration: none; }
  </style>
</head>
<body>
  <div class="receipt">
    <h1>Thank You for Your Order!</h1>
    <div class="info"><strong>Order ID:</strong> <span id="order-id"></span></div>
    <div class="info"><strong>Payment ID:</strong> <span id="pay-id"></span></div>
    <div class="info"><strong>Total:</strong> RM <span id="total"></span></div>
    <div class="info"><strong>Payment Method:</strong> <span id="method"></span></div>
    
    <a href="Customer Dashboard.php" class="back-btn">Back to Menu</a>
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
