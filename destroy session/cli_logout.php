<?php
session_start();

$_SESSION = [];

session_destroy();

header("Location: ../HTML_PHP/client_login.php");
exit();
?>