<?php

    class session
    {
        /**
         * $id = numerical profile id
         */
        public $id;

        /**
         * $authId = numerical auth id
         */
        public $authId;


        /**
         * $token = session_token
         */
        public $token;

        /**
         *  $time = long of when token was created
         */
        public $time;


        /**
         * $provider = GOOGLE|FACEBOOK|DYREBAR|WEB
         */
        public $provider;

        function __construct($id, $authId, $token, $provider, $time)
        {
            $this->id = $id;
            $this->token = $token;
            $this->provider = $provider;
        }

        public function getId()
        {
            return $this->id;
        }

        public function getAuthId()
        {
            return $this->authId;
        }

        public function getToken()
        {
            return $this->token;
        }

        public function getProvider()
        {
            return $this->provider;
        }


    }


?>