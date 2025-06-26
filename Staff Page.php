<?php 
require_once 'oracle.php';
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Menu Management - Iman Fried Chicken</title>
    <style>
        * {
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        
        body {
            margin: 0;
            padding: 20px;
            background-color: #f5f5f5;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        h1 {
            color: #e74c3c;
            text-align: center;
        }
        
        .menu-form {
            background: #f9f9f9;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
        }
        
        .form-group {
            margin-bottom: 15px;
        }
        
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 600;
        }
        
        input, select, textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        
        button {
            background-color: #2ecc71;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        
        button.delete {
            background-color: #e74c3c;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        
        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        
        th {
            background-color: #f2f2f2;
        }
        
        .availability-toggle {
            cursor: pointer;
            color: #2ecc71;
            font-weight: bold;
        }
        
        .availability-toggle.unavailable {
            color: #e74c3c;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>Menu Management</h1>
        <div class="menu-form">
            <h2>Add/Edit Menu Item</h2>
            <form id="menuForm">
                <input type="hidden" id="menuId">
                <div class="form-group">
                    <label for="menuName">Item Name</label>
                    <input type="text" id="menuName" required>
                </div>
                <div class="form-group">
                    <label for="menuPrice">Price (RM)</label>
                    <input type="number" id="menuPrice" min="0" step="0.01" required>
                </div>
                <div class="form-group">
                    <label for="menuCategory">Category</label>
                    <select id="menuCategory" required>
                        <option value="MAIN_COURSES">Main Courses</option>
                        <option value="SIDE_DISHES">Side Dishes</option>
                        <option value="DRINKS">Drinks</option>
                        <option value="DESSERTS">Desserts</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="menuAvailability">Availability</label>
                    <select id="menuAvailability" required>
                        <option value="YES">Available</option>
                        <option value="NO">Unavailable</option>
                    </select>
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
                fetch('/api/menu')
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
                            <td>${item.MENU_NAME}</td>
                            <td>RM ${parseFloat(item.MENU_PRICE).toFixed(2)}</td>
                            <td>${item.CATEGORY}</td>
                            <td>
                                <span class="availability-toggle ${item.MENU_AVAILABILITY === 'NO' ? 'unavailable' : ''}" data-id="${item.MENU_ID}">
                                    ${item.MENU_AVAILABILITY === 'YES' ? 'Available' : 'Unavailable'}
                                </span>
                            </td>
                            <td>
                                <button class="edit-btn" data-id="${item.MENU_ID}">Edit</button>
                                <button class="delete-btn" data-id="${item.MENU_ID}">Delete</button>
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
                fetch(`/api/menu`)
                    .then(res => res.json())
                    .then(data => {
                        const item = data.find(i => i.MENU_ID == id);
                        document.getElementById('menuId').value = item.MENU_ID;
                        document.getElementById('menuName').value = item.MENU_NAME;
                        document.getElementById('menuPrice').value = item.MENU_PRICE;
                        document.getElementById('menuCategory').value = item.CATEGORY;
                        document.getElementById('menuAvailability').value = item.MENU_AVAILABILITY;
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
                const data = {
                    menu_name: document.getElementById('menuName').value,
                    menu_price: parseFloat(document.getElementById('menuPrice').value),
                    category: document.getElementById('menuCategory').value,
                    menu_availability: document.getElementById('menuAvailability').value
                };

                const method = editMode ? 'PUT' : 'POST';
                const url = editMode ? `/api/menu/${editId}` : '/api/menu';

                fetch(url, {
                    method: method,
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                })
                    .then(res => res.json())
                    .then(result => {
                        form.reset();
                        cancelBtn.style.display = 'none';
                        saveBtn.textContent = 'Save Item';
                        editMode = false;
                        getMenuItems();
                    });
            };

            function deleteItem(id) {
                if (!confirm('Delete this menu item?')) return;
                fetch(`/api/menu/${id}`, { method: 'DELETE' })
                    .then(() => getMenuItems());
            }

            function toggleAvailability(id) {
                const item = Array.from(menuItems.children).find(row => row.querySelector('.availability-toggle').dataset.id == id);
                const currentStatus = item.querySelector('.availability-toggle').textContent === 'Available' ? 'YES' : 'NO';
                const newStatus = currentStatus === 'YES' ? 'NO' : 'YES';
                fetch(`/api/menu/${id}/availability`, {
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