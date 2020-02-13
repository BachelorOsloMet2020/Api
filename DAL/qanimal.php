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

            if (!isset($uid))
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

            $queryText = "INSERT INTO animalprofile (userId, image, idTag, name, animalType, sex, sterilized, color, furLength, furPattern, description)";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("isssiiisiis",
                $animalProfile->userId,
                $animalProfile->image,
                $animalProfile->idTag,
                $animalProfile->name,
                $animalProfile->animalType,
                $animalProfile->sex,
                $animalProfile->sterilized,
                $animalProfile->color,
                $animalProfile->furLength,
                $animalProfile->furPattern,
                $animalProfile->description
            );
            $status = $stmt->execute();
            if ($status == false || $stmt->affected_row == 0)
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

            $queryText = "UPDATE animalprofile
            SET image,
            SET idTag,
            SET name,
            SET animalType,
            SET sex,
            SET sterilized,
            SET color,
            SET furLength,
            SET furPattern,
            SET description";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("sssiiisiis",
                $animalProfile->image,
                $animalProfile->idTag,
                $animalProfile->name,
                $animalProfile->animalType,
                $animalProfile->sex,
                $animalProfile->sterilized,
                $animalProfile->color,
                $animalProfile->furLength,
                $animalProfile->furPattern,
                $animalProfile->description
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