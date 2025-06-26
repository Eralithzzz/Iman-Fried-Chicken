<?php
// db.php

<<<<<<< HEAD
class db {
    private $host = "localhost:1521/FREEPDB1"; // Replace if needed
    private $username = "Eralithzzz";
    private $password = "Eralithzzz";
    private $conn;
=======
$host = "localhost:1521/FREEPDB1"; // Replace with your Oracle service name
$username = "Eralithzzz"; // Oracle username
$password = "Eralithzzz"; // Oracle password
>>>>>>> fdeaecd312ddcb3f4a61765bafff67bcfbea0e0b

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
