    <?php
    require_once "./db.php";

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
        }



    }
    else if ($_SERVER['REQUEST_METHOD'] === "POST")
    {
        switch ($_POST['request'])
        {
            case 'oAuth':
            {
                require './post/oAuth.php';
                $data = isset($_POST['challenge']) ? $_POST['challenge'] : null;
                $o = new oAuth($db, $data);
                echo $o->getJson();
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