<?php

#Include required files
include_once '../includes/constants.php';

#Database connection
$host = "localhost";
if (ENVIRONMENT == 'development') {
    $host = 'localhost';
    $db = "inspiring_wings";
    $user = 'root';
    $pass = '';
}
else{
    $host = 'localhost';
    $db = "inspiring_wings";
    $user = 'InsWings';
    $pass = 'InspiringWings#2017@DB';
}

$conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
?>