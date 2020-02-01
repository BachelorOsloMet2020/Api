<?php
    require_once "./db.php";

    /**
     * isTokenValid
     * If it is needed to check that the user i authenticated before making request(s)
     */
    function isTokenValid($db)
    {
        
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
        $queryText = "SELECT * FROM session WHERE sessionToken = ?";
        $stmt = $db->prepare($queryText);
        $stmt->bind_param("s", $token);
        $stmt->execute();
        $result = $stmt->get_result();
        $r = $result->fetch_assoc();

        if ($result->num_rows == 1)
        {
            if ($r['sessionToken'] == $token)
                return true;
            else
                return false;
        }
        else
        {
            return false;
        }
        $stmt->free_result(); $stmt->close();
    }

    if ($_SERVER['REQUEST_METHOD'] === "GET")
    {
        switch ($_GET['request'])
        {
            case 'heartbeat':
            {
                require './get/heartbeat.php';
                $o = new heartbeat();
                echo $o->getJson();
                break;
            }

            case 'myProfile':
            {
                if (isTokenValid($db))
                {   
                    require './DAL/qprofile.php';
                    require './DHL/profile.php';
                    $token = isset($_GET['token']) ? $_GET['token'] : null;
                    $uid = isset($_GET['uid']) ? $_GET['uid'] : null;
                    $QP = new qprofile($db);
                    $QP_R = $QP->getPrivateProfile($uid, $token);
                    $PP = new profile();
                    $profile = $PP->getPrivateProfile($QP_R);
                    echo json_encode($profile);
                }   
                else
                {
                    echo json_encode(array(
                        "status" => false,
                        "message" => "Request for myProfile was attempted with invalid or missing token"
                    ));
                }
                break;
            }

            case 'myProfileId':
            {
                require './DAL/qprofile.php';
                require './DHL/profile.php';
                $authId = isset($_GET['authId']) ? $_GET['authId'] : null;
                $QP = new qprofile($db);
                $QP_R = $QP->getPrivateProfileId($authId);
                echo json_encode($QP_R);
                break;
            }

            case 'profile':
            {
                require './DAL/qprofile.php';
                require './DHL/profile.php';
                $uid = isset($_GET['uid']) ? $_GET['uid'] : null;
                $QP = new qprofile($db);
                $QP_R = $QP->getSinglePublicProfile($uid);
                $PP = new profile();
                $profile = $PP->getSinglePublicProfile($QP_R);
                echo json_encode($profile);
                break;
            }

        }



    }
    else if ($_SERVER['REQUEST_METHOD'] === "POST")
    {
        if (isset($_POST['auth']))
        {
            require './DAL/qauth.php';
            require './DHL/auth.php';

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
                    $session = $qa->newSessionPAuth($R_authId->data['id'], $oAuth);
                    echo json_encode($session);

                    break;
                }
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
                        require './DHL/profile.php';
                        $PP = new profile();
                        $data = isset($_REQUEST['data']) ? $_REQUEST['data'] : null;
                        $profile = $PP->getPrivateProfile_FromJson($data);

                        /*$QP = new qprofile($db);
                        $QP_R = $QP->getPrivateProfile($uid, $token);
                        ;*/
                        
                        echo json_encode($profile);
                    }   
                    else
                    {
                        echo json_encode(array(
                            "status" => false,
                            "message" => "Request for myProfile was attempted with invalid or missing token"
                        ));
                    }
                    break;
                }
            }
        }

        
    }








    function isSecure()
    {
        if ($_SERVER['HTTPS'] != "on")
        {
            $e = new stdClass();
            $e->status = false;
            $e->message = "Api does not accept plain HTTP requests!";
            echo json_encode($e);
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Closes the exsisting database connection
     */
    $db->close();
    $db = null;
?>