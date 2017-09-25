<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);

#Validations
validateAuthKey($auth_key);

#Load custom data
$req_details['takeup_status'] = $req_details['request_status'];
if(isset($req_details['request_status']) && $req_details['request_status'] == 'Closed') $req_details['request_status_closed_date'] = CURRENT_DATE_TIME;
if(!isset($req_details['request_date'])) $req_details['request_date'] = CURRENT_DATE_TIME;

#Fetch requests
header("Content-Type:text/json");
$response = json_encode(requests($conn,$req_details));
echo replaceActiveStatusWithTrueFalse($response);