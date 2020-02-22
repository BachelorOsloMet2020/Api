<?php

    /**
     * Post object for missing
     */
    class pMissing
    {
        public $lat;
        public $lng;
        public $timeDate;
        public $animalId;
        public $area;

        function __construct($animalId, $lat, $lng, $timeDate, $area)
        {
            $this->lat = $lat;
            $this->lng = $lng;
            $this->timeDate = $timeDate;
            $this->animalId = $animalId;
            $this->area = $area;
        }


    }

?>