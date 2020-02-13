<?php

    class poster
    {
        public $id;
        public $animalId;
        public $lat;
        public $long;
        public $timeDate;

        function __construct($id, $animalId, $lat, $long, $timeDate)
        {
            $this->id = $id;
            $this->animalId = $animalId;
            $this->lat = $lat;
            $this->long = $long;
            $this->timeDate = $timeDate;
        }


    }




?>