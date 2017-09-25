<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);

#Validations
validateAuthKey($auth_key);

#Add/Update users
if(!isset($req_details['created_date'])) $req_details['created_date'] = CURRENT_DATE_TIME;
if(!isset($req_details['modified_date'])) $req_details['modified_date'] = CURRENT_DATE_TIME;
if(!isset($req_details['is_active'])) $req_details['is_active'] = 1;

header("Content-Type:text/json");
echo json_encode(users($conn,$req_details));