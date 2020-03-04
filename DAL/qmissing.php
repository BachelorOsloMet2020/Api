<?php

    class qmissing
    {
        private $db;
        function __construct($db)
        {
            $this->db = $db;
        }

        public function getMissingById($id)
        {
            $out = new stdclass();
            $out->status = true;

            $queryText = "SELECT missing.id AS missingId, missing.lat, missing.lng, missing.timeDate, missing.description AS mdesc,
            profile.id AS animalId, profile.userId, image, idTag, name, animalType, animalTypeExtras, sex, sterilized, color, furLength, furPattern, profile.description, area
            FROM missing
            INNER JOIN animalprofile AS profile ON missing.animalId = profile.id WHERE missing.id = ?;";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("i", $id);
            $ex = $stmt->execute();
            
            $result = $stmt->get_result();
            if (false === $ex || false == $stmt)
            {
                $out->status = false;
            }
            else
            {
                $out->data = $result->fetch_assoc();
            }

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            return $out;
        }

        public function getMissing()
        {
            $out = new stdclass();
            $out->status = true;

            $queryText = "SELECT missing.id AS missingId, missing.lat, missing.lng, missing.timeDate,
            profile.id AS animalId, image, name, animalType, animalTypeExtras, color, area, missing.description AS mdesc
            FROM missing
            INNER JOIN animalprofile AS profile ON missing.animalId = profile.id;";
            $query = $this->db->query($queryText);
            if (false === $query)
            {
                $out->status = false;
            }
            else
            {
                $out->data = $query->fetch_all(MYSQLI_ASSOC);
            }

            return $out;
        }

        public function getMyMissing($uid)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT missing.id AS missingId, missing.lat, missing.lng, missing.timeDate,
            profile.id AS animalId, image, name, animalType, animalTypeExtras, color, area, missing.description AS mdesc
            FROM missing
            INNER JOIN animalprofile AS profile ON missing.animalId = profile.id WHERE missing.userId = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("i", $uid);
            $ex = $stmt->execute();
            $result = $stmt->get_result();
            if (false === $ex || false == $stmt)
            {
                $out->status = false;
                $out->error_message = $stmt->error;
            }
            else
            {
                $out->data = $result->fetch_all(MYSQLI_ASSOC);
            }

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            return $out;
        }

        public function deleteMissing($authId, $token, $userId, $missingId)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.* FROM userprofile AS profile 
            INNER JOIN session ON profile.authId = session.authId
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.id = ? AND profile.authId = ? AND session.sessionToken = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("iis", $userId, $authId, $token);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows != 1)
            {
                $out->data = "auhtId: ".$authId. " :: userId: ".$userId. " :: missingId: ".$missingId." :: token: ".$token;
                $out->status = false;
                $out->numRows = $result->num_rows;
                $out->debugStmt = $stmt->error;
                $out->debugDb = $this->db->error;
            }
            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            if ($out->status == false)
            {
                return $out;
            }

            $queryText = "DELETE FROM missing WHERE id = ? AND userId = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("ii", $missingId, $userId);
            $success = $stmt->execute();
            if ($success || $stmt->affected_rows != 0)
            {
                $out->status = true;
                $out->message = "";
                return $out;
            }
            else 
            {
                $out->status = false;
                $out->message = "Failed to delete";
                return $out;
            }
        }



        public function postMissing($authId, $token, $missing)
        {
            $out = new stdClass();
            $out->status = true;

            $authQuery = "SELECT userprofile.id FROM auth 
            INNER JOIN userprofile ON auth.id = userprofile.authId 
            INNER JOIN session ON auth.id = session.authId 
            INNER JOIN animalprofile ON userprofile.id = animalprofile.userId
            WHERE auth.id = ? AND animalprofile.id = ? AND session.sessionToken = ?";
            $stmt = $this->db->prepare($authQuery);
            $stmt->bind_param("iis", $authId, $missing->animalId, $token);
            $stmt->execute();
            $result = $stmt->get_result();
            error_log("postMissing rows".$result->num_rows);
            if ($result->num_rows != 1)
            {
                $out->status = false;
                $out->message = "Validation query returned 0 or more than 1 user";
                $out->error_message = $stmt->error;
                
                error_log("postMissing-missing -> ".print_r($missing, true));
                error_log("postMissing -> ".$this->db->error);
                return $out;
            }
            $stmt->free_result();
            $stmt->close();
            
            $queryText = "INSERT INTO missing (animalId, userId, Lat, Lng, timeDate, area, description)
            VALUES ( ?, ?, ?, ?, ?, ?, ? );";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("iiddiss", $missing->animalId, $missing->userId, $missing->lat, $missing->lng, $missing->timeDate, $missing->area, $missing->mdesc);
            $success = $stmt->execute();
            if (!$success || $stmt->affected_rows == 0)
            {
                $out->status = false;
                $out->message = "Failed to upload";
                return $out;
            }
            $out->message = "Animal Reported missing";
            return $out;
        }

    }

?>