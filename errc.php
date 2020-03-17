<?php

    $error = array(
        "0x1" => array( "errc" => "0x1", "errm" => "Email is already registered with another sign in method" ),
        "0x2" => array( "errc" => "0x2", "errm" => "Failed to register, please contact support if issue persists" ),
        "0x3" => array( "errc" => "0x3", "errm" => "Could not find any entry matching with your service id" ),
        "0x4" => array( "errc" => "0x4", "errm" => "Something went wrong" ),
        "0x5" => array( "errc" => "0x5", "errm" => "No password registered"),
        "0x6" => array( "errc" => "0x6", "errm" => "Unable to delete session, please contact support if issue persists"),
        "0x7" => array( "errc" => "0x7", "errm" => "Session is invalid"),
        "0x8" => array( "errc" => "0x8", "errm" => "Failed create a new session"),
        "0x9" => array( "errc" => "0x9", "errm" => "Email or password is incorrect"),
        "0x10" => array( "errc" => "0x10", "errm" => "Challenging Facebook authentication token failed"),
        "0x11" => array( "errc" => "0x11", "errm" => "Challenging Google authentication token failed"),
        "0x12" => array( "errc" => "0x12", "errm" => "Token is not vaild"),
        "0x13" => array( "errc" => "0x13", "errm" => "Could not find profile"),
        "0x14" => array( "errc" => "0x14", "errm" => "Could not register profile"),
        "0x15" => array( "errc" => "0x15", "errm" => "Could not update email"),
        "0x16" => array( "errc" => "0x16", "errm" => "Incomplete data"),
        "0x17" => array( "errc" => "0x17", "errm" => "No data could be found"),
        "0x18" => array( "errc" => "0x18", "errm" => "Failed to register animal"),
        "0x19" => array( "errc" => "0x19", "errm" => "Could not find missing post"),
        "0x20" => array( "errc" => "0x20", "errm" => "Unable to find your missing post"),
        "0x21" => array( "errc" => "0x21", "errm" => "Unable to delete your missing post"),
        "0x22" => array( "errc" => "0x22", "errm" => "Could not create missing post"),
        "0x23" => array( "errc" => "0x23", "errm" => "Could not find your found post"),
        "0x24" => array( "errc" => "0x24", "errm" => "Could not find found post"),
        "0x25" => array( "errc" => "0x25", "errm" => "Unable to delete your found post"),
        "0x26" => array( "errc" => "0x26", "errm" => "Unable to create found post"),
        "0x27" => array( "errc" => "0x27", "errm" => "Email is required"),
        "0x28" => array( "errc" => "0x28", "errm" => ""),
        "0x29" => array( "errc" => "0x29", "errm" => ""),
    );

    $success = array(

    );


    define("__err", $error);
    define("__scm", $success);

?>