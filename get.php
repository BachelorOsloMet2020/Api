<?php


/**
 * get.php
 * Required in the api.php file
 * 
 * Get requests are placed in separete file to reduce size of api.php
 * 
 */
$raw = isset($_GET['raw']) ? $_GET['raw'] : null;
switch ($_GET['request'])
{
    case 'heartbeat':
    {
        require './DML/heartbeat.php';
        $o = new heartbeat();
        echo $o->getJson();
        break;
    }

    case 'myProfile':
    {
        if (isTokenValid($db))
        {   
            require './DAL/qprofile.php';
            require './DML/profile.php';
            $token = isset($_GET['token']) ? $_GET['token'] : null;
            $uid = isset($_GET['uid']) ? $_GET['uid'] : null;
            $authId = isset($_GET['authId']) ? $_GET['authId'] : null;
            $QP = new qprofile($db);
            $QP_R = null;
            if (isset($uid) && $uid != null)
                $QP_R = $QP->getPrivateProfileByUid($uid, $token);
            else
                $QP_R = $QP->getPrivateProfileByAuthId($authId, $token);
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
        require './DML/profile.php';
        $authId = isset($_GET['authId']) ? $_GET['authId'] : null;
        $QP = new qprofile($db);
        $QP_R = $QP->getPrivateProfileId($authId);
        echo json_encode($QP_R);
        break;
    }

    case 'profile':
    {
        require './DAL/qprofile.php';
        require './DML/profile.php';
        $uid = isset($_GET['uid']) ? $_GET['uid'] : null;
        $QP = new qprofile($db);
        $QP_R = $QP->getSinglePublicProfile($uid);
        $PP = new profile();
        $profile = $PP->getSinglePublicProfile($QP_R);
        echo json_encode($profile);
        break;
    }

    case 'animal':
    {
        require './DAL/qanimal.php';
        require './DML/animal.php';
        
        $animalId = isset($_GET['animalId']) ? $_GET['animalId'] : null;


        break;
    }

    case 'animals':
    {
        /** Possibly require token to do this,
         * Discuss this later
         */

        require './DAL/qanimal.php';
        require './DML/animal.php';
        $userId = isset($_GET['uid']) ? $_GET['uid'] : null;
        if ($userId == null)
        {
            $out = new stdClass();
            $out->status = false;
            $out->message = "User id not provided, request for all animals are not permitted";
            echo json_encode($out);
            return;
        }

        $qa = new qanimal($db);
        $qa_r = $qa->getAnimalsByUid($userId);
        $ap = new animal();
        $animalProfiles = $ap->getAnimalProfiles($qa_r);
        if ($raw == null)
            echo json_encode($animalProfiles);
        else
            print_r($animalProfiles);
        break;
    }
    case 'missing':
    {
        require './DAL/qmissing.php';
        require './DML/missing.php';
        
        $out = null;
        $qm = new qmissing($db);
        $mp = new missing();
        $missingId = isset($_GET['id']) ? $_GET['id'] : null;
        if ($missingId == null)
        {
            $data = $qm->getMissing();
            $out = $mp->getMissings($data);
        }
        else 
        {
            $data = $qm->getMissingById($missingId);
            $out = $mp->getMissing($data);
        }
        if ($raw == null)
            echo json_encode($out);
        else
            print_r($out);
        break;
    }
}

?>