<?php
    require_once './class/pAuth.php';
    require_once './class/oAuth.php';
    require_once './class/session.php';

    require_once './secrets.php';
    require_once 'vendor/autoload.php';
    class Auth
    {
        private $db;
        private $auth;
        function __construct($db, $data)
        {
            $this->db = $db;
            $j = json_decode($data);
            $type = $j->{'action'};
            //print_r($j);
            if ($type == "CHALLENGE")
            {
                $provider = $j->{'provider'};
                if ($provider == "DYREBAR" || $provider == "WEB")
                {
                    $this->auth = new pAuth($j->{'id'}, $j->{'email'}, $j->{'password'}, $j->{'provider'}, (isset($j->{'duid'}) ? $j->{'duid'} : null));
                }
                else
                {
                    $this->auth = new oAuth(
                        $j->{'id'}, 
                        $j->{'email'},
                        $j->{'token'}, 
                        $j->{'provider'}, 
                        $j->{'client_type'},
                        (isset($j->{'app_id'}) ? $j-> {'app_id'} : null), 
                        (isset($j->{'duid'}) ? $j->{'duid'} : null)
                    );
                }
            }
            else
            {
                // Validate
                $this->auth = new session($j->{'id'}, $j->{'session_token'}, $j->{'provider'});
            }
        }

        public function challenge()
        {
            $result = new stdClass();
            $result->status = false;
            if ($this->auth->provider == "GOOGLE")
            {
                $result = $this->challenge_Google();
            }
            else if ($this->auth->provider == "FACEBOOK")
            {
                $result = $this->challenge_Facebook();
            }


            if (!$result->status)
            {
                return $result;
            }
            $hasAuthId = $this->hasAuthId();
            if (!$hasAuthId)
            {
                $out = new stdClass();
                $out->status = false;
                $out->message = "Could not create a authId";
                return $out;
            }
            $authId = $this->getAuthId()->authId;
            $result = $this->create_session($authId);
            return $result;
        }

        private function challenge_Google()
        {
            $result = new stdClass();
            $result->status = false;
            $client = new Google_Client(['client_id' => __google_client_id]);
            $payload = $client->verifyIdToken($this->auth->getToken());
            if ($payload)
            {
                $userId = $payload['sub'];
                if ($this->auth->getId() == $userId) 
                { 
                    $result->status = true;
                }
                else {  $result->message = "Challenge rejected!\r\nUser or App mismatch"; }
            }
            else
            {
                $result->message = "Challenge failed";
                $result->playload = $payload;
                $result->provider = $o->{'provider'};
            }
            return $result;
        }

        private function challenge_Facebook()
        {
            $result = new stdClass();
            $result->status = false;
            $client = new \Facebook\Facebook(["app_id" => __facebook_id, "app_secret" => __facebook_app_secret]);
            $response = null;
            try { $response = $client->get('/debug_token?input_token='.$this->auth->getToken(), __facebook_access_token); }
            catch(\Facebook\Exceptions\FacebookResponseException $e) { $result->graphError = $e->getMessage(); return; }
            catch(\Facebook\Exceptions\FacebookSDKException $e) { $result->graphError = $e->getMessage(); return; }
            $graph = $response->getGraphObject();

            if ($graph->getProperty('is_valid'))
            {
                if ($graph->getProperty('user_id') == $this->auth->getId() && $graph->getProperty('app_id') == __facebook_id)
                {
                    $result->status = true;
                }
                else
                {
                    $result->message = "Challenge rejected!\r\nUser or App mismatch";
                }
            }
            else
            {
                $result->message = "Challenge failed";
            }
            return $result;
        }


        public function dyrebar_sign_in()
        {
            $default = new stdClass();
            $default->status = false;
            $default->message = "Could not find user or password did not match";
            $q_id = (__query)($this->db, "SELECT * FROM auth WHERE oAuthId = '".$this->auth->getId()."'");
            if ((__num_rows)($q_id) == 1)
            {
                // Found user with matching id.
                $pw = (__fetch_assoc)($q_id)['password'];
                if ($pw == $this->auth->getPassword())
                {
                    $result = $this->has_pSession($this->auth->getId());
                    if (!$result->status)
                    {
                        $sessionResult = $this->create_session((__fetch_assoc)($q_id)['id']);
                        return $sessionResult;
                    }
                    else
                    {
                        return $result;
                    }
                }
                else
                {
                    return $default;
                }
            }
            else
            {
                return $default;
            }

        }



        private function hasAuthId()
        {
            $q_id = (__query)($this->db, "SELECT DISTINCT * FROM auth WHERE oAuthId = '".$this->auth->id."';");
            print_r(mysqli_error($this->db));
            if ((__num_rows)($q_id) != 1)
            {
                $in_id = (__query)($this->db, "INSERT INTO auth (oAuthId, email, provider) VALUES ('".$this->auth->id."', '".$this->auth->email."', '".$this->auth->provider."') ON DUPLICATE KEY UPDATE oAuthId = '".$this->auth->id."', provider =  '".$this->auth->provider."';");
                print_r(mysqli_error($this->db));
                return ($in_id == true) ? true:false;
            }
            return ($q_id == true) ? true:false;
        }

        private function getAuthId()
        {
            $result = new stdClass();
            $result->status = false;
            $q_id = (__query)($this->db, "SELECT DISTINCT * FROM auth WHERE oAuthId = '".$this->auth->id."';");
            if ((__num_rows)($q_id) == 1)
            {
                $result->status = true;
                $result->authId = (__fetch_assoc)($q_id)['id'];
            }
            return $result;
        }

        private function has_oSession($authId)
        {
            $result = new stdClass();
            $result->status = false;
            $res = (__query)($this->db, "SELECT auth.oAuthId, session.* FROM auth INNER JOIN session ON auth.id = session.authId WHERE auth.oAuthId = '$authId'");
                
            while ($r = (__fetch_array)($res))
            {
                $rec = new stdClass();
                $rec->authId = $r['authId'];
                $rec->token = (isset($r['oAuthToken']) ? $r['oAuthToken'] : null);
                $rec->session_token = $r['sessionToken'];
                $rec->time_date = $r['timeDate'];
                $rec->client_type = $r['clientType'];
                $rec->provider = $r['provider'];
                $rec->duid = (isset($r['duid']) ? $r['duid'] : null);

                if ($authId == $rec->authId && $this->auth->getToken() == $rec->session_token && $this->auth->getDeviceId() == $rec->duid)
                {
                    $result->status = true;
                    $result->session_token = $rec->session_token;
                    $result->time = $rec->time_date;
                    $result->id = $this->auth->getId();
                    $result->provider = $this->auth->provider;
                    break;
                }
            }
            return $result;
        }

        private function has_pSession($authId)
        {
            $result = new stdClass();
            $result->status = false;
            $res = (__query)($this->db, "SELECT auth.oAuthId, auth.email, auth.password, session.* FROM auth INNER JOIN session ON auth.id = session.authId WHERE auth.oAuthId = '$authId'");
                
            while ($r = (__fetch_array)($res))
            {
                $rec = new stdClass();
                $rec->authId = $r['authId'];
                $rec->email =  $r['email'];
                $rec->password = $r['password'];
                $rec->token = (isset($r['oAuthToken']) ? $r['oAuthToken'] : null);
                $rec->session_token = $r['sessionToken'];
                $rec->time_date = $r['timeDate'];
                $rec->client_type = $r['clientType'];
                $rec->provider = $r['provider'];
                $rec->duid = (isset($r['duid']) ? $r['duid'] : null);

                if ($authId == $rec->authId && $this->auth->getEmail() == $rec->email && $this->auth->getPassword() == $rec->password && $this->auth->getDeviceId() == $rec->duid)
                {
                    $result->status = true;
                    $result->session_token = $rec->session_token;
                    $result->time = $rec->time_date;
                    $result->id = $this->auth->getId();
                    $result->provider = $this->auth->provider;
                    break;
                }
            }
            return $result;
        }

        public function is_session_valid()
        {
            $result = new stdClass();
            $result->status = false;
            $result->isValid = false;
            $authId = $this->getAuthId()->authId;
            //$this->auth->getId()

            if ($this->auth instanceof session)
            {
                $res = (__query)($this->db, "SELECT auth.oAuthId, session.sessionToken, auth.provider FROM auth INNER JOIN session ON auth.id = session.authId WHERE auth.id = '".$authId."' AND session.sessionToken = '".$this->auth->getToken()."';");
                $result->status = true;
                while ($r = (__fetch_array)($res))
                {
                    $rec = new stdClass();
                    $rec->oAuthId = $r['oAuthId'];
                    $rec->token = (isset($r['sessionToken']) ? $r['sessionToken'] : null);
                    $rec->provider = $r['provider'];

                    //print_r($rec);
                    if ($rec->token != null && $this->auth->getId() == $rec->oAuthId && $this->auth->getToken() == $rec->token && $this->auth->getProvider() == $rec->provider)
                    {
                        
                        $result->isValid = true;
                        break;
                    }
                }
            }
            
            return $result;
        }

        private function create_session($authId)
        {
            $result = new stdClass();
            $result->status = false;

            $session_token = md5(uniqid($this->auth->getId()));
            $time = (new DateTime())->getTimestamp();

            $session_query = "";

            if ($this->auth instanceof pAuth)
            {
                $session_query = (!empty($this->auth->getDeviceId())) ? 
                "REPLACE INTO session  (authId, sessionToken, timeDate, clientType, provider, duId) 
                VALUES ('$authId', '".$session_token."', '".$time."', '".$this->auth->getClientType()."', '".$this->auth->getProvider()."', '".$this->auth->getDeviceId()."');" : 
                "INSERT INTO session  (authId, sessionToken, timeDate, clientType, provider) 
                VALUES ('$authId', '".$session_token."', '".$time."', '".$this->auth->getClientType()."', '".$this->auth->getProvider()."');
                ";
            }
            else if ($this->auth instanceof oAuth)
            {
                $session_query = (!empty($this->auth->getDeviceId())) ? 
                "REPLACE INTO session  (authId, oAuthToken, sessionToken, timeDate, clientType, provider, duId) 
                VALUES (
                    '$authId', 
                    '".$this->auth->getToken()."', 
                    '".$session_token."', 
                    '".$time."', 
                    '".$this->auth->getClientType()."', 
                    '".$this->auth->getProvider()."', 
                    '".$this->auth->getDeviceId()."'
                    );" : 
                "INSERT INTO session  (authId, oAuthToken, sessionToken, timeDate, clientType, provider) 
                VALUES 
                ('$authId', 
                '".$this->auth->getToken()."', 
                '".$session_token."', 
                '".$time."', 
                '".$this->auth->getClientType()."', 
                '".$this->auth->getProvider()."'
                );";
            }

            $q_res = (__query)($this->db, $session_query);
            if ($q_res)
            {
                $result->status = true;
                $result->id = $this->auth->getId();
                $result->session_token = $session_token;
                $result->time = $time;
                $result->provider = $this->auth->provider;
            }

            //$g_sess = (__query)($this->db, $in_q);


            return $result;
        }





    }

?>