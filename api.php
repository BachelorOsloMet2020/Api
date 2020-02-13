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
        require_once 'get.php';
    }
    else if ($_SERVER['REQUEST_METHOD'] === "POST")
    {
        require_once 'post.php';
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