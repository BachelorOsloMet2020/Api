<?php

    class animalProfile
    {
        public $id;
        public $userId;
        public $name;
        public $image;
        public $idTag;
        public $animalType;
        public $sex;
        public $sterilized;
        public $color;
        public $furLength;
        public $furPattern;
        public $description;

        function __construct($id, $userId, $name, $image, $idTag, $animalType, $sex, $sterilized, $color, $furLength, $furPattern, $description)
        {
            $this->id = $id;
            $this->userId = $userId;
            $this->name = $name;
            $this->image = $image;
            $this->idTag = $idTag;
            $this->animalType = $animalType;
            $this->sex = $sex;
            $this->sterilized = $sterilized;
            $this->color = $color;
            $this->furLength = $furLength;
            $this->furPattern = $furPattern;
            $this->description = $description;
        }


    }



?>