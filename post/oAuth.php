<?php

//https://stackoverflow.com/questions/42065895/how-to-verify-facebook-id-token-received-by-android-app-on-server-side-with-php
//https://developers.google.com/identity/sign-in/android/backend-auth
    require_once './secrets.php';

    require_once 'vendor/autoload.php';
    class oAuth
    {
        private $out;
        function __construct($data)
        {
            $challenge = json_decode($data);
            $this->out = new stdClass();
            $this->out->status = false;

            if (empty($challenge))
            {
                $this->out->mesage = "Challenge was empty";
                return;
            }


            if ($challenge->{'provider'} == "GOOGLE")
            {
                $this->challengeGoogle($challenge);
            }
            else if ($challenge->{'provider'} == "FACEBOOK")
            {
                $this->challengeFacebook($challenge);
            }
            else
            {
                $this->out->message = "Provider: ".$challenge->{'provider'} . ".\r\nIs not supported!";
            }

        }

        private function challengeGoogle($o)
        {
            $client = new Google_Client(['client_id' => __google_client_id]);
            $payload = $client->verifyIdToken($o->{'token'});
            if ($payload)
            {
                $userId = $payload['sub'];
                if ($o->{'id'} == $userId)
                {
                    $this->generateSession($o);
                }
                else
                {
                    $this->out->message = "Challenge rejected!\r\nUser or App mismatch";
                    $this->out->provider = $o->{'provider'};
                }
            }
            else
            {
                //Invalid
                $this->out->message = "Challenge failed";
                $this->out->playload = $payload;
                $this->out->provider = $o->{'provider'};
            }
        }

        private function challengeFacebook($o)
        {
            $client = new \Facebook\Facebook([
                "app_id" => __facebook_id,
                "app_secret" => __facebook_app_secret
            ]);
            $response = null;
            try
            {
                $response = $client->get(
                '/debug_token?input_token='.$o->{'token'},
                __facebook_access_token);

            }
            catch(\Facebook\Exceptions\FacebookResponseException $e) 
            {
                $this->out->graphError = $e->getMessage();
                return;
            }
            catch(\Facebook\Exceptions\FacebookSDKException $e)
            {
                $this->out->graphError = $e->getMessage();
                return;
            }
            $graphNode = $response->getGraphNode();

            $chr = $response->getGraphObject();
            //print_r($graphNode);

            $chr_app_id = $chr->getProperty('app_id');
            $chr_user_id = $chr->getProperty('user_id');
            $chr_is_valid = $chr->getProperty('is_valid');

            if ($chr_is_valid)
            {
                if ($chr_user_id == $o->{'id'} && $chr_app_id == __facebook_id)
                {
                    $this->generateSession($o);
                }
                else
                {
                    $this->out->message = "Challenge rejected!\r\nUser or App mismatch";
                    $this->out->provider = $o->{'provider'};
                }
            }
            else
            {
                $this->out->message = "Challenge failed";
                $this->out->provider = $o->{'provider'};
            }

            /*$this->out->isValid = $chr_is_valid;
            $this->out->app_id = $chr_app_id;
            $this->out->user_id = $chr_user_id;
            $this->out->out->node = $response->getGraphObject();*/
            //$this->out->t1 = $chr->getProperty('application');

            //$this->generateSession();

        }

        private function generateSession($o)
        {
            $this->out->status = true;
            $this->out->message = "Generating session";
            $this->out->provider = $o->{'provider'};
        }


        public function getJson()
        {
            return json_encode($this->out);
        }

    }



?>