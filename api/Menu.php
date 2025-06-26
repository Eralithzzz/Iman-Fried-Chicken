<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../oracle.php';  // ✅ uses your actual connection

try {
    // use $dbconn from oracle.php
    $sql = "SELECT menu_id, menu_name, menu_price, menu_availability, category FROM Menu ORDER BY menu_id";
    $stid = oci_parse($db_conn, $sql);

    if (!oci_execute($stid)) {
        $e = oci_error($stid);
        throw new Exception("Query execution failed: " . $e['message']);
    }

    $menu = [];
    while ($row = oci_fetch_assoc($stid)) {
        $menu[] = [
            'menu_id' => (int)$row['MENU_ID'],
            'menu_name' => $row['MENU_NAME'],
            'menu_price' => (float)$row['MENU_PRICE'],
            'menu_availability' => $row['MENU_AVAILABILITY'],
            'category' => $row['CATEGORY']
        ];
    }

    echo json_encode($menu);

    oci_free_statement($stid);
    oci_close($db_conn); // close connection
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
?>
