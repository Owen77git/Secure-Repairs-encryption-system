<?php
session_start();

$_SESSION = [];

session_destroy();

header("Location: ../HTML_PHP/technician_login.php");
exit();
?>