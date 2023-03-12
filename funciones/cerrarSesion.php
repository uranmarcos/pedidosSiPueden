<?php 
session_start();

$_SESSION["login"] = false;
$_SESSION["admin"] = false;

header("Location: ../index.html");    
?>