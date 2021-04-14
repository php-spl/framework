<?php

namespace Web\Filesystem;

use file_exists;
use file_put_contents;
use file_get_contents;
use is_dir;
use rmdir;

class File
{
    public static function exists($path): bool
    {
        return file_exists($path);
    }

    public static function inc($path)
    {
        include_once $path . '.php';
    }

    public static function req($path)
    {
        require_once $path . '.php';
    }

    public static function get($filename)
    {
        return file_get_contents($filename);
    }

    public static function put($filename, $data, $flags = 0)
    {
        return file_put_contents($filename, $data, $flags);
    }

    /**
    * Get the MD5 hash of the file at the given path.
    *
    * @param  string  $path
    * @return string
    */
    public static function hash($path)
    {
        if (self::exists($path)) {
            return md5_file($path);
        }
        return false;
    }

    public static function read($path, $filter = ['.','..','.gitignore'])
    {
        $files=[];
        $res= array_diff(scandir($path), $filter);
        foreach ($res as $key => $file) {
            $files[]=$file;
        }
        return $files;
    }

    public static function delete($filename)
    {
        if(self::exists($filename)){

            unlink($filename);

            return true;
        }

        return false;
    }

    public static function remove($dir)
    {

        $files = self::read($dir);
    
        foreach ($files as $file) {
          (is_dir("$dir/$file")) ? self::remove("$dir/$file") : self::delete("$dir/$file");
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