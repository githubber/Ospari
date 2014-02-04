<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Filehandler
 *
 * @author 28h
 */

namespace NZ;

class Filehandler {

    //put your code here

    public final function makeDirs($strPath, $mode = 0777) {
        return is_dir($strPath) or ( self::makeDirs(dirname($strPath), $mode) and mkdir($strPath, $mode) );
    }

    public function generatePathFromID($id, $depth = 2) {

        $dif = strlen($id) - $depth;
        while ($dif < 0) {
            $id .= $id . '0';
            $dif++;
        }

        $id = strrev($id);
        $path = '';
        for ($i = 0; $i < $depth; $i++) {
            $path .= '/' . substr($id, $i, 1);
        }

        return $path;
    }

    public function save($_file, $data) {
        $fileDir = dirname($_file);
        if ($fileDir) {
            self::makeDirs($fileDir);
        }
        return file_put_contents($_file, $data);
    }

    public function copyFile($source, $dest) {
        $fileDir = dirname($dest);
        if ($fileDir) {
            self::makeDirs($fileDir);
        }
        return copy($source, $dest);
    }

    public function getExtension($_file) {
        $pathParts = pathinfo($_file);
        if (isset($pathParts['extension'])) {
            return $pathParts['extension'];
        }
        return '';
    }

    public function recursFiles($it) {
        for (; $it->valid(); $it->next()) {
            if ($it->isDir() && !$it->isDot()) {
                $it->current();

                if ($it->hasChildren()) {
                    $bleh = $it->getChildren();
                    $this->recursFiles($bleh);
                }
            } elseif ($it->isFile()) {
                $this->files[] = $it->current()->__toString();
            }
        }
    }

    public function getDirs($path, $recursive = true) {
        if ($recursive) {
            $this->recursDirs(new \RecursiveDirectoryIterator($path));
            return $this->files;
        }

        $ret = array();
        $dir = new \DirectoryIterator($path);

        foreach ($dir as $file) {
            if ($file->isDir()) {
                if (!$file->isDot()) {
                    $ret[] = $file->getFilename();
                }
            }
        }

        return $ret;
    }

    public function recursDirs($it) {
        for (; $it->valid(); $it->next()) {
            if ($it->isDir() && !$it->isDot()) {
                $it->current();

                if ($it->hasChildren()) {
                    $bleh = $it->getChildren();
                    $this->recursDirs($bleh);
                }
            } elseif ($it->isFile()) {
                $this->files[] = $it->current()->__toString();
            }
        }
    }

}
