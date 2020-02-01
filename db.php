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


    /**
     * Variable to hold query type before defining
     */
    $query;

    /**
     * Variable to hold fetch type before defining
     */
    $fetch_array;

    /**
     * Variable to hold fetch type before defining
     */
    $fetch_object;

    /**
     * Variable to hold fetch type before defining
     */
    $fetch_assoc;


    $num_rows;

    /**
     * Variable to hold close
     */
    $close;

    switch($type)
    {
        /**
         * If type is MySql
         */
        case "mysql":
        {
            $query            = 'mysqli_query';
            $fetch_array      = 'mysqli_fetch_array';
            $fetch_object     = 'mysqli_fetch_object';
            $fetch_assoc      = 'mysqli_fetch_assoc';
            $close            = 'mysqli_close';
            $num_rows         = 'mysqli_num_rows';
            break;
        }


    }

/**
 * Defines constants
 */
define('__type',            $type);
define('__query',           $query);
define('__fetch_array',     $fetch_array);
define('__fetch_object',    $fetch_object);
define('__fetch_assoc',     $fetch_assoc);
define('__num_rows',        $num_rows);
define('__close',           $close);


?>