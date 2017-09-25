<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);
$req_details['created_date'] = CURRENT_DATE_TIME;
$req_details['modified_date'] = CURRENT_DATE_TIME;

#Validae auth_key
validateAuthKey($auth_key);

#Validate email_id
emailValidation($req_details['email_id']);

#Validate password
passwordValidation($req_details['password']);

#Register customer
header("Content-Type:text/json");
userRegistration($conn, $req_details);