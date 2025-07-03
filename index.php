<!-- enhanced index.php -->
<?php include('header.php'); ?>
<link rel="stylesheet" href="assets/style.css">


<main class="container py-4">
    <body style="background: url('images/bg.jpg') no-repeat center center fixed; background-size: cover;"></body>
  <div id="menu-section" class="row gy-4 mb-4 text-center">
    <div class="col-12">
      <div class="spinner-border text-primary" role="status"></div>
      <p>Loading menu...</p>
    </div>
  </div>

  <div class="row g-4">
    <!-- Order Summary -->
    <div class="col-md-6">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="card-title mb-3">Your Order</h4>
          <div id="order-items">
            <p>Your order is empty</p>
          </div>
          <hr />
          <div class="d-flex justify-content-between">
            <strong>Total:</strong>
            <strong>RM <span id="order-total">0.00</span></strong>
          </div>
          <button id="checkout-btn" class="btn btn-danger w-100 mt-3" disabled>Proceed to Payment</button>
        </div>
      </div>
    </div>

    <!-- Payment Method -->
    <div class="col-md-6">
      <div id="payment-method" class="card shadow-sm d-none">
        <div class="card-body">
          <h4 class="card-title mb-3">Payment Method</h4>
          <form id="payment-form">
            <div class="mb-3">
              <label for="payment" class="form-label">Select Payment Method</label>
              <select id="payment" class="form-select" required>
                <option value="">Select payment method</option>
                <option value="CASH">Cash</option>
                <option value="CARD">Credit/Debit Card</option>
                <option value="ONLINE">Online Payment</option>
              </select>
            </div>
            <button type="submit" class="btn btn-success w-100">Place Order</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</main>

<?php include('footer.php'); ?>
