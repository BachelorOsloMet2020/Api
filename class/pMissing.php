<?php

    /**
     * Post object for missing
     */
    class pMissing
    {
        public $lat;
        public $lng;
        public $timeDate;
        public $userId;
        public $animalId;
        public $area;

        function __construct($animalId, $userId, $lat, $lng, $timeDate, $area)
        {
            $this->lat = $lat;
            $this->lng = $lng;
            $this->timeDate = $timeDate;
            $this->animalId = $animalId;
            $this->userId = $userId;
            $this->area = $area;
        }


    }

?>