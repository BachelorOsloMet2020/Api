<?php
    require_once "./db.php";

    /**
     * isTokenValid
     * If it is needed to check that the user i authenticated before making request(s)
     */
    function isTokenValid($db)
    {
        
        $token = isset($_REQUEST['token']) ? $_REQUEST['token'] : null;
        //print_r($token);
        if ($token == null)
            return false;
        $res = (__query)($db, "SELECT * FROM session WHERE sessionToken = '$token';");
        if ((__num_rows)($res) == 1 && (__fetch_assoc)($res)['sessionToken'] == $token)
            return true;
        else
            return false;
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
                    require './get/profile.php';
                    $data = isset($_GET['data']) ? $_GET['data'] : null;
                    $p = new Profile($db, $data, strtolower($_GET['request']));
                    echo $p->getJson();
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
    (__close)($db);

?>