<?php

    class heartbeat
    {
        private $status;
        private $out;
        private $time;
        function __construct()
        {
            $this->status = true;
            $this->time = (new DateTime())->getTimestamp();
        }

        public function getJson()
        {
            $j = new stdClass();
            $j->status = $this->status;
            $j->time = $this->time;
            return json_encode($j);
        }



    }



?>