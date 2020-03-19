<?php
    require_once './Upload.php';
    class found
    {
        public function getFounds($data)
        {
            require_once './class/cFound.php';
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->err = __err["0x16"];
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
            require_once './class/cFound.php';
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->err = __err["0x16"];
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
            require_once './class/cFound.php';
            return new cFound(
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
                $o['fa_description'],
                (isset($o['fdesc']) ? $o['fdesc'] : null)
            );
        }

        private function get_ap_Found($o)
        {
            require_once './class/cFound.php';
            return new cFound(
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
                $o['ap_description'],
                (isset($o['fdesc']) ? $o['fdesc'] : null)
            );
        }

        public function postFound($data)
        {
            require_once './class/cFound.php';
            $out = new stdClass();
            $out->status = true;
            $j = json_decode($data);
            $f = new cFound(
                null,
                $j->{'lat'},
                $j->{'lng'},
                $j->{'timeDate'},
                $j->{'area'},
                ( (isset($j->{'animalId'}) && $j->{'animalId'} > 0) ? $j->{'animalId'} : null),
                null,
                $j->{'userId'},
                (!isset($j->{'imageType'}) || ($j->{'imageType'} == "url") ? $j->{'image'} : null),
                (isset($j->{'idTag'}) ? $j->{'idTag'} : null),
                (isset($j->{'name'}) ? $j->{'name'} : null),
                $j->{'animalType'},
                (isset($j->{'animalTypeExtras'}) ? $j->{'animalTypeExtras'} : null),
                $j->{'sex'},
                null,
                $j->{'color'},
                $j->{'furLength'},
                $j->{'furPattern'},
                (isset($j->{'description'}) ? $j->{'description'} : null),
                $j->{'fdesc'}
            );

            if ($f->image == null && $j->{'imageType'} == "file")
            {
                // Do file upload
            }
            else if ($f->image == null && $j->{'imageType'} == "base64")
            {
                $imageName = md5($f->userId.$f->color);
                $upload = new Upload($imageName, $this);
                $up = $upload->handleByteStream($j->{'image'});
                if ($up->status == true)
                {
                    $f->image = $up->url;
                }
                else
                    $out->status = false;
            }

            $out->data = $f;
            return $out;
        }



    }



?>