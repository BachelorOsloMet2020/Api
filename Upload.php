<?php
    require_once './secrets.php';

    class Upload
    {
        
        private $out;

        /**
         * Creates Upload object
         * $imageName 
         */
        function __construct($imageName, $type)
        {
            $this->out = new stdClass();
            $this->out->status = true;

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


            if ($type instanceof profile)
            {
                $imgPath = "profile/" . $imageName . $this->getExtension();
                $fullPath = __images_dir . $imgPath;
                $result = $this->moveFile($fullPath);
                if ($result)
                {
                    $this->out->image = __host . $imgPath;
                }
                else
                {
                    
                }
            }
    
    
    
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