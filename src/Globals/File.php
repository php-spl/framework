<?php

namespace Spl\Globals;

use Spl\Globals\Request;

class File
{
    public function exists($path, $info = 'tmp_name'): bool
    {
        if(file_exists($path) || is_uploaded_file($_FILES[$path][$info])) {
            return true;
        }
        return false;
    }

    public function has($path, $info = 'tmp_name'): bool
    {
        return $this->exists($path, $info);
    }

    public function inc($path, $extension = '.php')
    {
        include_once $path . $extension;
    }

    public function req($path, $extension = '.php')
    {
        require_once $path . $extension;
    }

    public function all()
    {
        return $_FILES;
    }

    public function get($name, $info = 'tmp_name')
    {
        if($this->exists($name)) {
            if(file_get_contents($name)) {
                return file_get_contents($name);
            }

            if(isset($_FILES[$name][$info])) {
                return $_FILES[$name][$info];
            }
        }
        return false;
    }

    public function put($filename, $data, $flags = 0)
    {
        return file_put_contents($filename, $data, $flags);
    }

    /**
    * Get the MD5 hash of the file at the given path.
    *
    * @param  string  $path
    * @return string
    */
    public function hash($path)
    {
        if ($this->has($path)) {
            return md5_file($path);
        }
        return false;
    }

    public function read($path, $filter = ['.','..','.gitignore'])
    {
        $files=[];
        $res= array_diff(scandir($path), $filter);
        foreach ($res as $key => $file) {
            $files[]=$file;
        }
        return $files;
    }

    public function delete($filename)
    {
        if($this->has($filename)){

            unlink($filename);

            return true;
        }

        return false;
    }

    public function move($filename, $destination)
    {
        if(move_uploaded_file($filename, $destination)) {
            return true;
        }

        return false;
    }

    public function create($dir, $permission = 0750)
    {
        if(mkdir($dir, $permission, true)) {
            return true;
        }

        return false;
    }

    public function remove($dir)
    {
        $files = $this->read($dir);
    
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? $this->remove("$dir/$file") : $this->delete("$dir/$file");
        }
        return rmdir($dir);
    }

    public static function download($file, $filename = null)
    {
        if ($filename == null) {
            $filename = basename($file);
        }

        header("Expires: 0");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
        header("Cache-Control: no-store, no-cache, must-revalidate");
        header("Cache-Control: post-check=0, pre-check=0", false);
        header("Pragma: no-cache");  header("Content-type: application/file");
        header('Content-length: '.filesize($file));
        header('Content-disposition: attachment; filename='.$filename);
        readfile($file);
    }
}