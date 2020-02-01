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
        function __construct($id, $email, $image, $firstName, $lastName, $phoneNumber)
        {
            $this->id = $id;
            $this->email = $email;
            $this->image = $image;
            $this->firstName = $firstName;
            $this->lastName = $lastName;
            $this->phoneNumber = $phoneNumber;
        }
    }



?>