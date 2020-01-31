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

        /**
         * getPrivateProfile, returns a record of one private profile
         * request is rejected if 
         */
        public function getPrivateProfile($userId, $token)
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

        public function postPublicProfile($token, $profile)
        {
            $out = new stdClass();
            $out->status = true;
            if ($profile->id == null)
            {
                /** User does not exists */
                $queryText = "INSERT INTO userprofile (authId, firstName, lastName, email, image, address, postnumber, phone) VALUES
                                                      ( ?,     ?,         ?,        ?,     ?,     ?,       ?,          ?)";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("isssssis", $profile->authId, $profile->firstName, $profile->lastName, $profile->email, $profile->image, $profile->address, $profile->postnumber, $profile->phoneNumber);
                $stmt->execute();
                $result = $stmt->get_result();
                if ($result->num_rows == 0)
                {
                    $out->status = false;
                    $out->message = "Failed to create profile";
                    $out->error_message = $stmt->error();
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
                SET email = ?,
                SET image = ?,
                SET address = ?,
                SET postnumber = ?,
                SET phone = ?
                WHERE id = ?";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("sssssisi", 
                    $profile->firstName, 
                    $profile->lastName,
                    $profile->email,
                    $profile->image,
                    $profile->address,
                    $profile->postnumber,
                    $profile->phone,
                    $profile->id);
                $stmt->execute();
                if ($stmt->rowCount() == 0)
                {
                    $out->status = false;
                    $out->message = "Failed to create profile";
                    $out->error_message = $stmt->errorInfo();
                }

                $stmt->free_result();
                $stmt->close();
            }

        }



    }


?>