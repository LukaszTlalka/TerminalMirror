<?php

namespace App\Server;

use Storage as FileStorage;

class Storage
{
    /**
     * @var unique storage reference (usually md5 hash)
     */
    private $reference;

    const FILE_TYPE_INPUT = 1;
    const FILE_TYPE_OUTPUT = 2;

    /**
     * $reference - unique storage reference
     */
    public function __construct($reference, $clearExistingStorage = false)
    {
        $this->reference = $reference;

        $this->disk = self::getDisk('console-sessions');

        $this->inputFileName = "{$this->reference}.input";
        $this->outputFileName = "{$this->reference}.output";

        $this->dataPushed = false;

        if ($clearExistingStorage) {
            $this->disk->delete($this->inputFileName);
            $this->disk->delete($this->outputFileName);
        }
    }

    private static function getDisk($key)
    {
        return FileStorage::disk($key);
    }

    public function get($fileType)
    {
        return $this->disk->get($fileType == Storage::FILE_TYPE_INPUT ? $this->inputFileName : $this->outputFileName);
    }

    /*
     * Get last modification hash
     */
    public function getModHash($fileType)
    {
        $file = $fileType == Storage::FILE_TYPE_INPUT ? $this->inputFileName : $this->outputFileName;
        $filePath = $this->disk->path($file);

        clearstatcache();


        return !file_exists($filePath) ? null : filesize($filePath).".".filemtime($filePath);
    }

    /**
     * Push data to the storage
     *
     * @return string - file modification hash used for checking if storage has been modified
     */
    public function append($fileType, $data)
    {
        $file = $fileType == Storage::FILE_TYPE_INPUT ? $this->inputFileName : $this->outputFileName;
        $this->disk->append($file, $data);

        return $this->getModHash($fileType);
    }

    public function referenceExists()
    {
        return $this->disk->exists($this->inputFileName);
    }
}
