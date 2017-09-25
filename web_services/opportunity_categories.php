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
if(isset($req_details['opportunity_category_id'])) $filters['opportunity_category_id'] = $req_details['opportunity_category_id'];
if(isset($req_details['is_active'])) $filters['o.is_active'] = $req_details['is_active'];

#Fetch Opportunity categories
header("Content-Type:text/json");
$response = json_encode(selectOpportunityCategories($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);