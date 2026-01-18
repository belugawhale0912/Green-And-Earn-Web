<?php
session_start();
$_SESSION = array();
session_abort();
// todo follow lect session abort
header("Location: ../pages/login.php")
?>