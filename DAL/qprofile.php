<?php
    require_once './class/privateProfile.php';
    require_once './class/publicProfile.php';    

    class qprofile
    {
        private $db;
        function __construct($db)
        {
            $this->db = $db;
        }


        public function getPrivateProfileId($authId)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT profile.id FROM userprofile AS profile
            WHERE profile.authId = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("i", $authId);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1)
            {
                $res = $result->fetch_assoc();
                if (isset($res['id']))
                    $out->data = $res['id'];
                else
                {
                    //$out->message = "Token not valid to retrieve data for user with id:".$userId;
                    //$out->error_message = "Token:".$token." is not valid with the user with user_id:".$userId;
                }
            }
            else
            {
                $out->status = false;
                $out->message = "No record found";
            }

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();
            
            return $out;
        }


        /**
         * getPrivateProfile, returns a record of one private profile
         * request is rejected if 
         */
        public function getPrivateProfileByUid($userId, $token)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.*, session.sessionToken FROM userprofile AS profile 
            INNER JOIN session ON profile.authId = session.authId
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.id = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1)
            {
                $res = $result->fetch_assoc();
                if ($res['sessionToken'] == $token)
                    $out->data = $res;
                else
                {
                    $out->status = false;
                    $out->err = __err["0x12"];
                    //$out->message = "Token not valid to retrieve data for user with id:".$userId;
                    //$out->error_message = "Token:".$token." is not valid with the user with user_id:".$userId;
                }
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x13"];
                //$out->message = "No record found";
            }

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();
            
            return $out;
        }

                /**
         * getPrivateProfile, returns a record of one private profile
         * request is rejected if 
         */
        public function getPrivateProfileByAuthId($authId, $token)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.* FROM userprofile AS profile 
            INNER JOIN session ON profile.authId = session.authId
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.authId = ? AND session.sessionToken = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("is", $authId, $token);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1)
            {
                $res = $result->fetch_assoc();
                $out->data = $res;
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x13"];
                //$out->message = "No record found";
            }

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();
            
            return $out;
        }

        /**
         * getSinglePublicProfile, returns a record of one public profile
         * Requires userId
         */
        public function getSinglePublicProfile($userId)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.id, profile.image, profile.firstName, profile.lastName, profile.phone FROM userprofile AS profile 
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.id = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("i", $userId);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows == 1)
            {
                $res = $result->fetch_assoc();
                $out->data = $res;
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x12"];
                //$out->message = "No record found";
            }

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();
            
            return $out;
        }

        /*public function getMultiplePublicProfile($userId)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.id, profile.image, profile.firstName, profile.lastName, profile.phone FROM userprofile AS profile 
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.id = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("i", $userId);

            if ($stmt->rowCount() > 0)
            {
                $out->data = $stmt->fetch();
            }
            else
            {
                $out->status = false;
                $out->message = "No record found";
            }

            
            $stmt->execute();
            $stmt->close();
            
            return $out;
        }*/

        public function postPrivateProfile($token, $profile)
        {
            $out = new stdClass();
            $out->status = true;
            $out->message = "";
            $out->error_message = "";
            //print_r($profile);
            if ($profile->id == null)
            {
                /** User does not exists */
                $queryText = "INSERT INTO userprofile (authId, firstName, lastName, image, address, postNumber, phone) 
                                            VALUES    (     ?,         ?,        ?,     ?,       ?,          ?,     ?)";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("issssss", 
                    $profile->authId, 
                    $profile->firstName, 
                    $profile->lastName, 
                    $profile->image,
                    $profile->address,
                    $profile->postNumber, 
                    $profile->phoneNumber
                );
                $success = $stmt->execute();
                if (!$success || $stmt->affected_rows == 0)
                {
                    $out->status = false;
                    $out->err = __err["0x14"];
                    //$out->message = "Failed to create profile";
                    //$out->error_message = $stmt->error;
                }

                $stmt->free_result();
                $stmt->close();
            }
            else
            {
                /** User exists */
                $queryText = "UPDATE userprofile SET
                firstName = ?,
                lastName = ?,
                image = ?,
                address = ?,
                postNumber = ?,
                phone = ?
                WHERE id = ?";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("ssssssi", 
                    $profile->firstName, 
                    $profile->lastName,
                    $profile->image,
                    $profile->address,
                    $profile->postNumber,
                    $profile->phoneNumber,
                    $profile->id);
                $status = $stmt->execute();
                if ($status == false)
                {
                    $out->status = false;
                    $out->message = "Failed to update profile";
                    $out->error_message = $stmt->error;
                    error_log("database error: " . $this->db->error);
                }
                else if ($stmt->affected_rows == 0)
                {
                    // No changes applied
                    $out->status = true;
                    $out->message = "No changes were made";
                }
                

                $stmt->free_result();
                $stmt->close();
            }

            /** Update email */
            if ($profile->email != null && isset($profile->email))
            {
                $queryText = "UPDATE auth SET email = ? WHERE id = ?";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("si", $profile->email, $profile->authId);
                $success = $stmt->execute();
    
                if ($success && $stmt->affected_rows == 0)
                {
                    //$out->message .= "| Email is not changed";
                }
                else if ($stmt->affected_rows >= 1)
                {
                    $out->message .= "; Email has been updated";
                }
                else
                {
                    $out->err = __err["0x15"];
                    //$out->message .= "| Email failed to update";
                    //$out->error_message .= " | " . $stmt->error;
                }
            }
            return $out;

        }

        public function forget_me($session)
        {
            $q1 = "DELETE FROM missing WHERE id = (SELECT id FROM userprofile WHERE authId = ?)";
            $q2 = "DELETE FROM foundAnimal WHERE id = (SELECT foundAnimalId FROM found WHERE userId = (SELECT id FROM userprofile WHERE authId = ?))";
            $q3 = "DELETE FROM found WHERE id = (SELECT id FROM userprofile WHERE authId = ?)";

            $q4 = "DELETE FROM animalprofile WHERE userId = (SELECT id FROM userprofile WHERE authId = ?)";
            $q5 = "DELETE FROM FROM userprofile WHERE authId = ?";
            $q6 = "DELETE FROM session WHERE authId = ?";
            $q7 = "DELETE FROM auth WHERE id = ?";

            $array = array($q1, $q2, $q3, $q4, $q5, $q6, $q7);

            $out = $this->execForgetMe($array);
            return $out;
        }

        function execForgetMe($array)
        {
            $out = new stdClass();
            $out->status = false;

            for( $i = 0; $i < count($array); $i++ )
            {
                $stmt = $this->db->prepare($array[$i]);
                $stmt->bind_param("i", $session->authId);
                $status = $stmt->execute();
                $stmt->close();
    
                if ($status == false)
                {
                    $out->err = __err["0x28"];
                    return $out;
                }
            }
            $out->status = true;
            return $out;
        }


    }


?>