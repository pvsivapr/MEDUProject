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
if(isset($req_details['incubation_center_category_id'])) $filters['incubation_center_category_id'] = $req_details['incubation_center_category_id'];
if(isset($req_details['is_active'])) $filters['i.is_active'] = $req_details['is_active'];

#Fetch Incubation center categories
header("Content-Type:text/json");
$response = json_encode(selectIncubationCenterCategories($conn,$filters));
echo replaceActiveStatusWithTrueFalse($response);