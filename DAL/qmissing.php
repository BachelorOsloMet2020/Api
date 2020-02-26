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

            $queryText = "SELECT missing.id AS missingId, missing.lat, missing.lng, missing.timeDate,
            profile.id AS animalId, userId, image, idTag, name, animalType, animalTypeExtras, sex, sterilized, color, furLength, furPattern, description, area
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
                $out->data = $result->fetch_assoc();;
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
            profile.id AS animalId, image, name, animalType, animalTypeExtras, color, area
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
            
            $queryText = "INSERT INTO missing (animalId, Lat, Lng, timeDate, area)
            VALUES ( ?, ?, ?, ?, ? );";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("iddis", $missing->animalId, $missing->lat, $missing->lng, $missing->timeDate, $missing->area);
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