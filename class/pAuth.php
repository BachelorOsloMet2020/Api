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
         * $device_Id
         */
        public $device_Id;


        function __construct($id, $email, $password, $provider, $device_id)
        {
            $this->id = (isset($id) ? $id : crc32($email));
            $this->email = $email;
            $this->password = hash('sha256', $password);
            $this->provider = $provider;
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


    }
?>