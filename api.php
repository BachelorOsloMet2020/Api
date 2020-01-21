<?php
    require_once "./db.php";




    if ($_SERVER['REQUEST_METHOD'] === "GET")
    {




    }
    else if ($_SERVER['REQUEST_METHOD'] === "POST")
    {




        
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

?>