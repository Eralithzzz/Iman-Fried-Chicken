<?php
header('Content-Type: application/json');
header("Access-Control-Allow-Origin: *");

require_once __DIR__ . '/../oracle.php';

try {
    $oracle = new Oracle();
    $conn = $oracle->connect();

    // Get posted JSON data
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || empty($input['items'])) {
        throw new Exception("Invalid order data.");
    }


   

    // 1. Insert into Orders
    $order_id = rand(1000, 9999); // replace with a sequence if you have one
    $insertOrder = oci_parse($conn, "INSERT INTO Orders (order_id, order_date, order_total) VALUES (:id, SYSTIMESTAMP, :total)");
    oci_bind_by_name($insertOrder, ':id', $order_id);
    oci_bind_by_name($insertOrder, ':total', $input['payment']['amount']);
    if (!oci_execute($insertOrder, OCI_NO_AUTO_COMMIT)) {
        throw new Exception(oci_error($insertOrder)['message']);
    }

    // 2. Insert into OrderDetails
    foreach ($input['items'] as $item) {
        $orderdetails_id = rand(10000, 99999); // replace with sequence if needed
        $stmt = oci_parse($conn, "INSERT INTO OrderDetails (orderdetails_id, order_id, menu_id, order_quantity, order_price, order_amount) 
                                  VALUES (:od_id, :order_id, :menu_id, :qty, :price, :amount)");
        oci_bind_by_name($stmt, ':od_id', $orderdetails_id);
        oci_bind_by_name($stmt, ':order_id', $order_id);
        oci_bind_by_name($stmt, ':menu_id', $item['menu_id']);
        oci_bind_by_name($stmt, ':qty', $item['quantity']);
        oci_bind_by_name($stmt, ':price', $item['price']);
        oci_bind_by_name($stmt, ':amount', $item['amount']);

        if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
            throw new Exception(oci_error($stmt)['message']);
        }
    }

    // 3. Insert into Payment
    $pay_id = rand(1000, 9999); // use sequence in real systems
    $insertPay = oci_parse($conn, "INSERT INTO Payment (pay_id, order_id, pay_date, pay_status, pay_amount, pay_method)
                                   VALUES (:pay_id, :order_id, SYSTIMESTAMP, :status, :amount, :method)");
    oci_bind_by_name($insertPay, ':pay_id', $pay_id);
    oci_bind_by_name($insertPay, ':order_id', $order_id);
    oci_bind_by_name($insertPay, ':status', $input['payment']['status']);
    oci_bind_by_name($insertPay, ':amount', $input['payment']['amount']);
    oci_bind_by_name($insertPay, ':method', $input['payment']['method']);

    if (!oci_execute($insertPay, OCI_NO_AUTO_COMMIT)) {
        throw new Exception(oci_error($insertPay)['message']);
    }

    // Commit the transaction
    oci_commit($conn);

    echo json_encode(['success' => true, 'order_id' => $order_id]);

    // Clean up
    oci_free_statement($insertOrder);
    oci_free_statement($insertPay);
    oci_free_statement($stmt);
    $oracle->close();

} catch (Exception $e) {
    if (isset($conn)) oci_rollback($conn);
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
}
