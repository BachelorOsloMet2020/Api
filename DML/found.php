<?php

    class found
    {
        public function getFounds($data)
        {
            require_once './class/fFound.php';
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->message = "Data from sql query is not present";
            }
            $array = array();
            foreach($data->data as $i)
            {
                $obj = null;
                if (isset($i['animalId']) || $i['animalId'] !== null)
                    $obj = $this->get_ap_Found($i);
                else if (isset($i['foundAnimalId']) || $i['foundAnimalId'] !== null)
                    $obj = $this->get_fa_Found($i);
                array_push($array, $obj);
            }
            $out->data = $array;
            return $out;
        }

        public function getFound($data)
        {
            require_once './class/fFound.php';
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->message = "Data from sql query is not present";
            }
            $i = $data->data;
            if (isset($i['animalId']) || $i['animalId'] !== null)
                $out->data = $this->get_ap_Found($i);
            else if (isset($i['foundAnimalId']) || $i['foundAnimalId'] !== null)
                $out->data = $this->get_fa_Found($i);
            return $out;
        }




        private function get_fa_Found($o)
        {
            require_once './class/fFound.php';
            return new fFound(
                $o['foundId'],
                $o['Lat'],
                $o['Lng'],
                $o['timeDate'],
                $o['area'],
                null,
                $o['fa_id'],
                $o['userId'],
                $o['fa_image'],
                null,
                null,
                $o['fa_animalType'],
                $o['fa_animalTypeExtras'],
                $o['fa_sex'],
                2,
                $o['fa_color'],
                $o['fa_furLength'],
                $o['fa_furPattern'],
                $o['fa_description']
            );
        }

        private function get_ap_Found($o)
        {
            require_once './class/fFound.php';
            return new fFound(
                $o['foundId'],
                $o['Lat'],
                $o['Lng'],
                $o['timeDate'],
                $o['area'],

                $o['ap_id'],
                null,
                $o['ap_userId'],
                $o['ap_image'],
                $o['ap_idTag'],
                $o['ap_name'],
                $o['ap_animalType'],
                $o['ap_animalTypeExtras'],
                $o['ap_sex'],
                $o['ap_sterilized'],
                $o['ap_color'],
                $o['ap_furLength'],
                $o['ap_furPattern'],
                $o['ap_description']
            );
        }



    }



?>