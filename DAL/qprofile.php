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
                    $out->message = "Token not valid to retrieve data for user with id:".$userId;
                    $out->error_message = "Token:".$token." is not valid with the user with user_id:".$userId;
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
                    $out->message = "Token not valid to retrieve data for user with id:".$userId;
                    $out->error_message = "Token:".$token." is not valid with the user with user_id:".$userId;
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
        public function getPrivateProfileByAuthId($authId, $token)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.*, session.sessionToken FROM userprofile AS profile 
            INNER JOIN session ON profile.authId = session.authId
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.authId = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("i", $authId);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows == 1)
            {
                $res = $result->fetch_assoc();
                if ($res['sessionToken'] == $token)
                    $out->data = $res;
                else
                {
                    $out->message = "Token not valid to retrieve data for user with id:".$authId;
                    $out->error_message = "Token:".$token." is not valid with the user with user_id:".$authId;
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
                $out->message = "No record found";
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
                $stmt->bind_param("issssis", 
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
                    $out->message = "Failed to create profile";
                    $out->error_message = $stmt->error;
                }

                $stmt->free_result();
                $stmt->close();
            }
            else
            {
                /** User exists */
                $queryText = "UPDATE userprofile
                SET firstName = ?,
                SET lastName = ?,
                SET image = ?,
                SET address = ?,
                SET postNumber = ?,
                SET phone = ?
                WHERE id = ?";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("ssssisi", 
                    $profile->firstName, 
                    $profile->lastName,
                    $profile->image,
                    $profile->address,
                    $profile->postNumber,
                    $profile->phone,
                    $profile->id);
                $status = $stmt->execute();
                if ($stauts == false || $stmt->affected_rows == 0)
                {
                    $out->status = false;
                    $out->message = "Failed to update profile";
                    $out->error_message = $stmt->error;
                }

                $stmt->free_result();
                $stmt->close();
            }

            /** Update email */
            $queryText = "UPDATE auth SET email = ? WHERE id = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("si", $profile->email, $profile->authId);
            $success = $stmt->execute();

            if ($success && $stmt->affected_rows == 0)
            {
                $out->message .= "| Email is not changed";
            }
            else if ($stmt->affected_rows >= 1)
            {
                $out->message .= "; Email has been updated";
            }
            else
            {
                $out->message .= "| Email failed to update";
                $out->error_message .= " | " . $stmt->error;
            }
            return $out;

        }



    }


?>