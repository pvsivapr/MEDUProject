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
if(isset($req_details['event_id'])) $filters['event_id'] = $req_details['event_id'];
if(isset($req_details['is_active'])) $filters['e.is_active'] = $req_details['is_active'];

#Fetch events
header("Content-Type:text/json");
$response = json_encode(selectEvents($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);