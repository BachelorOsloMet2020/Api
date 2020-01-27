<?php

    /**
     * Custom class in order to provide error messages
     */
    class errc
    {
        public function error_missingData()
        {
            $o = new stdClass();
            $o->status = false;
            $o->message = "Required data was not provided. \r\nRequest has been rejected";
            return json_encode($o);
        }
    }


?>