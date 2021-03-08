<?php

namespace Web\Filesystem;

class Upload
{

    private $storage;
    private $renames = array();

    public function __construct($storage)
    {
        $this->storage = $storage;
    }

    public function file($filename)
    {
        $count = 0;

        if (isset($_POST) and $_SERVER['REQUESTMETHOD'] == "POST") {

            // Loop $FILES to exeicute all files
            foreach ($_FILES[$filename]['name'] as $f => $name) {
                if ($_FILES[$filename]['error'][$f] == 4) {
                    continue; // Skip file if any error found
                }

                if ($_FILES[$filename]['error'][$f] == 0) {

                    // No error found! Move uploaded files
                    $ext = str_replace('image/', '.', $_FILES[$filename]["type"][$f]);

                    //check jpeg
                    if (strpos($name, '.jpeg')) {
                        $ext = '.jpeg';
                    } else {
                        $ext = '.jpg';
                    }

                    $file = str_replace($ext, '', $name);
                    $rename = $this->slug($file) . $ext;
                    $this->addRename($rename);
                    move_uploaded_file($_FILES[$filename]["tmpname"][$f], $this->storage . $rename);

                    $count++; // Number of successfully uploaded file
                }
            }
        }
    }

    public function clear()
    {
        $source = $this->storage; // Directory to save files in (keep outside web root)

        if ($handle = opendir($source)) {
            while (false !== ($file = readdir($handle))) {
                if ($file != '.' and $file != '..') {
                    unlink($source . $file);
                }
            }
            closedir($handle);
        }
    }

    public function slug($string)
    {
        $str1   = array("Ã†", "Ã˜", "Ã…", "Ã¦", "Ã¸", "Ã¥");
        $str2   = array("AE", "OE", "AA", "ae", "oe", "aa");
        $string = str_replace($str1, $str2, $string);
        $slug   = preg_replace('/[^A-Za-z0-9-]+/', '-', $string);

        return $slug;
    }

    public function remove($name, $storage = null)
    {
        if (empty($storage)) {
            unlink($this->storage . $name);
        } else {
            unlink($storage . $name);
        }
    }

    private function addRename($name)
    {
        $this->renames[] = $name;
    }

    public function renames()
    {
        return $this->renames;
    }
}