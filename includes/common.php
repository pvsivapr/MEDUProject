<?php 
#Fetch headers
ob_start();session_start();
$headers_array = getallheaders();
$auth_key = isset($headers_array['Auth-Key'])?$headers_array['Auth-Key']:null;
$status_code= http_response_code();

#Set timezone
date_default_timezone_set('Asia/Calcutta');

#Include required files
include 'constants.php';
include 'db-connect.php';
include 'common_functions.php';  
include 'web_functions.php';
include 'mobile_functions.php';
?>