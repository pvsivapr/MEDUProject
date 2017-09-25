<?php
function __autoload($classname) {
    $filename = "../classes/". $classname .".php";
    include_once($filename);
}

/**
 * Function to validate user with his credentials
 * @param obj $conn
 * @param array $user_details
 */
function validateUserLogin($conn, $user_details) {
    try {
        $query = $conn->prepare("SELECT IFNULL(COUNT(u.user_id),0) AS user_count, user_id, is_active
        FROM users u
        WHERE u.email_id = '" . $user_details['email_id'] . "' AND u.password = MD5('" . $user_details['password'] . "');");
        $query->execute();
        $result = $query->fetchall(PDO::FETCH_ASSOC);
        if ($result[0]['user_count'] > 0) {
            if ($result[0]['is_active'] == 0) {
                $response = array('status' => 1, 'message' => INACTIVE_USER, 'response_info' => null);
                return $response;
            }
            $user_filters['user_id'] = $result[0]['user_id'];
            $user_data = selectUsers($conn, $user_filters);
            $response = array('status' => 1, 'message' => VALID_USER, 'response_info' => $user_data);
        } else {
            $response = array('status' => 0, 'message' => INVALID_USER, 'response_info' => null);
        }
        return $response;
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        return $response;
    }
}

/**
 * Function to get contact_number count
 * @param type $conn
 * @param type $email_id
 * @return type
 */
function selectContactNumberCount($conn, $contact_number) {
    try {
        $query = $conn->prepare("SELECT IFNULL(COUNT(u.contact_number),0) AS contact_number_count
        FROM users u
        WHERE contact_number = '" . $contact_number . "';");
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to get email count
 * @param type $conn
 * @param type $email_id
 * @return type
 */
function selectEmailCount($conn, $email_id) {
    try {
        $query = $conn->prepare("SELECT IFNULL(COUNT(u.email_id),0) AS email_count,user_id
        FROM users u
        WHERE email_id = '" . $email_id . "';");
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to get aadhar count
 * @param type $conn
 * @param type $aadhar_card_number
 * @return type
 */
function selectAadharCount($conn, $aadhar_card_number) {
    try {
        $query = $conn->prepare("SELECT IFNULL(COUNT(u.aadhar_card_number),0) AS aadhar_count
        FROM users u
        WHERE aadhar_card_number = '" . $aadhar_card_number . "';");
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $ex) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to register a user
 * @param obj $conn
 * @param array $user_details
 */
function userRegistration($conn, $user_details) {
    try {
        //Check email existance
        $email_count = selectEmailCount($conn, $user_details['email_id']);
        if ($email_count[0]['email_count'] > 0) {
            $response = array('status' => 0, 'message' => EMAIL_EXISTS, 'response_info' => null);
            convertResponse($response);
        }
        //Check aadhar existance
        $aadhar_count = selectAadharCount($conn, $user_details['aadhar_card_number']);
        if ($aadhar_count[0]['aadhar_count'] > 0) {
            $response = array('status' => 0, 'message' => AADHAR_EXISTS, 'response_info' => null);
            convertResponse($response);
        }
        //Check contact_numbe existance
        $contact_number_count = selectContactNumberCount($conn, $user_details['contact_number']);
        if ($contact_number_count[0]['contact_number_count'] > 0) {
            $response = array('status' => 0, 'message' => CONTACT_NUMBER_EXISTS, 'response_info' => null);
            convertResponse($response);
        }
        $query = $conn->prepare("INSERT INTO users SET "
                . "first_name=:first_name,last_name=:last_name, "
                . "email_id=:email_id,password=:password, "
                . "contact_number=:contact_number,dob=:dob, "
                . "gender=:gender,aadhar_card_number=:aadhar_card_number, "
                . "role_id=:role_id,created_date=:created_date,modified_date=:modified_date");
        $password = md5($user_details['password']);
        $query->bindParam(':first_name', $user_details['first_name']);
        $query->bindParam(':last_name', $user_details['last_name']);
        $query->bindParam(':email_id', $user_details['email_id']);
        $query->bindParam(':password', $password);
        $query->bindParam(':contact_number', $user_details['contact_number']);
        $query->bindParam(':dob', $user_details['dob']);
        $query->bindParam(':gender', $user_details['gender']);
        $query->bindParam(':aadhar_card_number', $user_details['aadhar_card_number']);
        $query->bindParam(':role_id', $user_details['role_id']);
        $query->bindParam(':created_date', $user_details['created_date']);
        $query->bindParam(':modified_date', $user_details['modified_date']);
        $query->execute();
        $response = array('status' => 1, 'message' => USER_REGISTRATION_SUCCESS, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the roles
 * @param obj $conn
 * @param array $filters
 */
function selectRoles($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT * FROM roles;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = 'r.' . $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT * FROM roles r WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the opportunities
 * @param obj $conn
 * @param array $filters
 */
function selectOpportunities($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT o.opportunity_id,o.opportunity_category_id,o.opportunity_title,o.opportunity_content,o.created_date, oc.opportunity_category_name, CONCAT(IFNULL(u.first_name,''),IFNULL(u.last_name,'')) as created_by, o.is_active
            FROM opportunities o
            INNER JOIN opportunity_category oc ON o.opportunity_category_id = oc.opportunity_category_id
            INNER JOIN users u ON u.user_id = o.created_by;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT o.opportunity_id,o.opportunity_category_id,o.opportunity_title,o.opportunity_content,o.created_date, oc.opportunity_category_name, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) as created_by, o.is_active
            FROM opportunities o
            INNER JOIN opportunity_category oc ON o.opportunity_category_id = oc.opportunity_category_id
            INNER JOIN users u ON u.user_id = o.created_by WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update the opportunities
 * @param obj $conn
 * @param array $data
 */
function opportunities($conn, $data = NULL) {
    try {

        //Insert if table primary_key_id is not set
        if ($data['opportunity_id'] == 0) {
            $message = OPPORTUNITY_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO opportunities SET "
                    . "opportunity_category_id=:opportunity_category_id,opportunity_title=:opportunity_title, "
                    . "opportunity_content=:opportunity_content,created_by=:created_by,created_date=:created_date,is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = OPPORTUNITY_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE opportunities SET "
                    . "opportunity_category_id=:opportunity_category_id,opportunity_title=:opportunity_title, "
                    . "opportunity_content=:opportunity_content,created_by=:created_by,created_date=:created_date,is_active=:is_active WHERE opportunity_id=" . $data['opportunity_id']);
        }

        $query->bindParam(':opportunity_category_id', $data['opportunity_category_id']);
        $query->bindParam(':opportunity_title', $data['opportunity_title']);
        $query->bindParam(':opportunity_content', $data['opportunity_content']);
        $query->bindParam(':created_by', $data['created_by']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->bindParam(':created_date', $data['created_date']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update the requests
 * @param obj $conn
 * @param array $data
 */
function requests($conn, $data = NULL) {
    try {

        //Insert if table primary_key_id is not set
        if ($data['request_id'] == 0) {
            $message = REQUEST_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO helper_request SET "
                    . "request_content=:request_content,receiver_id=:receiver_id, "
                    . "opportunity_category_id=:opportunity_category_id,request_date=:request_date,"
                    . "request_status_closed_date=:request_status_closed_date,request_status=:request_status");
        }
        //Update if table primary_key_id is not set
        else {
            $message = REQUEST_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE helper_request SET "
                    . "request_content=:request_content,receiver_id=:receiver_id, "
                    . "opportunity_category_id=:opportunity_category_id,request_date=:request_date,"
                    . "request_status_closed_date=:request_status_closed_date,request_status=:request_status WHERE request_id=" . $data['request_id']);
        }

        $query->bindParam(':request_content', $data['request_content']);
        $query->bindParam(':receiver_id', $data['receiver_id']);
        $query->bindParam(':opportunity_category_id', $data['opportunity_category_id']);
        $query->bindParam(':request_date', $data['request_date']);
        $query->bindParam(':request_status_closed_date', $data['request_status_closed_date']);
        $query->bindParam(':request_status', $data['request_status']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update opportunity categories
 * @param obj $conn
 * @param array $data
 */
function opportunityCategories($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['opportunity_category_id'] == 0) {
            $message = OPPORTUNITY_CATEGORY_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO opportunity_category SET "
                    . "opportunity_category_name=:opportunity_category_name, "
                    . "description=:description,is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = OPPORTUNITY_CATEGORY_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE opportunity_category SET "
                    . "opportunity_category_name=:opportunity_category_name,description=:description, "
                    . "is_active=:is_active WHERE opportunity_category_id=" . $data['opportunity_category_id']);
        }

        $query->bindParam(':opportunity_category_name', $data['opportunity_category_name']);
        $query->bindParam(':description', $data['description']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update the stories
 * @param obj $conn
 * @param array $data
 */
function stories($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['story_id'] == 0) {
            $message = STORY_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO stories SET "
                    . "story_category_id=:story_category_id,story_person_name=:story_person_name, "
                    . "story_title=:story_title,story_content=:story_content,created_by=:created_by,created_date=:created_date,is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = STORY_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE stories SET "
                    . "story_category_id=:story_category_id,story_person_name=:story_person_name, "
                    . "story_title=:story_title,story_content=:story_content,created_by=:created_by,created_date=:created_date,is_active=:is_active WHERE story_id=" . $data['story_id']);
        }

        $query->bindParam(':story_category_id', $data['story_category_id']);
        $query->bindParam(':story_person_name', $data['story_person_name']);
        $query->bindParam(':story_title', $data['story_title']);
        $query->bindParam(':story_content', $data['story_content']);
        $query->bindParam(':created_by', $data['created_by']);
        $query->bindParam(':created_date', $data['created_date']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update the users
 * @param obj $conn
 * @param array $data
 */
function users($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['user_id'] == 0) {
            $message = USER_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO users SET "
                    . "first_name=:first_name,last_name=:last_name, "
                    . "middle_name=:middle_name,email_id=:email_id,password=:password,"
                    . "contact_number=:contact_number,dob=:dob,gender=:gender,aadhar_card_number=:aadhar_card_number,"
                    . "is_active=:is_active,role_id=:role_id,created_date=:created_date,"
                    . "modified_date=:modified_date");
            $password = md5($data['password']);
            $query->bindParam(':password', $password);
        }
        //Update if table primary_key_id is not set
        else {
            $message = USER_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE users SET "
                    . "first_name=:first_name,last_name=:last_name, "
                    . "middle_name=:middle_name,email_id=:email_id,"
                    . "contact_number=:contact_number,dob=:dob,gender=:gender,aadhar_card_number=:aadhar_card_number,"
                    . "is_active=:is_active,role_id=:role_id,created_date=:created_date,"
                    . "modified_date=:modified_date WHERE user_id=" . $data['user_id']);
        }
        //Hashed password
        
        $query->bindParam(':first_name', $data['first_name']);
        $query->bindParam(':last_name', $data['last_name']);
        $query->bindParam(':middle_name', $data['middle_name']);
        $query->bindParam(':email_id', $data['email_id']);        
        $query->bindParam(':contact_number', $data['contact_number']);
        $query->bindParam(':dob', $data['dob']);
        $query->bindParam(':gender', $data['gender']);
        $query->bindParam(':aadhar_card_number', $data['aadhar_card_number']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->bindParam(':role_id', $data['role_id']);
        $query->bindParam(':created_date', $data['created_date']);
        $query->bindParam(':modified_date', $data['modified_date']);

        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update story categories
 * @param obj $conn
 * @param array $data
 */
function storyCategories($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['story_category_id'] == 0) {
            $message = STORY_CATEGORY_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO story_category SET "
                    . "story_category_name=:story_category_name,description=:description, "
                    . "is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = STORY_CATEGORY_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE story_category SET "
                    . "story_category_name=:story_category_name,description=:description, "
                    . "is_active=:is_active WHERE story_category_id=" . $data['story_category_id']);
        }

        $query->bindParam(':story_category_name', $data['story_category_name']);
        $query->bindParam(':description', $data['description']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the opportunity categories
 * @param obj $conn
 * @param array $filters
 */
function selectOpportunityCategories($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT o.opportunity_category_id,o.opportunity_category_name,o.description, o.is_active
            FROM opportunity_category o;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT o.opportunity_category_id,o.opportunity_category_name,o.description, o.is_active
            FROM opportunity_category o WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the story categories
 * @param obj $conn
 * @param array $filters
 */
function selectStoryCategories($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT * FROM story_category;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = 'sc.' . $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT * FROM story_category sc WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the stories
 * @param obj $conn
 * @param array $filters
 */
function selectStories($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT s.story_id,s.story_category_id,s.story_person_name,s.story_title,s.story_content,s.created_date,s.is_active, sc.story_category_name, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS created_by
            FROM stories s
            INNER JOIN story_category sc ON sc.story_category_id = s.story_category_id
            INNER JOIN users u ON u.user_id = s.created_by;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT s.story_id,s.story_category_id,s.story_person_name,s.story_title,s.story_content,s.created_date,s.is_active, sc.story_category_name, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS created_by
            FROM stories s
            INNER JOIN story_category sc ON sc.story_category_id = s.story_category_id
            INNER JOIN users u ON u.user_id = s.created_by WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the requests
 * @param obj $conn
 * @param array $filters
 */
function selectRequests($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT r.takeup_id,r.takeup_date,hr.request_id,hr.request_content,hr.receiver_id,hr.opportunity_category_id,o.opportunity_category_name,hr.request_date,hr.request_status_closed_date,hr.request_status, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS receiver, u.contact_number
            FROM helper_request hr
            INNER JOIN users u ON u.user_id = hr.receiver_id            
            LEFT JOIN receiver_takeup r on r.helper_request_id = hr.request_id
            INNER JOIN opportunity_category o ON o.opportunity_category_id = hr.opportunity_category_id;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT r.takeup_id,r.takeup_date,hr.request_id,hr.request_content,hr.receiver_id,hr.opportunity_category_id,o.opportunity_category_name,hr.request_date,hr.request_status_closed_date,hr.request_status, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS receiver,contact_number
            FROM helper_request hr
            INNER JOIN users u ON u.user_id = hr.receiver_id
            LEFT JOIN receiver_takeup r on r.helper_request_id = hr.request_id
            INNER JOIN opportunity_category o ON o.opportunity_category_id = hr.opportunity_category_id WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the users
 * @param obj $conn
 * @param array $filters
 */
function selectUsers($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT u.user_id,u.first_name,u.last_name,u.middle_name,u.email_id,u.contact_number,u.dob,u.gender,u.aadhar_card_number, u.is_active,u.role_id,u.contact_number,u.created_date,u.modified_date,r.role_code
            FROM users u
            INNER JOIN roles r ON r.role_id = u.role_id;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT u.user_id,u.first_name,u.last_name,u.middle_name,u.email_id,u.contact_number,u.dob,u.gender,u.aadhar_card_number, u.is_active,u.role_id,u.contact_number,u.created_date,u.modified_date,r.role_code
            FROM users u
            INNER JOIN roles r ON r.role_id = u.role_id WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the events
 * @param obj $conn
 * @param array $filters
 */
function selectEvents($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT e.event_id,e.event_name,e.event_content,e.event_date, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS created_by,e.is_active
            FROM events e
            INNER JOIN users u ON u.user_id = e.created_by;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT e.event_id,e.event_name,e.event_content,e.event_date, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS created_by,e.is_active
            FROM events e
            INNER JOIN users u ON u.user_id = e.created_by WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update the events
 * @param obj $conn
 * @param array $data
 */
function events($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['event_id'] == 0) {
            $message = EVENT_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO events SET "
                    . "event_name=:event_name,event_content=:event_content, "
                    . "event_date=:event_date,created_by=:created_by");
        }
        //Update if table primary_key_id is not set
        else {
            $message = EVENT_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE events SET "
                    . "event_name=:event_name,event_content=:event_content, "
                    . "event_date=:event_date,created_by=:created_by WHERE event_id=" . $data['event_id']);
        }

        $query->bindParam(':event_name', $data['event_name']);
        $query->bindParam(':event_content', $data['event_content']);
        $query->bindParam(':event_date', $data['event_date']);
        $query->bindParam(':created_by', $data['created_by']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update  the quotes
 * @param obj $conn
 * @param array $data
 */
function quotes($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['quote_id'] == 0) {
            $message = QUOTE_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO quote_of_the_day SET "
                    . "quote_content=:quote_content,quote_date=:quote_date, "
                    . "created_by=:created_by,is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = QUOTE_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE quote_of_the_day SET "
                    . "quote_content=:quote_content,quote_date=:quote_date, "
                    . "created_by=:created_by,is_active=:is_active WHERE quote_id=" . $data['quote_id']);
        }

        $query->bindParam(':quote_content', $data['quote_content']);
        $query->bindParam(':quote_date', $data['quote_date']);
        $query->bindParam(':created_by', $data['created_by']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to insert/update  the testimonials
 * @param obj $conn
 * @param array $data
 */
function testimonials($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['testimonial_id'] == 0) {
            $message = TESTIMONIAL_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO testimonials SET "
                    . "testimonial_content=:testimonial_content,person_name=:person_name, "
                    . "is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = TESTIMONIAL_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE testimonials SET "
                    . "testimonial_content=:testimonial_content,person_name=:person_name, "
                    . "is_active=:is_active WHERE testimonial_id=" . $data['testimonial_id']);
        }

        $query->bindParam(':testimonial_content', $data['testimonial_content']);
        $query->bindParam(':person_name', $data['person_name']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the quotes
 * @param obj $conn
 * @param array $filters
 */
function selectQuotes($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT q.quote_id,q.quote_content,q.quote_date,q.is_active, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS created_by
            FROM quote_of_the_day AS q
            INNER JOIN users u ON u.user_id = q.created_by;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT q.quote_id,q.quote_content,q.quote_date,q.is_active, CONCAT(IFNULL(u.first_name,''), IFNULL(u.last_name,'')) AS created_by
            FROM quote_of_the_day AS q
            INNER JOIN users u ON u.user_id = q.created_by WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the testimonials
 * @param obj $conn
 * @param array $filters
 */
function selectTestimonials($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT testimonial_id,testimonial_content,person_name,is_active
            FROM testimonials;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT testimonial_id,testimonial_content,person_name,is_active
            FROM testimonials WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to request takeup
 * @param obj $conn
 * @param array $filters
 */
function requestTakeup($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['takeup_id'] > 0) {
            $message = REQUEST_TAKEUP_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE receiver_takeup SET "
                    . "helper_request_id=:helper_request_id,unsubscribe_takeup_date=:unsubscribe_takeup_date, "
                    . "helper_id=:helper_id,takeup_status=:takeup_status,"
                    . "is_helper_closed=:is_helper_closed WHERE takeup_id=:takeup_id");
            $query->bindParam(':takeup_id', $data['takeup_id']);
            $query->bindParam(':helper_request_id', $data['helper_request_id']);
            $query->bindParam(':helper_id', $data['helper_id']);
            $query->bindParam(':takeup_status', $data['takeup_status']);
            $query->bindParam(':unsubscribe_takeup_date', $data['unsubscribe_takeup_date']);
            $query->bindParam(':is_helper_closed', $data['is_helper_closed']);
            $query->execute();
        }
        //Update if table primary_key_id is not set
        else {
            $message = REQUEST_TAKEUP_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO receiver_takeup SET "
                    . "helper_request_id=:helper_request_id,takeup_date=:takeup_date, "
                    . "helper_id=:helper_id,takeup_status=:takeup_status,"
                    . "unsubscribe_takeup_date=:unsubscribe_takeup_date,is_helper_closed=:is_helper_closed");
            $query->bindParam(':helper_request_id', $data['helper_request_id']);
            $query->bindParam(':takeup_date', $data['takeup_date']);
            $query->bindParam(':helper_id', $data['helper_id']);
            $query->bindParam(':takeup_status', $data['takeup_status']);
            $query->bindParam(':unsubscribe_takeup_date', $data['unsubscribe_takeup_date']);
            $query->bindParam(':is_helper_closed', $data['is_helper_closed']);
            $query->execute();
            $data['takeup_id'] = $conn->lastInsertId();
        }

        //Insert takeup_comment if it is set
        if (strlen(trim($data['comment'])) > 0) {
            $message = REQUEST_TAKEUP_COMMENT_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO takeup_comment SET "
                    . "comment=:comment,takeup_id=:takeup_id, "
                    . "created_by=:created_by,comment_date=:comment_date");
            $query->bindParam(':comment', $data['comment']);
            $query->bindParam(':takeup_id', $data['takeup_id']);
            $query->bindParam(':created_by', $data['created_by']);
            $query->bindParam(':comment_date', $data['comment_date']);
            $query->execute();
        }
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the takeup comments
 * @param obj $conn
 * @param array $filters
 */
function selectTakeupComments($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT rt.takeup_id,rt.helper_request_id,rt.takeup_date, CONCAT(IFNULL(u1.first_name,''), IFNULL(u1.last_name,'')) AS helper_name,rt.takeup_status,rt.unsubscribe_takeup_date,rt.is_helper_closed,tc.comment_id,tc.`comment`, CONCAT(IFNULL(u2.first_name,''), IFNULL(u2.last_name,'')) AS comment_created_by,tc.comment_date
            FROM receiver_takeup rt
            INNER JOIN takeup_comment tc ON tc.takeup_id = rt.takeup_id
            INNER JOIN users u1 ON u1.user_id = rt.helper_id
            INNER JOIN users u2 ON u2.user_id = tc.created_by;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT rt.takeup_id,rt.helper_request_id,rt.takeup_date, CONCAT(IFNULL(u1.first_name,''), IFNULL(u1.last_name,'')) AS helper_name,rt.takeup_status,rt.unsubscribe_takeup_date,rt.is_helper_closed,tc.comment_id,tc.`comment`, CONCAT(IFNULL(u2.first_name,''), IFNULL(u2.last_name,'')) AS comment_created_by,tc.comment_date
            FROM receiver_takeup rt
            INNER JOIN takeup_comment tc ON tc.takeup_id = rt.takeup_id
            INNER JOIN users u1 ON u1.user_id = rt.helper_id
            INNER JOIN users u2 ON u2.user_id = tc.created_by WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to change user password
 * @param obj $conn
 * @param array $data
 */
function changePassword($conn, $data = NULL) {
    try {
        //Check email existance
        $email_count = selectEmailCount($conn, $data['email_id']);
        if ($email_count[0]['email_count'] > 0) {
            $query = $conn->prepare("UPDATE users SET password=:password,"
                    . "modified_date=:modified_date WHERE user_id=" . $email_count[0]['user_id']);
            //Hashed password
            $password = md5($data['password']);
            $query->bindParam(':password', $password);
            $query->bindParam(':modified_date', $data['modified_date']);

            $query->execute();
            $response = array('status' => 1, 'message' => PASSWORD_UPDATE_SUCESS, 'response_info' => null);
        } else {
            $response = array('status' => 1, 'message' => INVALID_EMAIL, 'response_info' => null);
        }
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to implement forgot password
 * @param obj $conn
 * @param string $user_email_id
 */
function forgotPassword($conn, $email_id) {
    try {
        //Check email existance
        $email_count = selectEmailCount($conn, $email_id);
        if ($email_count[0]['email_count'] == 0) {
            $response = array('status' => 0, 'message' => EMAIL_DOESNOT_EXISTS, 'response_info' => null);
            convertResponse($response);
        } else {
            $password = verificationCodeRandom();
            $hashed_password = md5($password);
            try {
                $password_update = $conn->prepare("UPDATE users SET  password=:password WHERE  email_id=:email_id");
                $password_update->bindParam(':email_id', $email_id);
                $password_update->bindParam(':password', $hashed_password);
                $password_update->execute();
                $to = $from = $email_id;
                $to_name = $from_name = FORGOT_PASSWORD_FROM_NAME;
                $subject = FORGOT_PASSWORD_SUB;
                $body = "Dear " . ucfirst(strtolower($user_name)) . ",<br/> Your new passowd is " . $password;
                mail::sendMail($to, $to_name, $from, $from_name, $subject, $body);
                $response = array('status' => 1, 'message' => FORGET_PASSWORD_SUCCESS, 'response_info' => null);
                convertResponse($response);
            } catch (Exception $e) {
                $response = array('status' => 0, 'message' => $e->getMessage(), 'response_info' => null);
                convertResponse($response);
            }
        }
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => FORGET_PASSWORD_ERROR, 'response_info' => null);
        convertResponse($response);
    }
}


/**
 * Function to insert/update the incubation centers
 * @param obj $conn
 * @param array $data
 */
function incubationCenters($conn, $data = NULL) {
    try {

        //Insert if table primary_key_id is not set
        if ($data['incubation_center_id'] == 0) {
            $message = INCUBATION_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO incubation_centers SET "
                    . "incubation_center_category_id=:incubation_center_category_id,incubation_center_title=:incubation_center_title, "
                    . "incubation_center_content=:incubation_center_content,created_by=:created_by,created_date=:created_date,is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = INCUBATION_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE incubation_centers SET "
                    . "incubation_center_category_id=:incubation_center_category_id,incubation_center_title=:incubation_center_title, "
                    . "incubation_center_content=:incubation_center_content,created_by=:created_by,created_date=:created_date,is_active=:is_active WHERE incubation_center_id=" . $data['incubation_center_id']);
        }

        $query->bindParam(':incubation_center_category_id', $data['incubation_center_category_id']);
        $query->bindParam(':incubation_center_title', $data['incubation_center_title']);
        $query->bindParam(':incubation_center_content', $data['incubation_center_content']);
        $query->bindParam(':created_by', $data['created_by']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->bindParam(':created_date', $data['created_date']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}


/**
 * Function to insert/update incubation center categories
 * @param obj $conn
 * @param array $data
 */
function incubationCenterCategories($conn, $data = NULL) {
    try {
        //Insert if table primary_key_id is not set
        if ($data['incubation_center_category_id'] == 0) {
            $message = INCUBATION_CATEGORY_INSERT_SUCCESS;
            $query = $conn->prepare("INSERT INTO incubation_center_category SET "
                    . "incubation_center_category_name=:incubation_center_category_name, "
                    . "description=:description,is_active=:is_active");
        }
        //Update if table primary_key_id is not set
        else {
            $message = INCUBATION_CATEGORY_UPDATE_SUCCESS;
            $query = $conn->prepare("UPDATE incubation_center_category SET "
                    . "incubation_center_category_name=:incubation_center_category_name,description=:description, "
                    . "is_active=:is_active WHERE incubation_center_category_id=" . $data['incubation_center_category_id']);
        }

        $query->bindParam(':incubation_center_category_name', $data['incubation_center_category_name']);
        $query->bindParam(':description', $data['description']);
        $query->bindParam(':is_active', $data['is_active']);
        $query->execute();
        $response = array('status' => 1, 'message' => $message, 'response_info' => null);
        convertResponse($response);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}


/**
 * Function to fetch the incubation centers
 * @param obj $conn
 * @param array $filters
 */
function selectIncubationCenters($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT i.incubation_center_id,i.incubation_center_category_id,i.incubation_center_title,i.incubation_center_content,i.created_date, ic.incubation_center_category_name, CONCAT(IFNULL(u.first_name,''),IFNULL(u.last_name,'')) as created_by, i.is_active
            FROM incubation_centers i
            INNER JOIN incubation_center_category ic ON i.incubation_center_category_id = ic.incubation_center_category_id
            INNER JOIN users u ON u.user_id = i.created_by;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT i.incubation_center_id,i.incubation_center_category_id,i.incubation_center_title,i.incubation_center_content,i.created_date, ic.incubation_center_category_name, CONCAT(IFNULL(u.first_name,''),IFNULL(u.last_name,'')) as created_by, i.is_active
            FROM incubation_centers i
            INNER JOIN incubation_center_category ic ON i.incubation_center_category_id = ic.incubation_center_category_id
            INNER JOIN users u ON u.user_id = i.created_by WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}

/**
 * Function to fetch the incubation center categories
 * @param obj $conn
 * @param array $filters
 */
function selectIncubationCenterCategories($conn, $filters = NULL) {
    try {
        if ($filters == NULL) {
            $query = $conn->prepare("SELECT i.incubation_center_category_id,i.incubation_center_category_name,i.description, i.is_active
            FROM incubation_center_category i;");
        } else {
            foreach ($filters as $col_name => $value) {
                $where_condition[] = $col_name . ' = "' . $value . '"';
            }
            $where_condition_string = implode(' AND ', $where_condition);
            $query = $conn->prepare("SELECT i.incubation_center_category_id,i.incubation_center_category_name,i.description, i.is_active
            FROM incubation_center_category i WHERE " . $where_condition_string . ";");
        }
        $query->execute();
        return $query->fetchall(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        $response = array('status' => 0, 'message' => UNABLE_TO_PROCESS, 'response_info' => null);
        convertResponse($response);
    }
}
