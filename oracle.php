<?php
// db.php

class Oracle {
    private $host = "localhost:1521/FREEPDB1"; // Replace if needed
    private $username = "Eralithzzz";
    private $password = "Eralithzzz";
    private $conn;

    // Connect to Oracle DB
    public function connect() {
        $this->conn = oci_connect($this->username, $this->password, $this->host);

        if (!$this->conn) {
            $e = oci_error();
            throw new Exception("Connection failed: " . $e['message']);
        }

        return $this->conn;
    }

    // Close Oracle connection
    public function close() {
        if ($this->conn) {
            oci_close($this->conn);
        }
    }

    // Execute a query with optional parameters
    public function executeQuery($query, $params = []) {
        if (!$this->conn) {
            throw new Exception("No active database connection.");
        }

        $stmt = oci_parse($this->conn, $query);
        if (!$stmt) {
            $e = oci_error($this->conn);
            throw new Exception("SQL Error: " . $e['message']);
        }

        foreach ($params as $key => $value) {
            oci_bind_by_name($stmt, $key, $params[$key]);
        }

        if (!oci_execute($stmt)) {
            $e = oci_error($stmt);
            throw new Exception("Execution Error: " . $e['message']);
        }

        return $stmt;
    }

    // Fetch all rows from a SELECT query
    public function fetchAll($query, $params = []) {
        $stmt = $this->executeQuery($query, $params);
        $results = [];

        while ($row = oci_fetch_assoc($stmt)) {
            $results[] = $row;
        }

        return $results;
    }
}
?>
