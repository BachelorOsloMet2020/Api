<?php
    require_once './class/animalProfile.php';

    class qanimal
    {
        private $db;
        function __construct($db)
        {
            $this->db = $db;
        }

        public function getAnimalsByUid($uid)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT * FROM animalprofile WHERE userId = ?;";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("i", $uid);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows >= 1)
            {
                $out->data = $result->fetch_all(MYSQLI_ASSOC);
            }
            else
            {
                $out->status = false;
            }
            

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            return $out;
        }

        public function getAnimalByUid($uid, $animalId)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT * FROM animalprofile WHERE userId = ? AND id = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("ii", $uid, $animalId);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows == 1)
            {
                $out->data = $result;
            }
            else
            {
                $out->status = false;
            }
            

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            return $out;
        }



        /** Posts */

        public function postAnimalProfile($token, $animalProfile)
        {
            $out = new stdClass();
            $out->status = true;

            if (!isset($animalProfile->userId))
            {
                $out->status = false;
                $out->message = "User id not present";
                return $out;
            }

            if (!isset($animalProfile->id) || $animalProfile->id == null)
            {
                return $this->postNewAnimalProfile($animalProfile);
            }
            else
            {
                return $this->postUpdateAnimalProfile($animalProfile);
            }
        }

        private function postNewAnimalProfile($animalProfile)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "INSERT INTO animalprofile (userId, image, idTag, name, animalType, animalTypeExtras, sex, sterilized, color, furLength, furPattern, description)
                                             VALUES (     ?,     ?,     ?,    ?,          ?,                ?,   ?,           ?,    ?,         ?,          ?,           ?)";
            $stmt = $this->db->prepare($queryText);
            if (isset($this->db->error))
                error_log("Database error: ". $this->db->error. " ########################");
            //error_log("Stmt Error1:". $stmt->error);
            $stmt->bind_param("isssisiisiis",
                $animalProfile->userId,
                $animalProfile->image,
                $animalProfile->idTag,
                $animalProfile->name,
                $animalProfile->animalType,
                $animalProfile->animalTypeExtras,
                $animalProfile->sex,
                $animalProfile->sterilized,
                $animalProfile->color,
                $animalProfile->furLength,
                $animalProfile->furPattern,
                $animalProfile->description
            );
            $status = $stmt->execute();
            /*error_log("Database error: ". $this->db->error. "\r\n");
            error_log("Stmt Error:". $stmt->error);*/
            
            if ($status == false || $stmt->affected_rows == 0)
            {
                $out->status = false;
                $out->error_message = $stmt->error;
            }
            return $out;
        }

        private function postUpdateAnimalProfile($animalProfile)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "UPDATE animalprofile SET 
            image = ?,
            idTag = ?,
            name = ?,
            animalType = ?,
            animalTypeExtras = ?,
            sex = ?,
            sterilized = ?,
            color = ?,
            furLength = ?,
            furPattern = ?,
            description = ?
            WHERE id = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("sssisiisiisi",
                $animalProfile->image,
                $animalProfile->idTag,
                $animalProfile->name,
                $animalProfile->animalType,
                $animalProfile->extras,
                $animalProfile->sex,
                $animalProfile->sterilized,
                $animalProfile->color,
                $animalProfile->furLength,
                $animalProfile->furPattern,
                $animalProfile->description,
                $animalProfile->id
            );
            $status = $stmt->execute();
            if ($status == false || $stmt->affected_row == 0)
            {
                $out->status = false;
                $out->error_message = $stmt->error;
            }
            return $out;
        }




    }

?>