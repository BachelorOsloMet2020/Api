<?php

    class qauth
    {
        private $db;
        function __construct($db)
        {
            $this->db = $db;
        }

        /**
         * getAuthId
         * Retrieves the authId from auth
         * returns true if auhtId is found along
         */
        public function getAuthId($oAuthId)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT id FROM auth WHERE oAuthId = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("i", $oAuthId);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows == 1)
            {
                 $out->data = $result->fetch_assoc();
            }
            else
            {
                $out->status = false;
                if ($result->num_rows > 1)
                {
                    $out->err = __err["0x4"];
                    error_log("Failure in database, duplicate entrys for oAuthId: " . $oAuthId);
                }
                else 
                {
                    $out->err = __err['0x3'];
                }
            }
            return $out;
        }

        /**
         * newAuthId
         * Creates new auth entry based on oAuth, email and provider
         * returns true if insert worked
         */
        public function newAuthId($oAuthId, $email, $provider)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "INSERT INTO auth (oAuthId, email, provider) VALUES (?, ? ,?)
            ON DUPLICATE KEY UPDATE
            oAuthId = VALUES(oAuthId),
            email = VALUES(email),
            provider = VALUES(provider)";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("iss", $oAuthId, $email, $provider);
            $stmt->execute();

            if ($stmt->affected_rows >= 1)
            {
                $out->data = true;
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x2"];
                //$out->error_message = $stmt->error;
            }
            return $out;
        }

        /**
         * newEmailAuthId
         * Requires pAuth Object
         */
        public function newEmailAuthId($p)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "INSERT INTO auth (oAuthId, email, password, provider) VALUES (?, ? ,?, ?)";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("isss", $p->id, $p->email, $p->password, $p->provider);
            $stmt->execute();

            if ($stmt->affected_rows == 1)
            {
                $out->status = true;
            }
            else
            {
                $out->status = false;
                if ($stmt->errno == 1062)
                {
                    // Duplicate
                    $out->err = __err["0x1"];
                    //$out->message = "The email is already registered with another sign in method"; //"Eposten er allerede registrert med en annen innloggins metode";
                    //$out->error_message = $stmt->error;
                }
                else
                {
                    // Unknown
                    $out->err = __err["0x2"];
                }

                
                
            }
            return $out;
        }



        /**
         * getPassword
         * Requires email
         */
        public function getPassword($email)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT * FROM auth WHERE email = ?";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("s", $email);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows == 1)
            {
                $out->data = $result->fetch_assoc();
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x5"];
            }
            return $out;
        }

        public function endSession($authId, $token)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "DELETE FROM session WHERE authId = ? AND sessionToken = ?;";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("is", $authId, $token);
            $stmt->execute();
            
            if ($stmt->affected_rows == 1)
            {
                $out->message = "Session has been ended";
            }
            else
            {
                $out->err = __err['0x6'];
                //$out->error_message = $stmt->error;
            }
            return $out;
        }

        /**
         * getSession
         * Returns true if result contains an valid session
         */
        public function getSession($authId, $token)
        {
            $out = new stdClass();
            $out->status = true;

            $queryText = "SELECT auth.oAuthId, session.sessionToken, auth.provider FROM auth 
            INNER JOIN session ON auth.id = session.authId 
            WHERE auth.id = ? AND session.sessionToken = ?;";
            $stmt = $this->db->prepare($queryText);
            $stmt->bind_param("is", $authId, $token);
            $stmt->execute();

            $result = $stmt->get_result();
            if ($result->num_rows == 1)
            {
                $out->data = $result->fetch_assoc();
                $out->isValid = true;
            }
            else
            {
                $out->status = false;
                $out->isValid = false;
                $out->err = __err["0x7"];
                //$out->error_message = $stmt->error;
            }
            return $out;

        }

        /**
         * newSessionOAuth
         * Generates new session based on oAuth data
         */
        public function newSessionOAuth($authId, $o)
        {
            $out = new stdClass();
            $out->status = true;

            $result = null;

            $time = (new DateTime())->getTimestamp();
            $session_token = md5(uniqid($o->getId()));
            if ($o->getDeviceId() != null)
            {
                $queryText = "INSERT INTO session (authId, oAuthToken, sessionToken, timeDate, clientType, provider, duid) VALUES
                (?, ?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                oAuthToken = VALUES(oAuthToken),
                sessionToken = VALUES(sessionToken),
                timeDate = VALUES(timeDate),
                clientType = VALUES(clientType),
                provider = VALUES(provider)
                ";
                /*print_r($authId);
                print_r($o);*/
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("ississs", $authId, $o->token, $session_token, $time, $o->client_type, $o->provider, $o->device_id);
                $stmt->execute();

                // $out->tmpAffectred = $stmt->affected_rows;
                // $out->tmpToken = $session_token;
                if ($stmt->affected_rows >= 1)
                {
                    $sessionData = new stdClass();
                    $sessionData->id = $o->getId();
                    $sessionData->authId = $authId;
                    $sessionData->session_token = $session_token;
                    $sessionData->time = $time;
                    $sessionData->provider = $o->getProvider();
                    $out->data = $sessionData;
                }
                else
                {
                    $out->status = false;
                    $out->err = __err["0x8"];
                    //$out->error_message = $stmt->error;
                }

                $stmt->free_result();
                $stmt->close();


            }
            else
            {
                $queryText = "INSERT INTO session (authId, oAuthToken, sessionToken, timeDate, clientType, provider) VALUES
                (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                oAuthToken = VALUES(oAuthToken),
                sessionToken = VALUES(sessionToken),
                timeDate = VALUES(timeDate),
                clientType = VALUES(clientType),
                provider = VALUES(provider)
                ";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("ississ", $authId, $o->token, $session_token, $time, $o->client_type, $o->provider);
                $stmt->execute();


                if ($stmt->affected_rows >= 1)
                {
                    $sessionData = new stdClass();
                    $sessionData->id = $o->getId();
                    $sessionData->authId = $authId;
                    $sessionData->session_token = $session_token;
                    $sessionData->time = $time;
                    $sessionData->provider = $o->getProvider();
                    $out->data = $sessionData;
                }
                else
                {
                    $out->status = false;
                    $out->err = __err["0x8"];
                    //$out->error_message = $stmt->error;
                }

                $stmt->free_result();
                $stmt->close();
            }

            return $out;
        }

        /**
         * newSessionPAuth
         * Generates new session based on pAuth data
         */
        public function newSessionPAuth($authId, $p)
        {
            $out = new stdClass();
            $out->status = true;

            $affected_rows = 0;

            $time = (new DateTime())->getTimestamp();
            $session_token = md5(uniqid($p->getId()));

            if ($p->getDeviceId() != null)
            {
                $queryText = "INSERT INTO session (authId, sessionToken, timeDate, clientType, provider, duid) VALUES
                (?, ?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                sessionToken = VALUES(sessionToken),
                timeDate = VALUES(timeDate),
                clientType = VALUES(clientType),
                provider = VALUES(provider)
                ";
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("isisss", $authId, $session_token, $time, $p->getClientType(), $p->getProvider(), $p->getDeviceId());
                $stmt->execute();

                $affected_rows = $stmt->affected_rows;

                $stmt->free_result();
                $stmt->close();


            }
            else
            {
                $queryText = "INSERT INTO session (authId, sessionToken, timeDate, clientType, provider) VALUES
                (?, ?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE 
                sessionToken = VALUES(sessionToken),
                timeDate = VALUES(timeDate),
                clientType = VALUES(clientType),
                provider = VALUES(provider)
                ";
                $clientType = $p->getClientType();
                $provider = $p->getProvider();
                $stmt = $this->db->prepare($queryText);
                $stmt->bind_param("isiss", $authId, $session_token, $time, $clientType, $provider);
                $stmt->execute();

                $affected_rows = $stmt->affected_rows;

                $stmt->free_result();
                $stmt->close();
            }

            if ($affected_rows >= 1)
            {
                $sessionData = new stdClass();
                $sessionData->id = $p->getId();
                $sessionData->authId = $authId;
                $sessionData->session_token = $session_token;
                $sessionData->time = $time;
                $sessionData->provider = $p->getProvider();
                $out->data = $sessionData;
            }
            else
            {
                $out->status = false;
                $out->err = __err["0x8"];
                //$out->error_message = $stmt->error;
            }
            return $out;
        }


    }



?>