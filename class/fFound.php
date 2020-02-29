<?php
    class fFound
    {
        public $foundId;
        public $lat;
        public $lng;
        public $timeDate;
        public $area;
        public $ai;
        public $fi; 
        public $userId;
        public $image;
        public $idTag;
        public $name;
        public $animalType;
        public $animalTypeExtras;
        public $sex;
        public $sterilized;
        public $color;
        public $furLength;
        public $furPattern;
        public $description;

        function __construct(
            $foundId, 
            $lat, 
            $lng, 
            $timeDate, 
            $area,
            $ai, 
            $fi, 
            $userId,
            $image,
            $idTag,
            $name,
            $animalType,
            $animalTypeExtras,
            $sex,
            $sterilized,
            $color,
            $furLength,
            $furPattern,
            $description)
            {
                $this->foundId = $foundId;
                $this->lat = $lat;
                $this->lng = $lng;
                $this->timeDate = $timeDate;
                $this->area = $area;
                $this->ai = $ai;
                $this->fi = $fi;
                $this->userId = $userId;
                $this->image = $image;
                $this->idTag = $idTag;
                $this->name = $name;
                $this->animalType = $animalType;
                $this->animalTypeExtras = $animalTypeExtras;
                $this->sex = $sex;
                $this->sterilized = $sterilized;
                $this->color = $color;
                $this->furLength = $furLength;
                $this->furPattern = $furPattern;
                $this->description = $description;
            }
    }
?>