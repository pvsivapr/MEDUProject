<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);

#Validations
validateAuthKey($auth_key);

#Load filters   
if(isset($req_details['user_id'])) $filters['user_id'] = $req_details['user_id'];
if(isset($req_details['is_active'])) $filters['is_active'] = $req_details['is_active'];

#Fetch roles
header("Content-Type:text/json");
$response =  json_encode(selectUsers($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);