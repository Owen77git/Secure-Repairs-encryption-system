<?php
session_start();


$_SESSION = [];


session_destroy();


header("Location: ../HTML_PHP/admin_login.php");
exit();
?>