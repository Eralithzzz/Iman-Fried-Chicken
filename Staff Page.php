<?php 
require_once 'oracle.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Staff Menu Management - Iman Fried Chicken</title>
  <style>
    * {
      box-sizing: border-box;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }
    body {
      margin: 0;
      padding: 0;
      background: linear-gradient(to right, #f8f9fa, #e0f7fa);
    }
    .container {
      max-width: 1200px;
      margin: 40px auto;
      background: white;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0,0,0,0.15);
      animation: fadeIn 1s ease-in;
    }
    @keyframes fadeIn {
      from {opacity: 0; transform: translateY(20px);}
      to {opacity: 1; transform: translateY(0);}
    }
    h1 {
      color: #e74c3c;
      text-align: center;
      font-size: 2.5rem;
      margin-bottom: 30px;
    }
    .menu-form {
      background: #fefefe;
      padding: 25px;
      border: 2px solid #e0e0e0;
      border-radius: 12px;
      margin-bottom: 30px;
      box-shadow: inset 0 0 10px rgba(0,0,0,0.02);
    }
    .form-group {
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-bottom: 8px;
      font-weight: 600;
      color: #333;
    }
    input, select {
      width: 100%;
      padding: 12px;
      border: 1px solid #ccc;
      border-radius: 8px;
      font-size: 15px;
      transition: border 0.3s;
    }
    input:focus, select:focus {
      border-color: #3498db;
      outline: none;
    }
    button {
      background-color: #2ecc71;
      color: white;
      border: none;
      padding: 12px 18px;
      border-radius: 8px;
      cursor: pointer;
      font-size: 16px;
      transition: background 0.3s;
    }
    button:hover {
      background-color: #27ae60;
    }
    button.delete {
      background-color: #e74c3c;
    }
    button.delete:hover {
      background-color: #c0392b;
    }
    #menuTable {
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    table {
      width: 100%;
      border-collapse: collapse;
    }
    th, td {
      padding: 15px;
      text-align: left;
      border-bottom: 1px solid #eee;
    }
    th {
      background-color: #fafafa;
      color: #555;
    }
    .availability-toggle {
      cursor: pointer;
      font-weight: bold;
    }
    .availability-toggle.unavailable {
      color: #e74c3c;
    }
    .availability-toggle.available {
      color: #2ecc71;
    }
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
    img {
      border-radius: 10px;
      transition: transform 0.3s;
    }
    img:hover {
      transform: scale(1.1);
    }
    .edit-btn, .delete-btn {
      padding: 6px 12px;
      margin: 2px;
      border-radius: 6px;
      font-size: 14px;
    }
  </style>
