<?php 
error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
define('ENVIRONMENT','development');

$hostName = 'www.devrabbit.com';//isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : '192.168.1.160';
if(ENVIRONMENT == 'development')
{
    $hostName = 'localhost:8080';
}

define('PATH','http://'.$hostName.'/inspiring_minds/');
define('AUTH_KEY','55a2bc0181d79fd2db84d5e147698dc7');

define('UNABLE_TO_PROCESS','Unable to process your request!');
define('CURRENT_DATE',date('Y-m-d'));
define('CURRENT_TIME',date('H:i:s'));
define('CURRENT_DATE_TIME',date('Y-m-d H:i:s'));
define('INVALID_AUTHKEY','Invalid Auth');
define('VALID_USER','Valid User');
define('INVALID_USER','Invalid User');
define('INACTIVE_USER','Your account has beed deactivated!');
define('EMAIL_EXISTS','Email ID is already registered!');
define('EMAIL_DOESNOT_EXISTS','Email ID is not registered with us!');
define('AADHAR_EXISTS','Aadhar is already registered!');
define('CONTACT_NUMBER_EXISTS','Contact number is already registered!');
define('EVENT_INSERT_SUCCESS','Event details inserted successfully!');
define('EVENT_UPDATE_SUCCESS','Event details updated successfully!');
define('OPPORTUNITY_INSERT_SUCCESS','Opportunity details inserted successfully!');
define('OPPORTUNITY_UPDATE_SUCCESS','Opportunity details updated successfully!');
define('QUOTE_INSERT_SUCCESS','Quote details inserted successfully!');
define('QUOTE_UPDATE_SUCCESS','Quote details updated successfully!');
define('STORY_INSERT_SUCCESS','Story details inserted successfully!');
define('STORY_UPDATE_SUCCESS','Story details updated successfully!');
define('OPPORTUNITY_CATEGORY_INSERT_SUCCESS','Opportunity category details inserted successfully!');
define('OPPORTUNITY_CATEGORY_UPDATE_SUCCESS','Opportunity category details updated successfully!');
define('STORY_CATEGORY_INSERT_SUCCESS','Story category details inserted successfully!');
define('STORY_CATEGORY_UPDATE_SUCCESS','Story category details updated successfully!');
define('USER_INSERT_SUCCESS','User details inserted successfully!');
define('USER_UPDATE_SUCCESS','User details updated successfully!');
define('TESTIMONIAL_INSERT_SUCCESS','Testimonial details inserted successfully!');
define('TESTIMONIAL_UPDATE_SUCCESS','Testimonial details updated successfully!');
define('REQUEST_INSERT_SUCCESS','Request details inserted successfully!');
define('REQUEST_UPDATE_SUCCESS','Request details updated successfully!');
define('REQUEST_TAKEUP_INSERT_SUCCESS','Request take up inserted successfully!');
define('REQUEST_TAKEUP_UPDATE_SUCCESS','Request take up updated successfully!');
define('REQUEST_TAKEUP_COMMENT_INSERT_SUCCESS','Takeup comment details inserted successfully!');
define('REQUEST_TAKEUP_COMMENT_UPDATE_SUCCESS','Takeup comment details updated successfully!');
define('PASSWORD_UPDATE_SUCESS','Password has been updated successfully!');
define('FORGOT_PASSWORD_SUB','Forgot Password - Generated new password!');
define('FORGOT_PASSWORD_FROM_NAME','IncAlert');
define('FORGET_PASSWORD_ERROR',"You entered email id doesn't exists in our records!");
define('FORGET_PASSWORD_SUCCESS',"New password sent to your email id.!");
define('INCUBATION_INSERT_SUCCESS','Incubation center details inserted successfully!');
define('INCUBATION_UPDATE_SUCCESS','Incubation center details updated successfully!');
define('INCUBATION_CATEGORY_INSERT_SUCCESS','Incubation category details inserted successfully!');
define('INCUBATION_CATEGORY_UPDATE_SUCCESS','Incubation category details updated successfully!');

define('INVALID_EMAIL','Invalid Email');
define('INVALID_USERNAME','Invalid Username');
define('INVALID_PASSWORD','Invalid Password');
define('USER_REGISTRATION_SUCCESS', 'User registered successfully!')

?>