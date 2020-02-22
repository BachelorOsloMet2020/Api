<?php
    require_once './DML/auth.php';

    class authTest extends PHPUnit\Framework\TestCase
    {

        public function test_json_construct_oAuth()
        {
            $id = 1010101010101;
            $email = "test@dyrebar.no";
            $oToken = "9483758093u453n4jh34tuhj803ug0384hjg8034hjg0843j0t3";
            $provider = "DYREBAR";
            $clientType = "unit";
            $appId = null;
            $duid = null;

            $j = '{ "id" : '.$id.', "email" : "'.$email.'", "token" : "'.$oToken.'", "provider" : "'.$provider.'", "client_type" : "'.$clientType.'" }';
            $auth = new auth();
            $o = $auth->to_oAuthObject($j);

            $this->assertEquals($id, $o->getId());
            $this->assertEquals($email, $o->getEmail());
            $this->assertEquals($oToken, $o->getToken());
            $this->assertEquals($provider, $o->getProvider());
            $this->assertEquals($clientType, $o->getClientType());
            $this->assertNull($o->getDeviceId());
            $this->assertNull($o->getAppId());
        }

        public function test_json_construct_pAuth()
        {
            $id = 1010101010101;
            $email = "test@dyrebar.no";
            $password = "tjhijtih9gr9rg809er8ge9r8g";
            $provider = "DYREBAR";
            $clientType = "unit";
            $appId = null;
            $duid = null;

            $j = '{ "id" : '.$id.', "email" : "'.$email.'", "password" : "'.$password.'", "provider" : "'.$provider.'", "client_type" : "'.$clientType.'" }';
            $auth = new auth();
            $o = $auth->to_pAuthObject($j);

            $this->assertEquals($id, $o->getId());
            $this->assertEquals($email, $o->getEmail());
            // Checks that the input password gets hashed
            $this->assertNotEquals($password, $o->getPassword());
            $this->assertEquals($provider, $o->getProvider());
            $this->assertEquals($clientType, $o->getClientType());
            $this->assertNull($o->getDeviceId());
        }

        public function test_password_challenge()
        {
            $id = 1010101010101;
            $email = "test@dyrebar.no";
            $password = "ff36459854953457mk6767483495848nvx9";
            $password_Hashed = "0309a8574767665bfda4342e381db90b973a170b3beb2b966c6b608c053283f4";
            $provider = "DYREBAR";
            $clientType = "unit";
            $appId = null;
            $duid = null;

            $qa = new stdClass();
            $qa->status = true;
            $qa->data = array(
                "id" => 1,
                "oAuthId" => $id,
                "email" => $email,
                "password" => $password_Hashed,
                "provider" => $provider
            );

            $j = '{ "id" : '.$id.', "email" : "'.$email.'", "password" : "'.$password.'", "provider" : "'.$provider.'", "client_type" : "'.$clientType.'" }';
            $auth = new auth();
            $auo = $auth->to_pAuthObject($j);

            $o = $auth->challengePassword($auo, $qa);
            print_r($o);

            $this->assertTrue($o->status);
        }
        
    }

?>