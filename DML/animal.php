<?php

    require_once './class/animalProfile.php';
    require_once './Upload.php';

    class animal
    {
        /**
         * $data - Raw data from database query for processing
         */
        public function getAnimalProfiles($data)
        {
            //print_r($data);
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->err = __err["0x16"];
                return $out;
            }
            $array = array();
            foreach ($data->data as $i)
            {
                $ap = new animalProfile(
                    $i['id'],
                    $i['userId'],
                    $i['name'],
                    $i['image'],
                    isset($i['idTag,']) ? $i['idTag,'] : null,
                    $i['animalType'],
                    $i['animalTypeExtras'],
                    $i['sex'],
                    $i['sterilized'],
                    $i['color'],
                    $i['furLength'],
                    $i['furPattern'],
                    isset($i['description']) ? $i['description'] : null
                );
                array_push($array, $ap);
            }
            $out->data = $array;
            return $out;
        }



        /**
         * $data - Data from post request to be processed
         */
        public function postAnimalProfile($data)
        {
            $out = new stdClass();
            $out->status = true;

            $j = json_decode($data);
            $animal = new animalProfile(
                (isset($j->{'id'}) ? $j->{'id'} : null),
                $j->{'userId'},
                $j->{'name'},
                (!isset($j->{'imageType'}) || ($j->{'imageType'} == "url") ? $j->{'image'} : null),
                $j->{'idTag'},
                $j->{'animalType'},
                $j->{'extras'},
                $j->{'sex'},
                $j->{'sterilized'},
                $j->{'color'},
                $j->{'furLength'},
                $j->{'furPattern'},
                $j->{'description'}
            );

            if ($animal->image == null && $j->{'imageType'} == "file")
            {
                // Do file upload
            }
            else if ($animal->image == null && $j->{'imageType'} == "base64")
            {
                $imageName = md5($animal->userId.$animal->name);
                $upload = new Upload($imageName, $this);
                $up = $upload->handleByteStream($j->{'image'});
                if ($up->status == true)
                {
                    $animal->image = $up->url;
                }
                else
                    $out->status = false;
            }
            $out->animal = $animal;
            //error_log("Temporary out; " . print_r($out, true));
            return $out;
        }
    }

?>