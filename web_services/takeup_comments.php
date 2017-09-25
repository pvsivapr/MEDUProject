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
if(isset($req_details['helper_request_id'])) $filters['helper_request_id'] = $req_details['helper_request_id'];

#Fetch stories
header("Content-Type:text/json");
$response = json_encode(selectTakeupComments($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);