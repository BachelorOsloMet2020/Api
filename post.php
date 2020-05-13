<?php

if (isset($_POST['auth']))
{
    require './DAL/qauth.php';
    require './DML/auth.php';

    $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
    //echo "Handling auth";
    switch ($_POST['auth'])
    {
        case 'GOOGLE':
        {
            $auth = new auth();
            $qa = new qauth($db);
            $oAuth = $auth->to_oAuthObject($data);
            $resp = $auth->challengeGoogle($oAuth);
            if ($resp->status != true) { echo json_encode($resp); exit; }
            $R_authId = $qa->getAuthId($oAuth->getId());
            if ($R_authId->status == false)
            {
                $cr_auhtId = $qa->newAuthId($oAuth->getId(), $oAuth->getEmail(), $oAuth->getProvider());
                if ($cr_auhtId->status == true && $cr_auhtId->data == true)
                    $R_authId = $qa->getAuthId($oAuth->getId());
                else
                    { echo json_encode(array("status" => false, "getAuth" => $R_authId, "newAuth" => $cr_auhtId)); exit; }
            }
            $session = $qa->newSessionOAuth($R_authId->data['id'], $oAuth);
            echo json_encode($session);
            break;
        }

        case 'FACEBOOK':
        {
            $auth = new auth();
            $qa = new qauth($db);
            $oAuth = $auth->to_oAuthObject($data);
            $resp = $auth->challengeFacebook($oAuth);
            if ($resp->status != true) { echo json_encode($resp); exit; }
            $R_authId = $qa->getAuthId($oAuth->getId());
            if ($R_authId->status == false)
            {
                $cr_auhtId = $qa->newAuthId($oAuth->getId(), $oAuth->getEmail(), $oAuth->getProvider());
                if ($cr_auhtId->status == true && $cr_auhtId->data == true)
                    $R_authId = $qa->getAuthId($oAuth->getId());
                else
                    { echo json_encode(array("status" => false, "getAuth" => $R_authId, "newAuth" => $cr_auhtId)); exit; }
            }
            $session = $qa->newSessionOAuth($R_authId->data['id'], $oAuth);
            echo json_encode($session);
            break;        
        }

        /** Authenticates email and password combination  */
        case 'DYREBAR':
        {
            $auth = new auth();
            $qa = new qauth($db);
            $pAuth = $auth->to_pAuthObject($data);
            $qAuth = $qa->getPassword($pAuth->getEmail());
            $resp = $auth->challengePassword($pAuth, $qAuth);
            if ($resp->status != true) { echo json_encode($resp); exit; }
            
            $R_authId = $qa->getAuthId($pAuth->getId());
            if ($R_authId->status == false)
            {
                $cr_auhtId = $qa->newAuthId($pAuth->getId(), $pAuth->getEmail(), $pAuth->getProvider());
                if ($cr_auhtId->status == true && $cr_auhtId->data == true)
                    $R_authId = $qa->getAuthId($pAuth->getId());
                else
                    { echo json_encode(array("status" => false, "getAuth" => $R_authId, "newAuth" => $cr_auhtId)); exit; }
            }
            $session = $qa->newSessionPAuth($R_authId->data['id'], $pAuth);
            echo json_encode($session);

            break;
        }

        case "REGISTER":
        {
            $auth = new auth();
            $qa = new qauth($db);

            $pAuth = $auth->to_pAuthObject($data);
            $resp = $qa->newEmailAuthId($pAuth);
            error_log("DEBUG Auth;". print_r($pAuth, true));
            error_log("DEBUG QAuth;". print_r($resp, true));

            if ($resp->status == false)
            {
                $out = new stdclass();
                $out->status = false;
                $out->message = "Couldn't register account";
                if (isset($resp->error_message))
                    $out->error_message = $resp->error_message;
                echo json_encode($out);
            }
            else
            {
                echo json_encode($resp);
            }
            break;
        }

        /** Validates token */
        case 'validate':
        {
            $auth = new auth();
            $qa = new qauth($db);
            $sessionObject = $auth->to_sessionObject($data);
            $R_authId = $qa->getAuthId($sessionObject->getId());
            if ($R_authId->status == false) { echo json_encode($R_authId); exit; }
            $R_session = $qa->getSession($R_authId->data['id'], $sessionObject->getToken());
            echo json_encode($R_session);
            break;
        }

        case 'endSession':
        {
            $auhtId = isset($_REQUEST['authId']) ? $_REQUEST['authId'] : null;
            $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;

            $auth = new auth();
            $status = $auth->endSession($auhtId, $token);
            if ($status->status == true)
            {
                $qa = new qauth($db);
                $out = $qa->endSession($auhtId, $token);
                echo json_encode($out);
            }
            else
            {
                $array =  array(
                    "status" => false,
                    "message" => "Values missing"
                );
                echo json_encode($array);
            }
            break;
        }

    }
}
else
{
    switch ($_POST['request'])
    {
        case 'myProfile':
        {
            if (isTokenValid($db))
            {   
                require './DAL/qprofile.php';
                require './DML/profile.php';
                $QP = new qprofile($db);
                $PP = new profile();
                $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
                $profile = $PP->getPrivateProfile_FromJson($data);
                if (!isset($profile) || !isset($profile->profile))
                {
                    // error message


                }
                $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
                $result = $QP->postPrivateProfile($token, $profile->profile);
                
                echo json_encode($result);
            }   
            else
            {
                echo json_encode(array(
                    "status" => false,
                    "err" => __err["0x12"]
                ));
            }
            break;
        }

        case "myAnimal":
        {
            if (isTokenValid($db))
            {
                require './DAL/qanimal.php';
                require './DML/animal.php';

                $qa = new qanimal($db);
                $data = isset($_POST['data']) ? $_POST['data'] : null;

                $pa = new animal();
                $animal = $pa->postAnimalProfile($data);
                if (!isset($animal) || !isset($animal->animal))
                {

                }
                $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
                $result = $qa->postAnimalProfile($token, $animal->animal);
                echo json_encode($result);
                break;
            }
        }
        case "missing":
        {
            require './DAL/qmissing.php';
            require './DML/missing.php';
            
            $out = null;
            $qm = new qmissing($db);
            $mp = new missing();

            $authId = isset($_POST['authId']) ? $_POST['authId'] : null;
            $token = isset($_POST['token']) ? $_POST['token'] : null;
            $data = isset($_POST['data']) ? $_POST['data'] : null;

            
            if ($authId == null || $token == null || $data == null)
            {
                $out = array(
                    "status" => false,
                    "err" => __err["0x12"]
                );
            }
            else
            {
                $mi = $mp->postMissing($data);
                error_log("Decoede data -> " . print_r($mi, true));
                $out = $qm->postMissing($authId, $token, $mi->data);
            }


            echo json_encode($out);
            break;
        }

        case "found":
        {
            require './DAL/qfound.php';
            require './DML/found.php';
            $out = null;

            $authId = isset($_POST['authId']) ? $_POST['authId'] : null;
            $token = isset($_POST['token']) ? $_POST['token'] : null;
            $data = isset($_POST['data']) ? $_POST['data'] : null;

            $qf = new qfound($db);
            $fp = new found();

            if ($authId == null || $token == null || $data == null)
            {
                $out = array(
                    "status" => false,
                    "err" => __err["0x12"]
                );
            }
            else
            {
                $fi = $fp->postFound($data);
                $out = $qf->postFound($authId, $token, $fi->data);
            }


            echo json_encode($out);
            

            break;
        }
        

        case "delete_missing":
        {
            require './DAL/qmissing.php';
            $qm = new qmissing($db);

            $authId = isset($_POST['authId']) ? $_POST['authId'] : null;
            $token = isset($_POST['token']) ? $_POST['token'] : null;
            $userId = isset($_POST['userId']) ? $_POST['userId'] : null;
            $missingId = isset($_POST['missingId']) ? $_POST['missingId'] : null;

            $out = $qm->deleteMissing($authId, $token, $userId, $missingId);

            echo json_encode($out);

            break;
        }

        case "delete_found":
        {
            require './DAL/qfound.php';
            $qf = new qfound($db);

            $authId = isset($_POST['authId']) ? $_POST['authId'] : null;
            $token = isset($_POST['token']) ? $_POST['token'] : null;
            $userId = isset($_POST['userId']) ? $_POST['userId'] : null;
            $missingId = isset($_POST['missingId']) ? $_POST['missingId'] : null;

            $out = $qf->deleteFound($authId, $token, $userId, $missingId);

            echo json_encode($out);

            break;
        }

        case "delete_me":
        {
            if (isTokenValid($db))
            {
                $auth = new auth();
                $sessionObject = $auth->to_sessionObject($data);

                $pdml = new qprofile($db);
                $out = $pdml->forget_me($sessionObject);
                echo json_encode($out);
            }
            break;
        }

    }
}




?>