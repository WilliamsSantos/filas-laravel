<?php

namespace App\Utils;

use Storage;
use Exception;

class FileManager
{
    private $storage;
    private $storageDir;
    private $customException;

    public function __construct()
    {
        $this->storageDir = 'imports';
        $this->storage = Storage::disk('public');
    }

    public function save($file, $filename)
    {
        return $this->storage->putFileAs( $this->storageDir, $file, $filename );
    }

    public function exists($file)
    {
        return $this->storage->exists($this->getFullFilePath($file));
    }

    public function read($file)
    {
        return $this->storage->get($this->getFullFilePath($file));
    }

    private function getFullFilePath($filename)
    {
        return $this->storageDir.'/'.$filename;
    }
}
