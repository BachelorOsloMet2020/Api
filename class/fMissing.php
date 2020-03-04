<?php

    class fMissing
    {
        public $missingId;
        public $lat;
        public $lng;
        public $timeDate;
        public $animalId;
        public $idTag;
        public $userId;
        public $name;
        public $image;
        public $animalType;
        public $animalTypeExtras;
        public $sex;
        public $sterilized;
        public $color;
        public $furLength;
        public $furPattern;
        public $description;
        public $area;
        public $mdesc;

        function __construct($missingId, $lat, $lng, $timeDate, $animalId, $idTag, $userId, $name, $image, $animalType, $animalTypeExtras, $sex, $sterilized, $color, $furLength, $furPattern, $description, $area, $mdesc)
        {
            $this->missingId = $missingId;
            $this->lat = $lat;
            $this->lng = $lng;
            $this->timeDate = $timeDate;
            $this->animalId = $animalId;
            $this->idTag = $idTag;
            $this->userId = $userId;
            $this->name = $name;
            $this->image = $image;
            $this->animalType = $animalType;
            $this->animalTypeExtras = $animalTypeExtras;
            $this->sex = $sex;
            $this->sterilized = $sterilized;
            $this->color = $color;
            $this->furLength = $furLength;
            $this->furPattern = $furPattern;
            $this->description = $description;
            $this->area = $area;
            $this->mdesc = $mdesc;
        }
    }


?>