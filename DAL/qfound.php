<?php

    class qfound
    {
        private $db;
        function __construct($db)
        {
            $this->db = $db;
        }

        public function getMyFound($uid)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT
            found.id AS foundId,
            found.animalId,
            found.foundAnimalId,
            found.userId,
            found.Lat,
            found.Lng,
            found.timeDate,
            found.area,

            fa.id AS fa_id,
            fa.image AS fa_image,
            fa.animalType AS fa_animalType,
            fa.animalTypeExtras AS fa_animalTypeExtras,
            fa.sex AS fa_sex,
            fa.color AS fa_color,
            fa.furLength AS fa_furLength,
            fa.furPattern AS fa_furPattern,
            fa.description AS fa_description,
            
            ap.id AS ap_id,
            ap.userId AS ap_userId,
            ap.image AS ap_image,
            ap.idTag AS ap_idTag,
            ap.name AS ap_name,
            ap.animalType as ap_animalType,
            ap.animalTypeExtras as ap_animalTypeExtras,
            ap.sex AS ap_sex,
            ap.sterilized AS ap_sterilized,
            ap.color AS ap_color,
            ap.furLength AS ap_furLength,
            ap.furPattern AS ap_furPattern,
            ap.description AS ap_description

            FROM found LEFT JOIN foundAnimal AS fa ON (found.foundAnimalId IS NOT NULL AND found.foundAnimalId = fa.id) LEFT JOIN animalprofile AS ap ON (found.animalId IS NOT NULL AND found.animalId = ap.id) WHERE found.userId = ?";
            $stmt = $this->db->prepare($queryText);
            error_log("getMyFoundDb -> ".$this->db->error);
            $stmt->bind_param("i", $uid);
            $ex = $stmt->execute();
            error_log("getMyFoundStmt -> ".$stmt->error);
            

            $result = $stmt->get_result();
            if (false === $ex || false == $stmt)
            {
                $out->status = false;
                $out->err = __err["0x23"];
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

        public function getFound()
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT
            found.id AS foundId,
            found.animalId,
            found.foundAnimalId,
            found.userId,
            found.Lat,
            found.Lng,
            found.timeDate,
            found.area,

            fa.id AS fa_id,
            fa.image AS fa_image,
            fa.animalType AS fa_animalType,
            fa.animalTypeExtras AS fa_animalTypeExtras,
            fa.sex AS fa_sex,
            fa.color AS fa_color,
            fa.furLength AS fa_furLength,
            fa.furPattern AS fa_furPattern,
            fa.description AS fa_description,
            
            ap.id AS ap_id,
            ap.userId AS ap_userId,
            ap.image AS ap_image,
            ap.idTag AS ap_idTag,
            ap.name AS ap_name,
            ap.animalType as ap_animalType,
            ap.animalTypeExtras as ap_animalTypeExtras,
            ap.sex AS ap_sex,
            ap.sterilized AS ap_sterilized,
            ap.color AS ap_color,
            ap.furLength AS ap_furLength,
            ap.furPattern AS ap_furPattern,
            ap.description AS ap_description

            FROM found LEFT JOIN foundAnimal AS fa ON (found.foundAnimalId IS NOT NULL AND found.foundAnimalId = fa.id) LEFT JOIN animalprofile AS ap ON (found.animalId IS NOT NULL AND found.animalId = ap.id)";
            $query = $this->db->query($queryText);
            //error_log("getMyFoundDb -> ".$this->db->error);
            //error_log("getMyFoundStmt -> ".$stmt->error);
            
            if (false === $query)
            {
                $out->status = false;
                $out->err = __err["0x17"];
            }
            else
            {
                $out->data = $query->fetch_all(MYSQLI_ASSOC);
            }

            
            return $out;
        }

        public function getFoundById($id)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT
            found.id AS foundId,
            found.animalId,
            found.foundAnimalId,
            found.userId,
            found.Lat,
            found.Lng,
            found.timeDate,
            found.area,
            found.description AS fdesc,

            fa.id AS fa_id,
            fa.image AS fa_image,
            fa.animalType AS fa_animalType,
            fa.animalTypeExtras AS fa_animalTypeExtras,
            fa.sex AS fa_sex,
            fa.color AS fa_color,
            fa.furLength AS fa_furLength,
            fa.furPattern AS fa_furPattern,
            fa.description AS fa_description,
            
            ap.id AS ap_id,
            ap.userId AS ap_userId,
            ap.image AS ap_image,
            ap.idTag AS ap_idTag,
            ap.name AS ap_name,
            ap.animalType as ap_animalType,
            ap.animalTypeExtras as ap_animalTypeExtras,
            ap.sex AS ap_sex,
            ap.sterilized AS ap_sterilized,
            ap.color AS ap_color,
            ap.furLength AS ap_furLength,
            ap.furPattern AS ap_furPattern,
            ap.description AS ap_description

            FROM found LEFT JOIN foundAnimal AS fa ON (found.foundAnimalId IS NOT NULL AND found.foundAnimalId = fa.id) LEFT JOIN animalprofile AS ap ON (found.animalId IS NOT NULL AND found.animalId = ap.id) WHERE found.id = ?";
            $stmt = $this->db->prepare($queryText);
            error_log("getFoundById -> ".$this->db->error);
            $stmt->bind_param("i", $id);
            $ex = $stmt->execute();
            error_log("getFoundById -> ".$stmt->error);
            

            $result = $stmt->get_result();
            if (false === $ex || false == $stmt)
            {
                $out->status = false;
                $out->err = __err["0x24"];
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



        public function deleteFound($authId, $token, $userId, $foundId)
        {
            $out = new stdClass();
            $out->status = false;

            $queryText = "SELECT auth.email, profile.* FROM userprofile AS profile 
            INNER JOIN session ON profile.authId = session.authId
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.id = ? AND profile.authId = ? AND session.sessionToken = ?;";

            $stmt =  $this->db->prepare($queryText);
            $stmt->bind_param("iis", $userId, $authId, $token);
            $stmt->execute();

            $result = $stmt->get_result();

            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            if ($result->num_rows != 1)
            {
                return $out;
            }

            $queryText = "DELETE FROM found WHERE id = ? AND userId = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("ii", $foundId, $userId);
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
                $out->err = __err["0x25"];
                //$out->message = "Failed to delete";
                return $out;
            }
        }

        public function postFound($authId, $token, $found)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.email, profile.* FROM userprofile AS profile 
            INNER JOIN session ON profile.authId = session.authId
            INNER JOIN auth ON auth.id = profile.authId
            WHERE profile.authId = ? AND session.sessionToken = ?;";

            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("is", $authId, $token);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows != 1)
            {
                $out->status = false;
                $out->err = __err["0x13"];
            }
            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            if ($out->status == false)
            {
                $out->err = __err["0x26"];
                return $out;
            }

            $data = null;
            if ($found->ai == null)
                $out = $this->postFoundWithAnimal($found);

            $out = $this->postOnlyFound($found);
                
            return $out;
        }

        private function postFoundWithAnimal(&$found)
        {
            $out = new stdClass();
            $out->status = true;

            $query = "INSERT INTO foundAnimal (name, image, animalType, animalTypeExtras, sex, color, furLength, furPattern, description)
                                    VALUES    (  ?,     ?,          ?,              ?,      ?,      ?,      ?,          ?)";
            $stmt = $this->db->prepare($query);
            $stmt->bind_param('ssisisiis', $found->name, $found->image, $found->animalType, $found->animalTypeExtras, $found->sex, $found->color, $found->furLength, $found->furPattern, $found->description);
            $stmt->execute();
            $newId = $stmt->insert_id;
            $found->fi = $newId;

            if (!$success || $stmt->affected_rows == 0)
            {
                $out->status = false;
                $out->err = __err["0x26"];
                //$out->message = "Failed to upload";
            }

            return $out;

        }

        private function postOnlyFound(&$found)
        {
            $out = new stdClass();
            $out->status = true;

            $fa = "INSERT INTO found (animalId, foundAnimalId, userId, Lat, Lng, timeDate, area, description)
                            VALUES (     ?,           ?,         ?,    ?,   ?,       ?,     ?,       ?)";
            $stmt = $this->db->prepare($fa);
            $stmt->bind_param("iiiddiss", $found->ai, $found->fi, $found->userId, $found->lat, $found->lng, $found->timeDate, $found->area, $found->fdesc);
            $success = $stmt->execute();
            
            if (!$success || $stmt->affected_rows == 0)
            {
                $out->status = false;
                $out->err = __err["0x26"];
                //$out->message = "Failed to upload";
                
            }


            /** Cleaning up */
            $stmt->free_result();
            $stmt->close();

            return $out;
        }
        

    }

?>