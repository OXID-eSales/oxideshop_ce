<?php
declare(strict_types = 1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Common\Storage;

use SplFileObject;
use Symfony\Component\Yaml\Yaml;

/**
 * Class YamlFileDao
 */
class YamlFileStorage implements ArrayStorageInterface
{
    /**
     * @var SplFileObject
     */
    private $file;

    /**
     * @param SplFileObject $file
     */
    public function __construct(SplFileObject $file)
    {
        $this->file = $file;
    }

    /**
     * @return array
     */
    public function get(): array
    {
        $this->file->rewind();

        $string = '';
        while (!$this->file->eof()) {
            $string .= $this->file->fgets();
        }

        return $string;
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function save(array $data)
    {
        $string = Yaml::dump($data);
        $fileLockAcquired = $this->file->flock(LOCK_EX | LOCK_NB, $wouldBlock) && !$wouldBlock;
        if (!$fileLockAcquired) {
            throw new \Exception('Could not acquire file lock');
        }

        $this->file->ftruncate(0);
        $this->file->fwrite($string);
        $this->file->rewind();
        $this->file->flock(LOCK_UN);
    }
}
