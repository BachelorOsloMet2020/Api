<?php

    class privateProfile
    {
        public $id;
        public $authId;
        public $email;
        public $image;
        public $firstName;
        public $lastName;
        public $address;
        public $postNumber;
        public $phoneNumber;

        /**
         * 
         */
        function __construct($id, $authId, $email, $image, $firstName, $lastName, $address, $postNumber, $phoneNumber)
        {
            $this->id = $id;
            $this->authId = $authId;
            $this->email = $email;
            $this->image = $image;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->address = $address;
            $this->postNumber = $postNumber;
            $this->phoneNumber = $phoneNumber;
        }
    }

?>