<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iman Fried Chicken</title>
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
        
        .out-of-stock {
            color: #999;
            font-style: italic;
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
        
        .checkout-btn:disabled {
            background-color: #95a5a6;
            cursor: not-allowed;
        }
        
        /* Payment Method Section */
        .payment-method {
            margin-top: 30px;
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            display: none;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        /* Loading spinner */
        .spinner {
            border: 4px solid rgba(0, 0, 0, 0.1);
            border-radius: 50%;
            border-top: 4px solid #3498db;
            width: 30px;
            height: 30px;
            animation: spin 1s linear infinite;
            margin: 20px auto;
        }
        
        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
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
            <h1>Iman Fried Chicken</h1>
        </div>
    </header>
    
    <main class="container">
        <div class="menu-section" id="menu-section">
            <!-- Menu categories will be loaded dynamically from database -->
            <div class="spinner"></div>
            <p>Loading menu...</p>
        </div>
        
        <div class="order-summary">
            <h2>Your Order</h2>
            <div class="order-items" id="order-items">
                <p>Your order is empty</p>
            </div>
            <div class="total-section">
                Total: RM<span id="order-total">0.00</span>
            </div>
            <button class="checkout-btn" id="checkout-btn" disabled>Proceed to Payment</button>
        </div>
        
        <div class="payment-method" id="payment-method">
            <h2>Payment Method</h2>
            <form id="payment-form">
                <div class="form-group">
                    <label for="payment">Select Payment Method</label>
                    <select id="payment" required>
                        <option value="">Select payment method</option>
                        <option value="CASH">Cash</option>
                        <option value="CARD">Credit/Debit Card</option>
                        <option value="ONLINE">Online Payment</option>
                    </select>
                </div>
                <button type="submit" class="checkout-btn">Place Order</button>
            </form>
        </div>
    </main>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const menuSection = document.getElementById('menu-section');
            const orderItems = document.getElementById('order-items');
            const orderTotal = document.getElementById('order-total');
            const checkoutBtn = document.getElementById('checkout-btn');
            const paymentMethod = document.getElementById('payment-method');
            const paymentForm = document.getElementById('payment-form');
            
            let cart = [];
            let total = 0;
            let menuData = {};
            
            // Fetch menu data from server
            fetch('api/menu.php') // Adjust the path to your API endpoint
                .then(response => response.json())
                .then(data => {
                    menuData = data;
                    renderMenu(data);
                })
                .catch(error => {
                    console.error('Error fetching menu:', error);
                    menuSection.innerHTML = '<p>Error loading menu. Please try again later.</p>';
                });
            
            // Render menu from database
            function renderMenu(menuData) {
                if (!menuData || menuData.length === 0) {
                    menuSection.innerHTML = '<p>No menu items available at this time.</p>';
                    return;
                }
                
                // Group menu items by category (you might want to add a category field to your Menu table)
                const categories = {
                    'Main Courses': menuData.filter(item => item.menu_id <= 100), // Example logic
                    'Side Courses': menuData.filter(item => item.menu_id > 100 && item.menu_id <= 200),
                    'Drinks': menuData.filter(item => item.menu_id > 200)
                };
                
                let menuHTML = '';
                
                for (const [categoryName, items] of Object.entries(categories)) {
                    if (items.length === 0) continue;
                    
                    menuHTML += `
                        <div class="category">
                            <h2>${categoryName}</h2>
                            ${items.map(item => `
                                <div class="menu-item">
                                    <div>
                                        <span class="item-name">${item.menu_name}</span>
                                        ${item.menu_availability === 'NO' ? 
                                            '<p class="out-of-stock">Currently unavailable</p>' : ''}
                                    </div>
                                    <div>
                                        <span class="item-price">RM ${item.menu_price.toFixed(2)}</span>
                                        <button class="add-btn" 
                                            data-id="${item.menu_id}" 
                                            data-name="${item.menu_name}" 
                                            data-price="${item.menu_price}"
                                            ${item.menu_availability === 'NO' ? 'disabled' : ''}>
                                            ${item.menu_availability === 'NO' ? 'Unavailable' : 'Add'}
                                        </button>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    `;
                }
                
                menuSection.innerHTML = menuHTML;
                
                // Add event listeners to all "Add" buttons
                document.querySelectorAll('.add-btn:not([disabled])').forEach(button => {
                    button.addEventListener('click', function() {
                        const id = parseInt(this.getAttribute('data-id'));
                        const name = this.getAttribute('data-name');
                        const price = parseFloat(this.getAttribute('data-price'));
                        
                        addToCart(id, name, price);
                    });
                });
            }
            
            // Add item to cart
            function addToCart(id, name, price) {
                // Check if item already exists in cart
                const existingItem = cart.find(item => item.id === id);
                
                if (existingItem) {
                    existingItem.quantity += 1;
                    existingItem.total = existingItem.quantity * price;
                } else {
                    cart.push({
                        id: id,
                        name: name,
                        price: price,
                        quantity: 1,
                        total: price
                    });
                }
                
                updateOrderSummary();
            }
            
            // Update order summary
            function updateOrderSummary() {
                if (cart.length === 0) {
                    orderItems.innerHTML = '<p>Your order is empty</p>';
                    orderTotal.textContent = '0.00';
                    total = 0;
                    checkoutBtn.disabled = true;
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
                                <span>RM ${item.price.toFixed(2)}</span>
                            </div>
                            <div class="quantity-controls">
                                <button class="quantity-btn minus" data-index="${index}">-</button>
                                <span>${item.quantity}</span>
                                <button class="quantity-btn plus" data-index="${index}">+</button>
                                <button class="remove-btn" data-index="${index}">Remove</button>
                            </div>
                            <div>RM ${item.total.toFixed(2)}</div>
                        </div>
                    `;
                });
                
                orderItems.innerHTML = itemsHTML;
                orderTotal.textContent = total.toFixed(2);
                checkoutBtn.disabled = false;
                
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
                
                paymentMethod.style.display = 'block';
                window.scrollTo(0, document.body.scrollHeight);
            });
            
            // Payment form submission - send order to server
            paymentForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const paymentMethod = document.getElementById('payment').value;
                
                const orderData = {
                    payment: {
                        method: paymentMethod,
                        amount: total,
                        status: paymentMethod === 'CASH' ? 'COMPLETED' : 'PENDING'
                    },
                    items: cart.map(item => ({
                        menu_id: item.id,
                        quantity: item.quantity,
                        price: item.price,
                        amount: item.total
                    }))
                };
                
                // Disable form while submitting
                const submitBtn = paymentForm.querySelector('button[type="submit"]');
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
                
                // Send order to server
                fetch('api/orders.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(orderData)
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    alert(`Order #${data.order_id} placed successfully!\nTotal: RM ${total.toFixed(2)}\nPayment Method: ${paymentMethod}\nThank you for your order!`);
                    
                    // Reset the cart
                    cart = [];
                    total = 0;
                    updateOrderSummary();
                    paymentForm.reset();
                    paymentMethod.style.display = 'none';
                
                })
                .finally(() => {
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Place Order';
                });
            });
        });
    </script>
</body>
</html>