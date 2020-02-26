<?php

    class missing
    {
        public function getMissings($data)
        {
            //print_r($data);
            require_once './class/sMissing.php';
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->message = "Data from sql query is not present";
            }
            $array = array();
            foreach ($data->data as $i) 
            {
                $m = new sMissing(
                    $i['missingId'],
                    $i['lat'],
                    $i['lng'],
                    $i['timeDate'],
                    $i['animalId'],
                    $i['name'],
                    $i['image'],
                    $i['animalType'],
                    $i['animalTypeExtras'],
                    $i['color'],
                    $i['area']
                );
                //print_r($m);
                array_push($array, $m);
            }
            $out->data = $array;
            return $out;
        }

        public function getMissing($data)
        {
            require_once './class/fMissing.php';
            $out = new stdClass();
            $out->status = true;
            //print_r($data);
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->message = "Data from sql query is not present";
            }
            //print_r($data);
            $_i = $data->data;
            $m = new fMissing(
                $_i['missingId'],
                $_i['lat'],
                $_i['lng'],
                $_i['timeDate'],
                $_i['animalId'],
                $_i['userId'],
                $_i['name'],
                $_i['image'],
                $_i['animalType'],
                $_i['animalTypeExtras'],
                $_i['sex'],
                $_i['sterilized'],
                $_i['color'],
                $_i['furLength'],
                $_i['furPattern'],
                $_i['description'],
                $_i['area']
            );
            $out->data = $m;
            return $out;
        }

        public function postMissing($data)
        {
            require_once './class/pMissing.php';
            $out = new stdClass();
            $out->status = true;

            $j = json_decode($data);
            $m = new pMissing(
                $j->{'animalId'},
                $j->{'lat'},
                $j->{'lng'},
                $j->{'timeDate'},
                $j->{'area'}
            );
            $out->data = $m;
            return $out;
        }


    }

?>