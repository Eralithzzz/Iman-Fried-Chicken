<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../oracle.php';

try {
    // Use the global connection from oracle.php
    global $db_conn;

    // Get posted JSON data
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['items'])) {
        throw new Exception("Invalid order data.");
    }

    // === 1. Insert into Orders ===
    $order_id = null;
    $insertOrder = oci_parse($db_conn, "
        INSERT INTO Orders (order_id, order_date, order_total) 
        VALUES (order_seq.NEXTVAL, SYSTIMESTAMP, :total)
        RETURNING order_id INTO :order_id
    ");
    oci_bind_by_name($insertOrder, ':total', $input['payment']['amount']);
    oci_bind_by_name($insertOrder, ':order_id', $order_id, 32);

    if (!oci_execute($insertOrder, OCI_NO_AUTO_COMMIT)) {
        throw new Exception(oci_error($insertOrder)['message']);
    }

    // === 2. Insert into OrderDetails ===
    foreach ($input['items'] as $item) {
        $stmt = oci_parse($db_conn, "
            INSERT INTO OrderDetails (
                orderdetails_id, order_id, menu_id, order_quantity, order_price, order_amount
            ) VALUES (
                orderdetails_seq.NEXTVAL, :order_id, :menu_id, :qty, :price, :amount
            )
        ");
        oci_bind_by_name($stmt, ':order_id', $order_id);
        oci_bind_by_name($stmt, ':menu_id', $item['menu_id']);
        oci_bind_by_name($stmt, ':qty', $item['quantity']);
        oci_bind_by_name($stmt, ':price', $item['price']);
        oci_bind_by_name($stmt, ':amount', $item['amount']);

        if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
            throw new Exception(oci_error($stmt)['message']);
        }

        oci_free_statement($stmt); // cleanup per iteration
    }

    // === 3. Insert into Payment ===
    $pay_id = null;
    $insertPay = oci_parse($db_conn, "
        INSERT INTO Payment (
            pay_id, order_id, pay_date, pay_status, pay_amount, pay_method
        ) VALUES (
            pay_seq.NEXTVAL, :order_id, SYSTIMESTAMP, :status, :amount, :method
        )
        RETURNING pay_id INTO :pay_id
    ");
    oci_bind_by_name($insertPay, ':order_id', $order_id);
    oci_bind_by_name($insertPay, ':status', $input['payment']['status']);
    oci_bind_by_name($insertPay, ':amount', $input['payment']['amount']);
    oci_bind_by_name($insertPay, ':method', $input['payment']['method']);
    oci_bind_by_name($insertPay, ':pay_id', $pay_id, 32);

    if (!oci_execute($insertPay, OCI_NO_AUTO_COMMIT)) {
        throw new Exception(oci_error($insertPay)['message']);
    }

    // === Commit All ===
    oci_commit($db_conn);

    // === Success Response ===
    echo json_encode([
        'success' => true,
        'order_id' => $order_id,
        'pay_id' => $pay_id
    ]);
    exit;

} catch (Exception $e) {
    if (isset($db_conn)) oci_rollback($db_conn);
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
    exit;
} finally {
    // Cleanup
    if (isset($insertOrder)) oci_free_statement($insertOrder);
    if (isset($insertPay)) oci_free_statement($insertPay);
}
