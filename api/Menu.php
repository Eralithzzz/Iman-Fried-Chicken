<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

require_once __DIR__ . '/../oracle.php'; // Adjust if needed

$method = $_SERVER['REQUEST_METHOD'];
$realMethod = $_SERVER['REQUEST_METHOD'];

// Support _method override for PUT with file uploads
if ($method === 'POST' && isset($_GET['_method']) && $_GET['_method'] === 'PUT') {
    $realMethod = 'PUT';
}

$id = $_GET['id'] ?? null;

try {
    switch ($realMethod) {
        case 'GET':
            $sql = "SELECT menu_id, menu_name, menu_price, menu_availability, category, menu_image FROM Menu ORDER BY menu_id";
            $stid = oci_parse($db_conn, $sql);
            oci_execute($stid);

            $menu = [];
            while ($row = oci_fetch_assoc($stid)) {
                $menu[] = [
                    'menu_id' => (int)$row['MENU_ID'],
                    'menu_name' => $row['MENU_NAME'],
                    'menu_price' => (float)$row['MENU_PRICE'],
                    'menu_availability' => $row['MENU_AVAILABILITY'],
                    'category' => $row['CATEGORY'],
                    'menu_image' => $row['MENU_IMAGE'] ?: 'default.jpg'
                ];
            }

            echo json_encode($menu);
            oci_free_statement($stid);
            break;

        case 'POST':
            // Use $_POST and $_FILES
            $name = $_POST['menu_name'];
            $price = $_POST['menu_price'];
            $avail = $_POST['menu_availability'];
            $cat = $_POST['category'];
            $imageName = '';

            if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['menu_image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('menu_', true) . '.' . $ext;
                move_uploaded_file($_FILES['menu_image']['tmp_name'], __DIR__ . '/../images/' . $imageName);
            }

            $sql = "INSERT INTO Menu (menu_id, menu_name, menu_price, menu_availability, category, menu_image) 
                    VALUES (menu_seq.NEXTVAL, :name, :price, :avail, :cat, :img)";
            $stid = oci_parse($db_conn, $sql);
            oci_bind_by_name($stid, ':name', $name);
            oci_bind_by_name($stid, ':price', $price);
            oci_bind_by_name($stid, ':avail', $avail);
            oci_bind_by_name($stid, ':cat', $cat);
            oci_bind_by_name($stid, ':img', $imageName);
            oci_execute($stid);
            echo json_encode(['message' => 'Menu item added']);
            break;

        case 'PUT':
            if (!$id) throw new Exception("ID is required for update.");

            // Toggle availability (from JS toggleAvailability)
            $rawData = json_decode(file_get_contents("php://input"), true);
            if (isset($rawData['availability'])) {
                $sql = "UPDATE Menu SET menu_availability = :avail WHERE menu_id = :id";
                $stid = oci_parse($db_conn, $sql);
                oci_bind_by_name($stid, ':avail', $rawData['availability']);
                oci_bind_by_name($stid, ':id', $id);
                oci_execute($stid);
                echo json_encode(['message' => 'Availability updated']);
                break;
            }

            // Full form update (from FormData override PUT)
            $name = $_POST['menu_name'];
            $price = $_POST['menu_price'];
            $avail = $_POST['menu_availability'];
            $cat = $_POST['category'];
            $imageName = $_POST['existing_image'] ?? ''; // Optional hidden field

            if (isset($_FILES['menu_image']) && $_FILES['menu_image']['error'] === UPLOAD_ERR_OK) {
                $ext = pathinfo($_FILES['menu_image']['name'], PATHINFO_EXTENSION);
                $imageName = uniqid('menu_', true) . '.' . $ext;
                move_uploaded_file($_FILES['menu_image']['tmp_name'], __DIR__ . '/../images/' . $imageName);
            }

            $sql = "UPDATE Menu SET 
                        menu_name = :name,
                        menu_price = :price,
                        menu_availability = :avail,
                        category = :cat,
                        menu_image = :img
                    WHERE menu_id = :id";
            $stid = oci_parse($db_conn, $sql);
            oci_bind_by_name($stid, ':name', $name);
            oci_bind_by_name($stid, ':price', $price);
            oci_bind_by_name($stid, ':avail', $avail);
            oci_bind_by_name($stid, ':cat', $cat);
            oci_bind_by_name($stid, ':img', $imageName);
            oci_bind_by_name($stid, ':id', $id);
            oci_execute($stid);
            echo json_encode(['message' => 'Menu item updated']);
            break;

        case 'DELETE':
            if (!$id) throw new Exception("ID is required for deletion.");

            $sql = "DELETE FROM Menu WHERE menu_id = :id";
            $stid = oci_parse($db_conn, $sql);
            oci_bind_by_name($stid, ':id', $id);
            oci_execute($stid);
            echo json_encode(['message' => 'Menu item deleted']);
            break;

        case 'OPTIONS':
            http_response_code(204);
            break;

        default:
            http_response_code(405);
            echo json_encode(['error' => 'Method not allowed']);
            break;
    }

    oci_close($db_conn);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
