<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);

#Validations
validateAuthKey($auth_key);

#Validate sign in user
header("Content-Type:text/json");
$response =  json_encode(validateUserLogin($conn,$req_details));
echo replaceActiveStatusWithTrueFalse($response);
