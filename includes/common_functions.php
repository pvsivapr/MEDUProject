<?php

/**
 * Function to echo json response
 * @param json $response
 */
function convertResponse($response) {    
    echo json_encode($response);
    exit;
}

/**
 * Function to validate auth_key
 * @param string $auth_key
 * @return boolean
 */
function validateAuthKey($auth_key)
{
    if($auth_key == AUTH_KEY)
    {
        return true;
    }
    else{
        $response = array('status' => 0, 'message' => INVALID_AUTHKEY, 'response_info' => null);
        convertResponse($response);
    }
}


/**
 * Function to validate email
 * @param email $email
 * @param string $web
 * @return validation response
 */
function emailValidation($email, $web = null) {
    if ($email == null) {
        if ($web) {
            return EMPTY_EMAIL;
        } else {
            $response = array('status' => 0, 'message' => EMPTY_EMAIL, 'response_info' => null);
            convertResponse($response);
        }
    } else if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        if ($web) {
            return INVALID_EMAIL;
        } else {
            $response = array('status' => 0, 'message' => INVALID_EMAIL, 'response_info' => null);
            convertResponse($response);
        }
    }
    if ($web) {
        return null;
    }
}

/**
 * Function to validate username
 * @param type $username
 * @param type $web
 * @return type
 */
function userNameValidation($username, $web = null) {
    if ($username == null) {
        if ($web) {
            return EMPTY_USERNAME;
        } else {
            $response = array('status' => 0, 'message' => EMPTY_USERNAME, 'response_info' => null);
            convertResponse($response);
        }
    }
    if ($web) {
        return null;
    }
}

/**
 * Function to validate password
 * @param type $password
 * @param type $web
 * @return type
 */
function passwordValidation($password, $web = null) {
    if ($password == null) {
        if ($web) {
            return EMPTY_PASSWORD;
        } else {
            $response = array('status' => 0, 'message' => EMPTY_PASSWORD, 'response_info' => null);
            convertResponse($response);
        }
    } else if (strlen($password) < 6) {
        if ($web) {
            return EMPTY_PASSWORD_LEN;
        } else {
            $response = array('status' => 0, 'message' => EMPTY_PASSWORD_LEN, 'response_info' => null);
            convertResponse($response);
        }
    }
    if ($web) {
        return null;
    }
}

/**
 * Function to replace Active/Inactive status with true/false 
 * @param string $response
 * @return string
 */
function replaceActiveStatusWithTrueFalse($response)
{
    $active_string = '"is_active":"1"';
    $inactive_string = '"is_active":"0"';
    $active_replace_string = '"is_active": true';
    $inactive_replace_string = '"is_active": false';
    if (is_string($response)) {
        $updated_str = str_replace($active_string, $active_replace_string, $response);
        $updated_str = str_replace($inactive_string, $inactive_replace_string, $updated_str);
        return $updated_str;
    }
}

/**
 * Function to generate random string of specific length
 * @param int $length
 * @return random string with input int length
 */
function verificationCodeRandom($length = 6) {
    $characters = '0123456789LIFEIMPROVEMENTCOMMUNITY';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}