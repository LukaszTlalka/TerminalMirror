<?php

namespace App\Server;

use Storage as FileStorage;

class Storage
{
    /**
     * @var unique storage reference (usually md5 hash)
     */
    private $reference;

    /**
     * $reference - unique storage reference
     */
    public function __construct($reference, $clearExistingStorage = false)
    {
        $this->reference = $reference;

        $this->disk = self::getDisk('console-sessions');

        $this->inputFileName = "{$this->reference}.input";

        $this->dataPushed = false;

        if ($clearExistingStorage) {
            $this->disk->delete($this->inputFileName);
        }
    }

    private static function getDisk($key)
    {
        return FileStorage::disk('console-sessions');
    }

    public function inputGet()
    {
        return $this->disk->get($this->inputFileName);
    }

    public function inputAppend($data)
    {
        $this->disk->append($this->inputFileName, $data);
    }

    public function referenceExists()
    {
        return $this->disk->exists($this->inputFileName);
    }
}
