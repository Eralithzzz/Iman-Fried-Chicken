<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restaurant Ordering System</title>
    <style>
        /* Global Styles */
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        h1, h2, h3 {
            color: #2c3e50;
        }
        
        /* Header Styles */
        header {
            background-color: #e74c3c;
            color: white;
            padding: 20px 0;
            text-align: center;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        /* Menu Section */
        .menu-section {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 30px;
        }
        
        .category {
            flex: 1;
            min-width: 300px;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .menu-item {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        
        .menu-item:last-child {
            border-bottom: none;
        }
        
        .item-name {
            font-weight: 600;
        }
        
        .item-price {
            color: #e74c3c;
            font-weight: bold;
        }
        
        .add-btn {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .add-btn:hover {
            background-color: #27ae60;
        }
        
        /* Order Summary */
        .order-summary {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .order-items {
            margin-bottom: 20px;
        }
        
        .order-item {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .quantity-controls {
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .quantity-btn {
            background-color: #3498db;
            color: white;
            border: none;
            width: 25px;
            height: 25px;
            border-radius: 50%;
            cursor: pointer;
        }
        
        .remove-btn {
            background-color: #e74c3c;
            color: white;
            border: none;
            padding: 2px 8px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 10px;
        }
        
        .total-section {
            font-size: 1.2em;
            font-weight: bold;
            text-align: right;
            padding-top: 10px;
            border-top: 2px solid #eee;
        }
        
        .checkout-btn {
            display: block;
            width: 100%;
            padding: 12px;
            background-color: #e74c3c;
            color: white;
            border: none;
            border-radius: 4px;
            font-size: 1.1em;
            cursor: pointer;
            margin-top: 20px;
            transition: background-color 0.3s;
        }
        
        .checkout-btn:hover {
            background-color: #c0392b;
        }
        
        /* Customer Info */
        .customer-info {
            margin-top: 30px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .menu-section {
                flex-direction: column;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Restaurant Ordering System</h1>
        </div>
    </header>
    
    <main class="container">
        <div class="menu-section">
            <div class="category">
                <h2>Appetizers</h2>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Bruschetta</span>
                        <p class="item-description">Toasted bread with tomatoes, garlic and basil</p>
                    </div>
                    <div>
                        <span class="item-price">$8.99</span>
                        <button class="add-btn" data-name="Bruschetta" data-price="8.99">Add</button>
                    </div>
                </div>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Calamari</span>
                        <p class="item-description">Fried squid with marinara sauce</p>
                    </div>
                    <div>
                        <span class="item-price">$12.99</span>
                        <button class="add-btn" data-name="Calamari" data-price="12.99">Add</button>
                    </div>
                </div>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Spinach Dip</span>
                        <p class="item-description">Creamy spinach with tortilla chips</p>
                    </div>
                    <div>
                        <span class="item-price">$9.99</span>
                        <button class="add-btn" data-name="Spinach Dip" data-price="9.99">Add</button>
                    </div>
                </div>
            </div>
            
            <div class="category">
                <h2>Main Courses</h2>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Spaghetti Bolognese</span>
                        <p class="item-description">Classic pasta with meat sauce</p>
                    </div>
                    <div>
                        <span class="item-price">$16.99</span>
                        <button class="add-btn" data-name="Spaghetti Bolognese" data-price="16.99">Add</button>
                    </div>
                </div>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Grilled Salmon</span>
                        <p class="item-description">Fresh salmon with lemon butter sauce</p>
                    </div>
                    <div>
                        <span class="item-price">$22.99</span>
                        <button class="add-btn" data-name="Grilled Salmon" data-price="22.99">Add</button>
                    </div>
                </div>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Chicken Parmesan</span>
                        <p class="item-description">Breaded chicken with marinara and cheese</p>
                    </div>
                    <div>
                        <span class="item-price">$18.99</span>
                        <button class="add-btn" data-name="Chicken Parmesan" data-price="18.99">Add</button>
                    </div>
                </div>
            </div>
            
            <div class="category">
                <h2>Desserts</h2>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Tiramisu</span>
                        <p class="item-description">Classic Italian dessert</p>
                    </div>
                    <div>
                        <span class="item-price">$7.99</span>
                        <button class="add-btn" data-name="Tiramisu" data-price="7.99">Add</button>
                    </div>
                </div>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Chocolate Lava Cake</span>
                        <p class="item-description">Warm chocolate cake with molten center</p>
                    </div>
                    <div>
                        <span class="item-price">$8.99</span>
                        <button class="add-btn" data-name="Chocolate Lava Cake" data-price="8.99">Add</button>
                    </div>
                </div>
                <div class="menu-item">
                    <div>
                        <span class="item-name">Cheesecake</span>
                        <p class="item-description">New York style with berry topping</p>
                    </div>
                    <div>
                        <span class="item-price">$7.99</span>
                        <button class="add-btn" data-name="Cheesecake" data-price="7.99">Add</button>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="order-summary">
            <h2>Your Order</h2>
            <div class="order-items" id="order-items">
                <!-- Order items will be added here dynamically -->
                <p>Your order is empty</p>
            </div>
            <div class="total-section">
                Total: $<span id="order-total">0.00</span>
            </div>
            <button class="checkout-btn" id="checkout-btn">Proceed to Checkout</button>
        </div>
        
        <div class="customer-info" id="customer-info" style="display: none;">
            <h2>Customer Information</h2>
            <form id="customer-form">
                <div class="form-group">
                    <label for="name">Full Name</label>
                    <input type="text" id="name" required>
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" required>
                </div>
                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="tel" id="phone" required>
                </div>
                <div class="form-group">
                    <label for="address">Delivery Address</label>
                    <textarea id="address" rows="3" required></textarea>
                </div>
                <div class="form-group">
                    <label for="payment">Payment Method</label>
                    <select id="payment" required>
                        <option value="">Select payment method</option>
                        <option value="cash">Cash on Delivery</option>
                        <option value="card">Credit Card</option>
                    </select>
                </div>
                <button type="submit" class="checkout-btn">Place Order</button>
            </form>
        </div>
    </main>
    
    <script>
        // Simple JavaScript functionality
        document.addEventListener('DOMContentLoaded', function() {
            const orderItems = document.getElementById('order-items');
            const orderTotal = document.getElementById('order-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            const customerInfo = document.getElementById('customer-info');
            const customerForm = document.getElementById('customer-form');
            
            let cart = [];
            let total = 0;
            
            // Add event listeners to all "Add" buttons
            document.querySelectorAll('.add-btn').forEach(button => {
                button.addEventListener('click', function() {
                    const name = this.getAttribute('data-name');
                    const price = parseFloat(this.getAttribute('data-price'));
                    
                    // Check if item already exists in cart
                    const existingItem = cart.find(item => item.name === name);
                    
                    if (existingItem) {
                        existingItem.quantity += 1;
                        existingItem.total = existingItem.quantity * price;
                    } else {
                        cart.push({
                            name: name,
                            price: price,
                            quantity: 1,
                            total: price
                        });
                    }
                    
                    updateOrderSummary();
                });
            });
            
            // Update order summary
            function updateOrderSummary() {
                if (cart.length === 0) {
                    orderItems.innerHTML = '<p>Your order is empty</p>';
                    orderTotal.textContent = '0.00';
                    total = 0;
                    return;
                }
                
                let itemsHTML = '';
                total = 0;
                
                cart.forEach((item, index) => {
                    total += item.total;
                    itemsHTML += `
                        <div class="order-item">
                            <div>
                                <span>${item.name}</span>
                                <span>$${item.price.toFixed(2)}</span>
                            </div>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus" data-index="${index}">-</button>
                                <span>${item.quantity}</span>
                                <button class="quantity-btn plus" data-index="${index}">+</button>
                                <button class="remove-btn" data-index="${index}">Remove</button>
                            </div>
                            <div>$${item.total.toFixed(2)}</div>
                        </div>
                    `;
                });
                
                orderItems.innerHTML = itemsHTML;
                orderTotal.textContent = total.toFixed(2);
                
                // Add event listeners to quantity buttons
                document.querySelectorAll('.quantity-btn.minus').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        if (cart[index].quantity > 1) {
                            cart[index].quantity -= 1;
                            cart[index].total = cart[index].quantity * cart[index].price;
                            updateOrderSummary();
                        }
                    });
                });
                
                document.querySelectorAll('.quantity-btn.plus').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        cart[index].quantity += 1;
                        cart[index].total = cart[index].quantity * cart[index].price;
                        updateOrderSummary();
                    });
                });
                
                document.querySelectorAll('.remove-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const index = parseInt(this.getAttribute('data-index'));
                        cart.splice(index, 1);
                        updateOrderSummary();
                    });
                });
            }
            
            // Checkout button
            checkoutBtn.addEventListener('click', function() {
                if (cart.length === 0) {
                    alert('Your cart is empty. Please add items before checkout.');
                    return;
                }
                
                customerInfo.style.display = 'block';
                window.scrollTo(0, document.body.scrollHeight);
            });
            
            // Form submission
            customerForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                // In a real application, you would send this data to a server
                alert(`Order placed successfully!\nTotal: $${total.toFixed(2)}\nThank you for your order!`);
                
                // Reset the form and cart
                cart = [];
                total = 0;
                updateOrderSummary();
                customerForm.reset();
                customerInfo.style.display = 'none';
            });
        });
    </script>
</body>
</html>