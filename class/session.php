<?php

    class session
    {
        /**
         * $id = numerical profile id
         */
        public $id;

        /**
         * $token = session_token
         */
        public $token;

        /**
         * $provider = GOOGLE|FACEBOOK|DYREBAR|WEB
         */
        public $provider;

        function __construct($id, $token, $provider)
        {
            $this->id = $id;
            $this->token = $token;
            $this->provider = $provider;
        }

        public function getId()
        {
            return $this->id;
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