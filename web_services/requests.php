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
if(isset($req_details['request_id'])) $filters['request_id'] = $req_details['request_id'];
if(isset($req_details['request_status'])) $filters['request_status'] = $req_details['request_status'];
if(isset($req_details['receiver_id'])) $filters['receiver_id'] = $req_details['receiver_id'];

#Fetch requests
header("Content-Type:text/json");
$response = json_encode(selectRequests($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);