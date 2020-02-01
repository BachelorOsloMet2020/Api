<?php

    require_once './class/privateProfile.php';
    require_once './class/publicProfile.php';
    require_once './secrets.php';


    require_once './Upload.php';

    class profile
    {

        /**
         * getPrivateProfile
         * Requires only raw data ex: array with profile data
         */
        public function getPrivateProfile($data)
        {
            //print_r($data);
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->message = "Data is not provided";
                return $out;
            }
            $_data = $data->data;
            if (isset($_data['id']) && 
                isset($_data['authId']) && 
                isset($_data['email']) && 
                isset($_data['image']) && 
                isset($_data['firstName']) && 
                isset($_data['lastName']) &&
                isset($_data['address']) &&
                isset($_data['postnumber']) &&
                isset($_data['phone'])
            )
            {
                $profile = new privateProfile(
                    $_data['id'],
                    $_data['authId'],
                    $_data['email'],
                    $_data['image'],
                    $_data['firstName'],
                    $_data['lastName'],
                    $_data['address'],
                    $_data['postnumber'],
                    $_data['phone']
                );
                $out->profile = $profile;
            }
            else
            {
                $out->status = false;
                $out->message = "Values are incomplete";
                $out->error_message = "Data or values are missing in order to complete the requrest for a privateProfile object";
            }
            return $out;
        }

        /**
         * getPublicProfile
         * Requires only raw data ex: array with profile data
         */
        public function getSinglePublicProfile($data)
        {
            //print_r($data);
            $out = new stdClass();
            $out->status = true;
            if (!isset($data) || !isset($data->data))
            {
                $out->status = false;
                $out->message = "Data is not provided";
                return $out;
            }
            $_data = $data->data;
            if (isset($_data['id']) && 
                isset($_data['email']) && 
                isset($_data['image']) && 
                isset($_data['firstName']) && 
                isset($_data['lastName']) &&
                isset($_data['phone'])
            )
            {
                $profile = new publicProfile(
                    $_data['id'],
                    $_data['email'],
                    $_data['image'],
                    $_data['firstName'],
                    $_data['lastName'],
                    $_data['phone']
                );
                $out->profile = $profile;
            }
            else
            {
                $out->status = false;
                $out->message = "Values are incomplete";
                $out->error_message = "Data or values are missing in order to complete the requrest for a publicProfile object";
            }
            return $out;
        }

        /**
         * getPrivateProfile_FromJson
         * $data = raw json string
         */
        public function getPrivateProfile_FromJson($data)
        {
            //print_r($data);
            $out = new stdClass();
            $out->status = true;
            $j = json_decode($data);
            //$token = $j->{'token'};

            $profile = new privateProfile(
                (isset($j->{'id'}) ? $j->{'id'} : null),
                $j->{'authId'},
                $j->{'email'},
                (!isset($j->{'imageType'}) || ($j->{'imageType'} == "url") ? $j->{'image'} : null),
                $j->{'firstName'},
                $j->{'lastName'},
                $j->{'address'},
                $j->{'postnumber'},
                $j->{'phone'}
            );

            if ($profile->image == null && $j->{'imageType'} == "file")
            {
                $imageName = md5($profile->email.$profile->firstName.$profile->lastName);
                $Up = new Upload($imageName, $this);
                $profile->image = $Up->image;
                $out->status = $Up->status;
                if ($Up->status == false)
                {
                    $out->error_message = $Up->message;
                }
                print_r("image is null and file");
            }
            else if ($profile->image == null && $j->{'imageType'} == "base64")
            {
                $imageName = md5($profile->email.$profile->firstName.$profile->lastName);

                $imgPath = "profile/" . $imageName . ".png";
                $fullPath = "../" . __images_dir . $imgPath;
                file_put_contents($fullPath, base64_decode($j->{'image'}));
                $webPath = __host.__images_dir.$imgPath;
                $profile->image = $webPath;
                print_r($webPath);
            }
            $out->profile = $profile;
            return $out;
        }


    }



?>