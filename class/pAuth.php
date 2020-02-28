<?php

    class pAuth
    {
        /**
         * $id = numerical id based on email
         */
        public $id;

        /**
         * $epost = email
         */
        public $email;

        /**
         * $password = to be hashes password
         */
        public $password;

        /**
         * $provider = "DYREBAR"
         */
        public $provider;

        /**
         * $client_type = type of device or software (Android|Web|iOS)
         */
        public $client_type;

        /**
         * $device_Id
         */
        public $device_Id;


        function __construct($id, $email, $password, $provider, $client_type, $device_id)
        {
            $this->id = (isset($id) ? $id : crc32($email));
            $this->email = strtolower($email);
            $this->password = hash('sha256', $password);
            $this->provider = $provider;
            $this->client_type = $client_type;
            $this->device_Id = $device_id;
        }

        function getId()
        {
            return $this->id;
        }
        function getEmail()
        {
            return $this->email;
        }
        function getPassword()
        {
            return $this->password;
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
            return $this->device_Id;
        }


    }
?>