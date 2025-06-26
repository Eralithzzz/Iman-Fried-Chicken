<?php
// db.php

$host = "localhost:1521/FREEPDB1"; // Replace with your Oracle service name
$username = "Eralithzzz"; // Oracle username
$password = "Eralithzzz"; // Oracle password

    public function connect() {
        $this->conn = oci_connect($this->username, $this->password, $this->host);

        if (!$this->conn) {
            $e = oci_error();
            throw new Exception("Connection failed: " . $e['message']);
        }

        return $this->conn;
    }

    public function close() {
        if ($this->conn) {
            oci_close($this->conn);
        }
    }
}
?>
