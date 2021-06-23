<?php

namespace App\Server;

use Storage as FileStorage;

use Cache;

class Storage
{
    /**
     * @var unique storage reference (md5 hash)
     */
    private $reference;

    /**
     * $reference - unique storage md5 reference
     */
    public function __construct($reference, $clearExistingStorage = false)
    {
        $this->reference = $reference;
        $this->cacheTimeout = config('cache.stores.terminal.timeout');
        $this->cache = Cache::store('terminal');

        if ($clearExistingStorage) {
            $this->cache->forget($this->reference);
        }

        /*
        $this->inputFileName = "{$this->reference}.input";
        $this->outputFileName = "{$this->reference}.output";


        if ($clearExistingStorage) {
            $this->disk->delete($this->inputFileName);
            $this->disk->delete($this->outputFileName);
        }
        */
    }

    private function getModFile()
    {
        if (!is_array($contents = $this->cache->get($this->reference))) {
            $this->cache->set($this->reference, [], $this->cacheTimeout);
            return [];
        }

        return $contents;
    }

    /**
     * Get stored data
     */
    public function get($fileType, $sinceID = 1)
    {
        $to = $this->getModID($fileType);

        $out = [];
        for ($i = $sinceID; $i <= $to;$i++) {
            $key = $this->reference . $fileType . $i;

            if ($cacheData = $this->cache->get($key, null)) {
                $out[$i] = ['id' => $i, 'data' => $cacheData];
            }
        }

        return $out;
    }

    public function getModHash($fileType) {
        return $this->getModID($fileType);
    }

    /*
     * Get last modification id
     */
    public function getModID($fileType)
    {
        $info = $this->getModFile();

        $modKey = 'mod_'.$fileType;

        return $info[$modKey] ?? -1;
    }

    /**
     * Push data to the storage
     *
     * @return string - file modification hash used for checking if storage has been modified
     */
    public function append($fileType, $data)
    {
        $modKey = 'mod_'.$fileType;

        $info = $this->getModFile();
        @$info[$modKey]++;

        $this->cache->set($this->reference, $info, $this->cacheTimeout);

        $key = $this->reference . $fileType . $info[$modKey];

        $this->cache->set($key, $data, $this->cacheTimeout);

        return $info[$modKey];
    }

    public function referenceExists()
    {
        return (bool)$this->cache->get($this->reference, false);
    }
}
