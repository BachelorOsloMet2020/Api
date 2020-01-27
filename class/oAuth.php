<?php

    class oAuth
    {

        /**
         * $id = oAuth numerical profile id
         */
        public $id;

        /**
         * $email = email
         */
        public $email;

        /**
         * $token = oAuth token
         */
        public $token;

        /**
         * $provider = "FACEBOOK|GOOGLE"
         */
        public $provider;

        /**
         * $app_id = id from app defined on oAuth Service
         */
        public $app_id;

        /**
         * $client_type = type of device or software (Android|Web|iOS)
         */
        public $client_type;

        /**
         * $device_id = id of device | optional
         */
        public $device_id;

        /**
         * Parameters required:
         *  - id
         *  - email
         *  - token
         *  - provider
         *  - client_type
         * 
         * Paramters optional/nullable
         *  - app_id
         *  - device_id
         */
        function __construct($id, $email, $token, $provider, $client_type, $app_id, $device_id)
        {
            $this->id = $id;
            $this->email = $email;
            $this->token = $token;
            $this->provider = $provider;
            $this->client_type = $client_type;
            $this->app_id = $app_id;
            $this->device_id = $device_id;
        }

        function getId()
        {
            return $this->id;
        }
        function getEmail()
        {
            return $this->email;
        }
        function getToken()
        {
            return $this->token;
        }
        function getProvider()
        {
            return $this->provider;
        }
        function getClientType()
        {
            return $this->client_type;
        }
        function getDeviceId()
        {
            return $this->device_id;
        }

    }

?>