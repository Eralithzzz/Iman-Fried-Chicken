<?php
// php & Oracle DB connection file
$user = "Eralithzzz"; //oracle username
$pass = "Eralithzzz"; //Oracle password
$host = "localhost:1521/FREEPDB1"; //server name or ip address
$dbconn = oci_connect($user, $pass, $host);
if (!$dbconn) {
 $e = oci_error();
 trigger_error(htmlentities($e['message'], ENT_QUOTES), E_USER_ERROR);
} else {
 echo "ORACLE DATABASE SUCCESSFULLY!!!<br>"; //you can remove this
}
?>