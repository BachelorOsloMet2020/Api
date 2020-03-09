<?php
    require_once './class/pAuth.php';
    require_once './class/oAuth.php';
    require_once './class/session.php';

    require_once './secrets.php';
    require_once 'vendor/autoload.php';   

    class auth
    {
        function __construct()
        {
        }

        public function to_oAuthObject($json)
        {
            $j = json_decode($json);
            $auth = new oAuth(
                $j->{'id'}, 
                $j->{'email'},
                $j->{'token'}, 
                $j->{'provider'}, 
                $j->{'client_type'},
                (isset($j->{'app_id'}) ? $j-> {'app_id'} : null), 
                (isset($j->{'duid'}) ? $j->{'duid'} : null)
            );
            return $auth;
        }

        public function to_pAuthObject($json)
        {
            $j = json_decode($json);
            $auth = new pAuth((isset($j->{'id'}) ? $j->{'id'} : null ), $j->{'email'}, $j->{'password'}, $j->{'provider'}, $j->{'client_type'}, (isset($j->{'duid'}) ? $j->{'duid'} : null));
            return $auth;
        }

        public function to_sessionObject($json)
        {
            $j = json_decode($json);
            $session = new session(
                $j->{'id'}, $j->{'session_token'}, $j->{'provider'}
            );
            return $session;
        }
        

        public function challengeFacebook($auth)
        {
            $out = new stdClass();
            $out->status = true;

            $client = new \Facebook\Facebook(["app_id" => __facebook_id, "app_secret" => __facebook_app_secret]);
            $response = null;
            try { $response = $client->get('/debug_token?input_token='.$auth->getToken(), __facebook_access_token); }
            catch(\Facebook\Exceptions\FacebookResponseException $e) { $result->graphError = $e->getMessage(); return; }
            catch(\Facebook\Exceptions\FacebookSDKException $e) { $result->graphError = $e->getMessage(); return; }
            $graph = $response->getGraphObject();

            if ($graph->getProperty('is_valid') && $graph->getProperty('user_id') == $auth->getId() && $graph->getProperty('app_id') == __facebook_id)
            {
                $out->status = true;
                $out->message = "Token challenge completed";
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x10"];
                //$out->message = "Challenging Facebook login failed";
                //$out->error_message = "Challenge was rejected due to token validity, user or application mismatch";
            }
            return $out;
        }

        public function challengeGoogle($auth)
        {
            $out = new stdClass();
            $out->status = true;

            $client = new Google_Client(['client_id' => __google_client_id]);
            $payload = $client->verifyIdToken($auth->getToken());
            if ($payload == true && $payload["sub"] == $auth->getId())
            {
                $out->status = true;
                $out->message = "Token challenge completed";
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x11"];
                //$out->message = "Challenging Google login failed";
                //$out->error_message = "Challenge was rejected due to token validity, user mismatch";
            }
            return $out;
        }

        public function challengePassword($auth, $qa)
        {
            $out = new stdClass();
            if (!isset($auth) || $qa->status == false)
            {
                $out->status = false;  
                $out->err = __err["0x9"];
                //$out->message = "Values are not present";
                //$out->error_message = ((!isset($auth)) ? "Password Authentication object is not defined or empty" : "Query is not completed or faulty");  return $out;
            }
            else
            {
                if ($auth->getPassword() == $qa->data['password'] && $auth->getEmail() == $qa->data['email'])
                {
                    $out->status = true;
                    $out->message = "Credentials is valid";
                }
                else
                {
                    $out->status = false;
                    $out->err = __err["0x9"];
                    //$out->message = "Email or password mismatch";
                }
            }
            return $out;
        }

        public function endSession($aid, $token)
        {
            $out = new stdClass();
            if (isset($token) && isset($aid))
            {
                // Echo missing data
                $out->status = true;
            }
            else
            {
                $out->status = false;
            }
            return $out;
        }
        



    }



?>