<?php
require_once '../get/heartbeat.php';

    class heartbeatTest extends PHPUnit\Framework\TestCase
    {
        public function testHeartbeatJSON()
        {   
            $h = new heartbeat();
            $out = $h->getJson();

            $j = json_decode($out);
            $this->assertTrue($j->{'status'}, $h->time);
        }
    }

?>