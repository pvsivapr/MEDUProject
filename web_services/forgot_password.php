<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);

#Validations
validateAuthKey($auth_key);
emailValidation($req_details['email_id']);

#Fetch opportunities
header("Content-Type:text/json");
$response = forgotPassword($conn, $req_details['email_id']);
echo replaceActiveStatusWithTrueFalse($response);

