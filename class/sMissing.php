<?php

    /**
     * Small size missing, only small details
     */
    class sMissing
    {
        public $missingId;
        public $lat;
        public $lng;
        public $timeDate;
        public $animalId;
        public $name;
        public $image;
        public $animalType;
        public $animalTypeExtras;
        public $color;
        public $area;

        function __construct($missingId, $lat, $lng, $timeDate, $animalId, $name, $image, $animalType, $animalTypeExtras, $color, $area)
        {
            $this->missingId = $missingId;
            $this->lat = $lat;
            $this->lng = $lng;
            $this->timeDate = $timeDate;
            $this->animalId = $animalId;
            $this->name = $name;
            $this->image = $image;
            $this->animalType = $animalType;
            $this->animalTypeExtras = $animalTypeExtras;
            $this->color = $color;
            $this->area = $area;
        }


    }

?>