<?php

    class missing
    {
        public function getMissings($data)
        {
            //var_dump($data);
            require_once './class/fMissing.php';
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data) || $data == null)
            {
                $out->status = false;
                $out->err = __err["0x16"];
                return $out;
            }
            $array = array();
            foreach ($data->data as $i) 
            {
                $m = new fMissing(
                    $i['missingId'],
                    $i['lat'],
                    $i['lng'],
                    $i['timeDate'],
                    $i['animalId'],
                    $i['idTag'],
                    $i['userId'],
                    $i['name'],
                    $i['image'],
                    $i['animalType'],
                    $i['animalTypeExtras'],
                    $i['sex'],
                    $i['sterilized'],
                    $i['color'],
                    $i['furLength'],
                    $i['furPattern'],
                    $i['description'],
                    $i['area'],
                    $i['mdesc']
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
                $out->err = __err["0x16"];
            }
            //print_r($data);
            $_i = $data->data;
            $m = new fMissing(
                $_i['missingId'],
                $_i['lat'],
                $_i['lng'],
                $_i['timeDate'],
                $_i['animalId'],
                $_i['idTag'],
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
                $_i['area'],
                $_i['mdesc']
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
                $j->{'userId'},
                $j->{'lat'},
                $j->{'lng'},
                $j->{'timeDate'},
                $j->{'area'},
                $j->{'mdesc'}
            );
            $out->data = $m;
            return $out;
        }


    }

?>