<?php

    class session
    {
        private $db;
        function __construct($db, $data)
        {
            $this->db = $db;
        }




        public function hasProviderSession($id, $token, $clientType, $deviceId)
        {
            $session = new stdClass();
            $session->status = false;
            $q_id = (__query)($this->db, "SELECT DISTINCT * FROM auth WHERE oAuthId = '$id';");
            $authId = null;
            if ((__num_rows)($q_id) == 1)
            {
                $authId = (__fetch_assoc)($q_id)['id'];
                $res = (__query)($this->db, "SELECT * FROM session WHERE authId = '$authId'");
                
                while ($r = (__fetch_array)($res))
                {
                    if ($r['oAuthToken'] == $token && $r['clientType'] == $clientType && $r['duId'] == $deviceId)
                    {
                        $session->status = true;
                        $session->session_token = $r['sessionToken'];
                    }
                }
            }

            return $session;
        }

        /**
         * $id = User id for Facebook or Google
         * $o = object that contains:
         *      - token
         *      - timeDate
         *      - clientTye
         *      - provider
         *      - deviceId
         */
        public function createProviderSession($id, $o)
        {
            $session = new stdClass();
            $session->status = false;
            $db_has_authId = $this->hasAuthId($id);
            $session->hasAuthId = $db_has_authId;
            if ($db_has_authId)
            {
                $sToken = md5(uniqid($id, true));
                $time = (new DateTime())->getTimestamp();


                $q_id = (__query)($this->db, "SELECT DISTINCT * FROM auth WHERE oAuthId = '$id';");
                $session->db_error .= "\r\n" . mysqli_error($this->db);
                $authId = (__fetch_assoc)($q_id)['id'];

                $in_q = (!empty($o->deviceId)) ? 
                "INSERT INTO session  (authId, oAuthToken, sessionToken, timeDate, clientType, provider, duId) 
                VALUES ('$authId', '$o->token', '$sToken', '$time', '$o->clientType', '$o->provider', '$o->deviceId');" : 
                "INSERT INTO session  (authId, oAuthToken, sessionToken, timeDate, clientType, provider) 
                VALUES ('$authId', '$o->token', '$sToken', '$time', '$o->clientType', '$o->provider');
                ";
                $g_sess = (__query)($this->db, $in_q);
                $session->inq = $in_q;
                $session->db_error .= "\r\n" . mysqli_error($this->db);
                if ($g_sess == true)
                {
                    $session->status = true;
                    $session->session_token = $sToken;
                    return $session;
                }
                else
                {
                    return $session;
                }
            
            }
            else
            {
                // Something went really wrong
                $session->message = "Could'n create AuthId";
                return $session;
            }

        }

        public function hasAuthId($id)
        {
            $q_id = (__query)($this->db, "SELECT DISTINCT * FROM auth WHERE oAuthId = '$id';");
            if ((__num_rows)($q_id) != 1)
            {
                $in_id = (__query)($this->db, "INSERT INTO auth (oAuthId) VALUES ('$id');");
                return ($in_id == true) ? true:false;
            }
            return ($q_id == true) ? true:false;

        }



    }

?>