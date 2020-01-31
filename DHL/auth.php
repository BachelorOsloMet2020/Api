<?php
    require_once './class/pAuth.php';
    require_once './class/oAuth.php';
    require_once './class/session.php';

    require_once './secrets.php';
    require_once 'vendor/autoload.php';   

    class auth
    {


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
                $out->message = "Challenging Facebook login failed";
                $out->error_message = "Challenge was rejected due to token validity, user or application mismatch";
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
                $out->message = "Challenging Google login failed";
                $out->error_message = "Challenge was rejected due to token validity, user mismatch";
            }
            return $out;
        }

        



    }



?>