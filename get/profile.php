<?php

    require_once './class/privateProfile.php';
    require_once './class/publicProfile.php';

    class profile
    {
        private $db;
        private $data;
        private $out;
        function __construct($db, $data, $type)
        {
            $this->db = $db;
            $this->out = new stdClass();

            $j = json_decode($data);
            $token = $j->{'token'};
            $uId = $j->{'userId'};

            $profile = null;

            $q = (__query)($this->db,  "SELECT auth.email, profile.*, session.sessionToken FROM userprofile AS profile 
                                        INNER JOIN session ON profile.authId = session.authId
                                        INNER JOIN auth ON auth.id = profile.authId
                                        WHERE profile.id = '".$uId."';");
            if ((__num_rows)($q) == 1)
            {
                $r = (__fetch_assoc)($q);
                $sToken = $r['sessionToken'];
                $this->out->status = true;
                if ($sToken == $token && $type == "myprofile")
                {
                    
                    $profile = new privateProfile(
                        $r['id'],
                        $r['authId'],
                        $r['email'],
                        $r['image'],
                        $r['firstName'],
                        $r['lastName'],
                        $r['address'],
                        $r['postnumber'],
                        $r['phone']
                    );
                }
                else
                {
                    $profile = new publicProfile(
                        $r['id'],
                        $r['authId'],
                        $r['email'],
                        $r['image'],
                        $r['firstName'],
                        $r['lastName'],
                        $r['phone']
                    );
                }
            }
            else
            {
                
                $this->out->message = "No user found";
            }
            $this->out->profile = $profile;
        }

        public function getJson()
        {
            return json_encode($this->out);
        }


    }



?>