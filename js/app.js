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

      fetch('api/menu.php')
        .then(response => response.json())
        .then(data => {
          menuData = data;
          renderMenu(data);
        })
        .catch(error => {
          console.error('Error fetching menu:', error);
          menuSection.innerHTML = '<p>Error loading menu. Please try again later.</p>';
        });

      function renderMenu(menuData) {
        if (!menuData || menuData.length === 0) {
          menuSection.innerHTML = '<p>No menu items available at this time.</p>';
          return;
        }

        const categories = {
          'Main Courses': menuData.filter(item => item.menu_id <= 100),
          'Side Courses': menuData.filter(item => item.menu_id > 100 && item.menu_id <= 200),
          'Drinks': menuData.filter(item => item.menu_id > 200)
        };

        let html = '';
        menuSection.innerHTML = '';

        for (const [categoryName, items] of Object.entries(categories)) {
          if (items.length === 0) continue;

          const categoryDiv = document.createElement('div');
          categoryDiv.classList.add('col-12');
          categoryDiv.innerHTML = `<h3>${categoryName}</h3>`;
          menuSection.appendChild(categoryDiv);

          items.forEach(item => {
            const itemCard = document.createElement('div');
            itemCard.className = 'col-sm-6 col-md-4';
            itemCard.innerHTML = `
              <div class="card menu-card h-100">
              <img src="images/${item.menu_image || 'default.jpg'}" class="card-img-top" alt="${item.menu_name}" style="height: 180px; object-fit: cover;">
                <div class="card-body d-flex flex-column justify-content-between">
                  <h5 class="menu-item-name">${item.menu_name}</h5>
                  ${item.menu_availability === 'NO' ? '<p class="out-of-stock">Currently unavailable</p>' : ''}
                  <p class="menu-item-price">RM ${parseFloat(item.menu_price).toFixed(2)}</p>
                  <button class="btn btn-sm btn-success mt-auto add-btn" 
                    data-id="${item.menu_id}" 
                    data-name="${item.menu_name}" 
                    data-price="${item.menu_price}" 
                    ${item.menu_availability === 'NO' ? 'disabled' : ''}>
                    ${item.menu_availability === 'NO' ? 'Unavailable' : 'Add to Cart'}
                  </button>
                </div>
              </div>`;
            menuSection.appendChild(itemCard);
          });
        }

        document.querySelectorAll('.add-btn:not([disabled])').forEach(button => {
          button.addEventListener('click', function() {
            const id = parseInt(this.getAttribute('data-id'));
            const name = this.getAttribute('data-name');
            const price = parseFloat(this.getAttribute('data-price'));
            addToCart(id, name, price);
          });
        });
      }

      function addToCart(id, name, price) {
        const existingItem = cart.find(item => item.id === id);

        if (existingItem) {
          existingItem.quantity += 1;
          existingItem.total = existingItem.quantity * price;
        } else {
          cart.push({ id, name, price, quantity: 1, total: price });
        }

        updateOrderSummary();
      }

      function updateOrderSummary() {
        if (cart.length === 0) {
          orderItems.innerHTML = '<p>Your order is empty</p>';
          orderTotal.textContent = '0.00';
          total = 0;
          checkoutBtn.disabled = true;
          return;
        }

        let html = '';
        total = 0;

        cart.forEach((item, index) => {
          total += item.total;
          html += `
            <div class="border-bottom py-2">
              <div class="d-flex justify-content-between">
                <span>${item.name}</span>
                <span>RM ${item.price.toFixed(2)}</span>
              </div>
              <div class="d-flex align-items-center mt-2">
                <button class="btn btn-sm btn-primary quantity-btn me-1 minus" data-index="${index}">-</button>
                <span>${item.quantity}</span>
                <button class="btn btn-sm btn-primary quantity-btn ms-1 me-3 plus" data-index="${index}">+</button>
                <button class="btn btn-sm btn-outline-danger remove-btn" data-index="${index}">Remove</button>
                <span class="ms-auto">RM ${item.total.toFixed(2)}</span>
              </div>
            </div>`;
        });

        orderItems.innerHTML = html;
        orderTotal.textContent = total.toFixed(2);
        checkoutBtn.disabled = false;

        document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
          btn.addEventListener('click', function() {
            const i = parseInt(this.dataset.index);
            if (cart[i].quantity > 1) {
              cart[i].quantity -= 1;
              cart[i].total = cart[i].quantity * cart[i].price;
              updateOrderSummary();
            }
          });
        });

        document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
          btn.addEventListener('click', function() {
            const i = parseInt(this.dataset.index);
            cart[i].quantity += 1;
            cart[i].total = cart[i].quantity * cart[i].price;
            updateOrderSummary();
          });
        });

        document.querySelectorAll('.remove-btn').forEach(btn => {
          btn.addEventListener('click', function() {
            const i = parseInt(this.dataset.index);
            cart.splice(i, 1);
            updateOrderSummary();
          });
        });
      }

      checkoutBtn.addEventListener('click', function() {
        if (cart.length === 0) return;
        paymentMethod.classList.remove('d-none');
        window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });
      });

      paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const paymentType = document.getElementById('payment').value;

        const orderData = {
          payment: {
            method: paymentType,
            amount: total,
            status: paymentType === 'CASH' ? 'COMPLETED' : 'PENDING'
          },
          items: cart.map(item => ({
            menu_id: item.id,
            quantity: item.quantity,
            price: item.price,
            amount: item.total
          }))
        };

        const submitBtn = paymentForm.querySelector('button[type="submit"]');
        submitBtn.disabled = true;
        submitBtn.textContent = 'Processing...';

        fetch('api/orders.php', {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify(orderData)
        })
        .then(res => res.json())
        .then(data => {
          window.location.href = `receipt.php?order_id=${data.order_id}&pay_id=${data.pay_id}&method=${paymentType}&total=${total.toFixed(2)}`;
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.textContent = 'Place Order';
        });
      });
    });