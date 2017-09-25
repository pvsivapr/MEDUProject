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
if(isset($req_details['quote_id'])) $filters['quote_id'] = $req_details['quote_id'];
if(isset($req_details['is_active'])) $filters['q.is_active'] = $req_details['is_active'];
if(isset($req_details['quote_date'])) $filters['DATE(quote_date)'] = $req_details['quote_date'];

#Fetch quotes
header("Content-Type:text/json");
$response = json_encode(selectQuotes($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);