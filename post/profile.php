<?php
    require_once './class/privateProfile.php';
    require_once './Upload.php';

    class profile
    {
        private $db;
        private $out;
        function __construct($db, $data)
        {
            $this->db = $db;
            $this->out = new stdClass();

            $j = json_decode($data);
            $token = $j->{'token'};
            $profile = null;
            if ($j->{'imageType'} == "url")
            {
                $profile = new privateProfile(
                    (isset($j->{'id'}) ? $j->{'id'} : null),
                    $j->{'authId'},
                    $j->{'email'},
                    $j->{'image'},
                    $j->{'firstName'},
                    $j->{'lastName'},
                    $j->{'address'},
                    $j->{'postnumber'},
                    $j->{'phone'}
                );
            }
            else
            {
                $profile = new privateProfile(
                    (isset($j->{'id'}) ? $j->{'id'} : null),
                    $j->{'authId'},
                    $j->{'email'},
                    null,
                    $j->{'firstName'},
                    $j->{'lastName'},
                    $j->{'address'},
                    $j->{'postnumber'},
                    $j->{'phone'}
                );
                
                $imageName = md5($profile->id().$token);
                $Up = new Upload($imageName, $this);
                $profile->image = $Up->image;
            }

            $qt = null;
            if ($profile->id == null)
            {
                $qt = "INSERT INTO userprofile (authId, firstName, lastName, email, image, address, postnumber, phone) VALUES
                ( '".$profile->authId."', '".$profile->firstName."', '".$profile->lastName."', 
                '".$profile->email."', '".$profile->image."', '".$profile->address."', '".$profile->postnumber."',   '".$profile->phone."')";
            }
            else
            {
                $qt = "UPDATE userprofile 
                SET firstName = '".$profile->firstName."',
                SET lastName  = '".$profile->lastName."',
                SET email = '".$profile->email."',
                SET image = '".$profile->image."',
                SET address = '".$profile->address."',
                SET postnumber = '".$profile->postnumber."',
                SET phone = '".$profile->phoneNumber."'
                WHERE id = '".$profile->id."'; ";
            }
            if ($qt != null)
            {
                $q = (__query)($db, $qt);
                if ($q == true && $Up->status == true)
                {
                    $this->out->status = true;
                    $this->out->message = "Uploaded";
                }
                else if ($q == false)
                {
                    $this->out->status = false;
                    $this->out->message = "Query failed";
                    $this->out->sqlError = mysqli_error($db);
                }
                else
                {
                    $this->out->status = false;
                    $this->out->message = $Up->message;
                }
            }
            else
            {
                $this->out->status = false;
                $this->out->message = "Data incomplete";
            }




        }

        public function getJson()
        {
            return json_encode($this->out);
        }

    }



?>