<?php
#Include required fies here
include('../includes/common.php');

#Read the response
$json = file_get_contents('php://input');

#Decode the json object
$req_details = json_decode($json,true);

#Validations
validateAuthKey($auth_key);

#Add/Update request takeup
$req_details['takeup_date'] = CURRENT_DATE_TIME;

//For unsubscribe action
if(!isset($req_details['unsubscribe_takeup_date'])) $req_details['unsubscribe_takeup_date'] = NULL;
if(isset($req_details['is_unsubscribe']) && isset($req_details['is_unsubscribe']) == 'true')
{
    if(!isset($req_details['unsubscribe_takeup_date'])) $req_details['unsubscribe_takeup_date'] = CURRENT_DATE_TIME;
}  

//For helper close action
if(!isset($req_details['is_helper_closed'])) $req_details['is_helper_closed'] = 0;

//For request comment
if(isset($req_details['comment'])) $req_details['comment_date'] = CURRENT_DATE_TIME;

header("Content-Type:text/json");
$response = json_encode(requestTakeup($conn,$req_details));
echo replaceActiveStatusWithTrueFalse($response);