</head>
<body>
  <?php include 'sidebar.php'; ?>
  <div class="main-content"></div>
  <div class="container">
    <h1><i class="fas fa-utensils"></i> Menu Management</h1>
    <div class="menu-form">
      <h2>Add/Edit Menu Item</h2>
      <form id="menuForm" enctype="multipart/form-data">
        <input type="hidden" id="menuId">
        <div class="form-group">
          <label for="menuName">Item Name</label>
          <input type="text" id="menuName" name="menu_name" required>
        </div>
        <div class="form-group">
          <label for="menuPrice">Price (RM)</label>
          <input type="number" id="menuPrice" name="menu_price" min="0" step="0.01" required>
        </div>
        <div class="form-group">
          <label for="menuCategory">Category</label>
          <select id="menuCategory" name="category" required>
            <option value="MAIN COURSES">Main Courses</option>
            <option value="SIDE DISHES">Side Dishes</option>
            <option value="DRINKS">Drinks</option>
          </select>
        </div>
        <div class="form-group">
          <label for="menuAvailability">Availability</label>
          <select id="menuAvailability" name="menu_availability" required>
            <option value="YES">Available</option>
            <option value="NO">Unavailable</option>
          </select>
        </div>
        <div class="form-group">
          <label for="menuImage">Upload Image</label>
          <input type="file" id="menuImage" name="menu_image" accept="image/*">
        </div>
        <button type="submit" id="saveBtn">Save Item</button>
        <button type="button" id="cancelBtn" style="display:none;">Cancel</button>
      </form>
    </div>

    <div id="loading">
      <div class="spinner"></div>
      <p>Loading menu items...</p>
    </div>

    <table id="menuTable" style="display:none;">
      <thead>
        <tr>
          <th>Image</th>
          <th>Name</th>
          <th>Price</th>
          <th>Category</th>
          <th>Availability</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody id="menuItems"></tbody>
    </table>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
      const form = document.getElementById('menuForm');
      const saveBtn = document.getElementById('saveBtn');
      const cancelBtn = document.getElementById('cancelBtn');
      const menuItems = document.getElementById('menuItems');
      const loading = document.getElementById('loading');
      const menuTable = document.getElementById('menuTable');
      let editMode = false;
      let editId = null;

      function getMenuItems() {
        fetch('api/Menu.php')
          .then(res => res.json())
          .then(data => {
            renderTable(data);
            loading.style.display = 'none';
            menuTable.style.display = 'table';
          });
      }

      function renderTable(items) {
        menuItems.innerHTML = '';
        items.forEach(item => {
          menuItems.innerHTML += `
            <tr>
              <td><img src="images/${item.menu_image}" alt="${item.menu_name}" style="width:60px; height:auto; border-radius:5px;" /></td>
              <td>${item.menu_name}</td>
              <td>RM ${parseFloat(item.menu_price).toFixed(2)}</td>
              <td>${item.category}</td>
              <td>
                <span class="availability-toggle ${item.menu_availability === 'NO' ? 'unavailable' : 'available'}" data-id="${item.menu_id}">
                  ${item.menu_availability === 'YES' ? 'Available' : 'Unavailable'}
                </span>
              </td>
              <td>
                <button class="edit-btn" data-id="${item.menu_id}">Edit</button>
                <button class="delete-btn" data-id="${item.menu_id}">Delete</button>
              </td>
            </tr>
          `;
        });

        document.querySelectorAll('.edit-btn').forEach(btn => {
          btn.onclick = () => editItem(btn.dataset.id);
        });

        document.querySelectorAll('.delete-btn').forEach(btn => {
          btn.onclick = () => deleteItem(btn.dataset.id);
        });

        document.querySelectorAll('.availability-toggle').forEach(span => {
          span.onclick = () => toggleAvailability(span.dataset.id);
        });
      }

      function editItem(id) {
        fetch('api/Menu.php')
          .then(res => res.json())
          .then(data => {
            const item = data.find(i => i.menu_id == id);
            document.getElementById('menuId').value = item.menu_id;
            document.getElementById('menuName').value = item.menu_name;
            document.getElementById('menuPrice').value = item.menu_price;
            document.getElementById('menuCategory').value = item.category;
            document.getElementById('menuAvailability').value = item.menu_availability;
            saveBtn.textContent = "Update Item";
            cancelBtn.style.display = 'inline-block';
            editMode = true;
            editId = id;
          });
      }

      cancelBtn.onclick = () => {
        form.reset();
        editMode = false;
        editId = null;
        saveBtn.textContent = "Save Item";
        cancelBtn.style.display = 'none';
      };

      form.onsubmit = (e) => {
        e.preventDefault();

        const formData = new FormData(form);

        const method = editMode ? 'POST' : 'POST';
        const url = editMode ? `api/Menu.php?id=${editId}&_method=PUT` : 'api/Menu.php';

        fetch(url, {
          method: method,
          body: formData
        })
        .then(res => {
          if (!res.ok) {
            return res.text().then(text => { throw new Error(text); });
          }
          return res.json();
        })
        .then(() => {
          form.reset();
          cancelBtn.style.display = 'none';
          saveBtn.textContent = 'Save Item';
          editMode = false;
          getMenuItems();
        })
        .catch(err => {
          console.error("Server error:\n", err.message);
          alert("Error: Check browser console (F12 > Console)");
        });
      };

      function deleteItem(id) {
        if (!confirm('Delete this menu item?')) return;
        fetch(`api/Menu.php?id=${id}`, { method: 'DELETE' })
          .then(() => getMenuItems());
      }

      function toggleAvailability(id) {
        const row = Array.from(menuItems.children).find(r => r.querySelector('.availability-toggle').dataset.id == id);
        const currentStatus = row.querySelector('.availability-toggle').textContent === 'Available' ? 'YES' : 'NO';
        const newStatus = currentStatus === 'YES' ? 'NO' : 'YES';
        fetch(`api/Menu.php?id=${id}`, {
          method: 'PUT',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ availability: newStatus })
        }).then(() => getMenuItems());
      }

      getMenuItems();
    });
  </script>
</body>
</html>
