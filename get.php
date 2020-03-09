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
                "err" => __err["0x12"]
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
            require './DAL/qprofile.php';
            require './DML/profile.php';

            $data = $qm->getMissingById($missingId);
            $missing = $mp->getMissing($data);


            $QP = new qprofile($db);
            $QP_R = $QP->getSinglePublicProfile($missing->data->userId);
            $PP = new profile();
            $profile = $PP->getSinglePublicProfile($QP_R);

            $out = new stdClass();
            $out->status = $missing->status;
            $out->missing = $missing->data;
            $out->profile = $profile->profile;
        }
        if ($raw == null)
            echo json_encode($out);
        else
            print_r($out);
        break;
    }
    case 'found':
    {
        require './DAL/qfound.php';
        require './DML/found.php';

        $out = new stdClass();

        $qf = new qfound($db);
        $fm = new found();
        $foundId = isset($_GET['id']) ? $_GET['id'] : null;

        if ($foundId == null)
        {
            $data = $qf->getFound();
            $out = $fm->getFounds($data);
        }
        else 
        {
            require './DAL/qprofile.php';
            require './DML/profile.php';

            $data = $qf->getFoundById($foundId);
            $found = $fm->getFound($data);


            $QP = new qprofile($db);
            $QP_R = $QP->getSinglePublicProfile($found->data->userId);
            $PP = new profile();
            $profile = $PP->getSinglePublicProfile($QP_R);

            
            $out = new stdClass();
            $out->status = $found->status;
            $out->found = $found->data;
            $out->profile = $profile->profile;

            
        }
        if ($raw == null)
            echo json_encode($out);
        else
            print_r($out);

        break;
    }
    case 'myPosters':
    {
        if (isTokenValid($db))
        {   
            require './DAL/qmissing.php';
            require './DML/missing.php';

            require './DAL/qfound.php';
            require './DML/found.php';

            $out = new stdClass();

            $uid = isset($_GET['uid']) ? $_GET['uid'] : null;

            $qm = new qmissing($db);
            $missings = $qm->getMyMissing($uid);

            $qf = new qfound($db);
            $founds = $qf->getMyFound($uid);

            if (isset($missings) && $missings != null)
            {
                $_m = new missing();
                $_missing = $_m->getMissings($missings);
                if (isset($_missing))
                {
                    $missing = new stdClass();
                    $missing->data = $_missing->data;
                    $out->missing = $missing;
                }
            }

            if (isset($founds) && $founds != null)
            {
                $_f = new found();
                $_found = $_f->getFounds($founds);
                if (isset($_found))
                {
                    $found = new stdClass();
                    $found->data = $_found->data;
                    $out->found = $found;
                }
            }

            echo json_encode($out);

        }   
        else
        {
            echo json_encode(array(
                "status" => false,
                "err" => __err["0x12"]
            ));
        }
        break;
    }



}

?>