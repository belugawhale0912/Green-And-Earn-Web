<?php
session_start();
$_SESSION = array();
session_abort();

header("Location: ../pages/login.php")
?>