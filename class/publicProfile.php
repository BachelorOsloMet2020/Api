<?php

    class publicProfile
    {
        public $id;
        public $email;
        public $image;
        public $firstName;
        public $lastName;
        public $phoneNumber;

        /**
         * 
         */
        function __construct($id, $authId, $email, $image, $firstName, $lastName, $phoneNumber)
        {
            $this->id = $id;
            $this->authId = $authId;
            $this->email = $email;
            $this->image = $image;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->phoneNumber = $phoneNumber;
        }
    }



?>