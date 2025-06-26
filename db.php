<?php
// db.php

class db {
    private $host = "localhost:1521/FREEPDB1"; // Replace if needed
    private $username = "Eralithzzz";
    private $password = "Eralithzzz";
    private $conn;

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
