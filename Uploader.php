<?php
namespace fileupload;

/**
 * 
 */
class Uploader
{

    private $location;
    public $uploads = [];
    public $error   = [];
    //define file types
    private $sizeLimit  = 2000000;
    //add possible file extensions
    private $extensions = ["jpeg", "jpg", "png"];
    /**
     * [__construct description]
     * @param [type] $location [description]
     */
    public function __construct($location = null)
    {
        $this->location = $location;
        if (isset($_POST['upfiles'])) {
            $this->location = $location;
            //if a database name was added. this is an array
            $title          = $_POST['title'];
            //extract the file parameters
            $names          = $_FILES['image']['name'];
            $types          = $_FILES['image']['type'];
            $tmp_names      = $_FILES['image']['tmp_name'];
            $sizes          = $_FILES['image']['size'];
            $errors         = $_FILES['image']['error'];
            //print_r($sizes);
            foreach ($names as $key => $value) {
                //echo "uploading $value ";
                if ($value == "") {
                    continue; //id no file was uploaded
                }

                if (!$this->checkExtension($value)) {
                    $this->error[] = "File extension is not supported";
                }
                //check file size
                if (!$this->checkSize($sizes[$key])) {
                    $this->error[] = "File Size for image: $value too large ";
                }

                /*if($dbName[$key] == ""){
                $dbName[$key] = $this->rename($value);
                }*/
                $this->upload($tmp_names[$key], $value, $title[$key]);
            }
        }
    }
    /**
     * [checkExtension description]
     * @param  [string] $param [description]
     * @return [boolean]        [description]
     */
    private function checkExtension($param)
    {
        //echo "Checking extension for $param <br>" ;
        $ext = $this->getFileExtension($param);
        //echo "<br>$ext<br>";
        if (!in_array($ext, $this->extensions)) {
            return false;
        } else {
            return true;
        }
    }

    private function getFileExtension($file)
    {
        $ex = explode('.', $file);
        return strtolower(end($ex));
    }

    private function fileExists($param)
    {
        //echo "Checking size for $param<br>";
    }

    private function checkSize($param)
    {
        //echo "Checking size for $param<br>";
        if ($param > $this->sizeLimit) {
            return false;
        } else {
            return true;
        }
    }

    private function rename($param)
    {
        $time = $this->microtimeFloat();
        return $time . "." . $this->getFileExtension($param);
    }

    public function microtimeFloat()
    {
        list($usec, $sec) = explode(" ", microtime());
        return ((float) $usec + (float) $sec);
    }

    private function upload($temp, $file, $title)
    {
        //rename the file
        $file = $this->rename($file);
        echo $this->location;

        if (move_uploaded_file($temp, $this->location . $file)) {
            //add to database
            $this->uploads[] = [
                'title' => $title,
                'file'  => $file];
            return true;
        } else {
            return false;
        }
    }
}
