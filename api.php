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
            case 'isSessionValid':
            case 'validateSessionToken':
            {
                require './Auth.php';
                $data = isset($_GET['data']) ? $_GET['data'] : null;
                $o = new Auth($db, $data);
                echo json_encode($o->is_session_valid());
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
        switch ($_POST['request'])
        {
            case 'oAuth':
            {
                require './Auth.php';
                $data = isset($_POST['data']) ? $_POST['data'] : null;
                $o = new Auth($db, $data);
                $result = $o->challenge();
                echo json_encode($result);
                break;
            }
            case 'pAuth':
            {
                require './Auth.php';
                $data = isset($_POST['data']) ? $_POST['data'] : null;
                $o = new Auth($db, $data);
                $result = $o->dyrebar_sign_in();
                echo json_encode($result);
                break;
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