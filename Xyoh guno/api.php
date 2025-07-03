<?php
require_once 'oracle.php'; // Assumes $db_conn is your Oracle connection

header("Content-Type: application/json");

// Enable debugging during development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get the requested API endpoint
$request = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// API Routing
switch (true) {
    case preg_match('/\/api\/menu$/', $request) && $method == 'GET':
        getMenuItems();
        break;
    case preg_match('/\/api\/menu$/', $request) && $method == 'POST':
        createMenuItem();
        break;
    case preg_match('/\/api\/menu\/(\d+)$/', $request, $matches) && $method == 'PUT':
        updateMenuItem($matches[1]);
        break;
    case preg_match('/\/api\/menu\/(\d+)$/', $request, $matches) && $method == 'DELETE':
        deleteMenuItem($matches[1]);
        break;
    case preg_match('/\/api\/menu\/(\d+)\/availability$/', $request, $matches) && $method == 'PUT':
        toggleAvailability($matches[1]);
        break;
    default:
        http_response_code(404);
        echo json_encode(['error' => 'Endpoint not found']);
        break;
}

// Function to fetch all menu items
function getMenuItems() {
    global $db_conn;
    try {
        $query = "SELECT * FROM Menu ORDER BY category, menu_name";
        $stmt = oci_parse($db_conn, $query);
        oci_execute($stmt);
        $items = [];
        while ($row = oci_fetch_assoc($stmt)) {
            $items[] = $row;
        }
        echo json_encode($items);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Function to create a new menu item
function createMenuItem() {
    global $db_conn;
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $query = "INSERT INTO Menu (menu_name, menu_price, menu_availability, category) 
                  VALUES (:name, :price, :availability, :category)
                  RETURNING menu_id INTO :id";

        $stmt = oci_parse($db_conn, $query);

        oci_bind_by_name($stmt, ':name', $data['menu_name']);
        oci_bind_by_name($stmt, ':price', $data['menu_price']);
        oci_bind_by_name($stmt, ':availability', $data['menu_availability']);
        oci_bind_by_name($stmt, ':category', $data['category']);
        oci_bind_by_name($stmt, ':id', $menu_id, -1, SQLT_INT);

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            throw new Exception("Oracle error: " . $e['message']);
        }

        echo json_encode(['message' => 'Menu item created', 'menu_id' => $menu_id]);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Function to update a menu item
function updateMenuItem($id) {
    global $db_conn;
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $query = "UPDATE Menu SET 
                    menu_name = :name, 
                    menu_price = :price, 
                    menu_availability = :availability,
                    category = :category
                  WHERE menu_id = :id";

        $stmt = oci_parse($db_conn, $query);
        oci_bind_by_name($stmt, ':name', $data['menu_name']);
        oci_bind_by_name($stmt, ':price', $data['menu_price']);
        oci_bind_by_name($stmt, ':availability', $data['menu_availability']);
        oci_bind_by_name($stmt, ':category', $data['category']);
        oci_bind_by_name($stmt, ':id', $id);

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            throw new Exception("Oracle error: " . $e['message']);
        }

        echo json_encode(['message' => 'Menu item updated']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Function to delete a menu item
function deleteMenuItem($id) {
    global $db_conn;
    try {
        $query = "DELETE FROM Menu WHERE menu_id = :id";
        $stmt = oci_parse($db_conn, $query);
        oci_bind_by_name($stmt, ':id', $id);

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            throw new Exception("Oracle error: " . $e['message']);
        }

        echo json_encode(['message' => 'Menu item deleted']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}

// Function to toggle menu item availability
function toggleAvailability($id) {
    global $db_conn;
    try {
        $data = json_decode(file_get_contents('php://input'), true);

        $query = "UPDATE Menu SET menu_availability = :availability WHERE menu_id = :id";
        $stmt = oci_parse($db_conn, $query);
        oci_bind_by_name($stmt, ':availability', $data['availability']);
        oci_bind_by_name($stmt, ':id', $id);

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            throw new Exception("Oracle error: " . $e['message']);
        }

        echo json_encode(['message' => 'Availability updated']);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
}
?>
