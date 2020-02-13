<?php

    /**
     * Variabel to hold the database system type
     * Mysql, SqlSrv, Postgre, MariaDb, etc..
     */
    $type = "mysql";

    /**
     * Database connection object
     */
    $db = null;

    /**
     * Variable to hold the database address
     * Hostname or IPv4 address
     * Have not tested for IPv6
     */
    $address = "localhost";

    /**
     * Variable to hold the database connection username
     */
    $username = "dyrebar";

    /**
     * Variable to hold the database connection password
     */
    $password = "b@&qJ9d!PavHHPpF&otw1cd%I^Mwe1aWR";

    /**
     * Variable to hold the database name
     */
    $database = "dyrebar";

    switch($type)
    {
        case "mysql":
        {
            $db = new mysqli($address, $username, $password, $database); //mysqli_connect($address, $username, $password, $database);
            $db->set_charset("utf8");
            break;
        }

        case "postgre":
        {
            $db = pg_connect("host=$address user=$username password=$password dbname=$database options='--client_encoding=UTF8'");
            break;
        }
    }

    if ($db == false)
    {
        $e = new stdClass();
        $e->status = false;
        $e->message = "A connection to the designated database could not be made";
        echo json_encode($e);
        die();
    }

?>