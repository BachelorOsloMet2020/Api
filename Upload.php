<?php
    require_once './secrets.php';

    class Upload
    {
        
        private $out;
        private $imageName;
        private $imagePath;
        private $imageFolder;
        /**
         * Creates Upload object
         * $imageName 
         */
        function __construct($imageName, $type)
        {
            $this->imageName = $imageName;
            if ($type instanceof profile)
            {
                $this->imagePath = "../" . __images_dir . "profile/";
                $this->imageFolder = "profile/";
            }
            else if ($type instanceof animal)
            {
                $this->imagePath = "../" . __images_dir . "animal/";
                $this->imageFolder = "animal/";
            }
            else
            {
                error_log("Upload.php encounterted an unexpected type, please verify paramter". print_r($type, true));
            }

        }

        /**
         * $byteStream Requires Base64 String
         * @return Returns Object with "status" and "url", "url" is not set if status is false
         */
        public function handleByteStream($byteStream)
        {
            $out = new stdClass();
            $fullPath = $this->imagePath . $this->imageName . ".png";
            $base_decode = base64_decode($byteStream);
            $saved = file_put_contents($fullPath, $base_decode);
            if ($saved != false)
            {
                $out->status = true;
                $out->url = __host . __images_dir . $this->imageFolder . $this->imageName . ".png";
            }
            else
                $out->status = false;
            return $out;
        }



        public function handleFileUpload()
        {
            $out = new stdClass();
            $out->status = true;

            if (!$this->isFilePresent())
            {
                $this->out->status = false;
                return;
            }
            
            if (!$this->isValidSize())
            {
                $this->out->status = false;
                return;
            }

            if (!$this->isValidFormat())
            {
                $this->out->status = false;
                return;
            }


            if ($this->type instanceof profile)
            {
                $imgPath = "profile/" . $this->imageName . $this->getExtension();
                $fullPath = __images_dir . $imgPath;
                $result = $this->moveFile($fullPath);
                if ($result)
                {
                    $out->url = __host . $imgPath;
                }
                else
                {
                    $out->status = false;
                }
            }
            else if ($this->type instanceof animal)
            {
                $imgPath = "animal/" . $this->imageName . $this->getExtension();
                $fullPath = __images_dir . $imgPath;
                $result = $this->moveFile($fullPath);
                if ($result)
                {
                    $out->url = __host . $imgPath;
                }
                else
                {
                    $out->status = false;
                }
            }

            return $out;
        }


    
        private function moveFile($fullPath)
        {
            if (move_uploaded_file($_FILES['file']['tmp_name'], $fullPath))
            {
                return true;
            }
            else
            {
                $this->out->message = "Failed to handle uploaded file";
                return false;
            }
        }


        private function isFilePresent()
        {
            if ($_FILES['file']['error'] == UPLOAD_ERR_NO_FILE)
            {
                $this->out->message = "File not uploaded";
                return false;
            }
            else
            {
                return true;
            }
        }

        private function isValidSize()
        {
            if ($_FILES['file']['error'] == UPLOAD_ERR_INI_SIZE || $_FILES['file']['error'] == UPLOAD_ERR_FORM_SIZE)
            {
                $this->out->message = "File is to large";
                return false;
            }
            else
            {
                return true;
            }
        }

        private function isValidFormat()
        {
            $formats = array(
                'jpg' => 'image/jpeg',
                'png' => 'image/png',
                'gif' => 'image/gif'
            );
            $finfo = new finfo(FILEINFO_MIME_TYPE);
            if (false === $ext = array_search($finfo->file($_FILES['file']['tmp_name']), $formats, true)) 
            {
                $this->out->message = "Upload does not accept this filetype";
                return false;
            }
            else
            {
                return true;
            }
        }

        private function getExtension()
        {
            $ext = pathinfo($_FILES['file']['tmp_name'], PATHINFO_EXTENSION);
            return $ext;
        }


        public function getJson()
        {
            return json_encode($this->out);
        }

    }






?>