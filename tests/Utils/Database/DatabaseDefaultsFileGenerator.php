<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Utils\Database;


use OxidEsales\Facts\Config\ConfigFile;

class DatabaseDefaultsFileGenerator
{
    /**
     * @var ConfigFile
     */
    private $config;

    /**
     * @param ConfigFile $config
     */
    public function __construct(ConfigFile $config)
    {
        $this->config = $config;
    }

    /**
     * @return string File path.
     */
    public function generate(): string
    {
        $file = sys_get_temp_dir() . DIRECTORY_SEPARATOR . uniqid('testing_lib', true) . '.cnf';
        $resource = fopen($file, 'w');
        $fileContents = "[client]"
            . "\nuser=" . $this->config->getVar('dbUser')
            . "\npassword=" . $this->config->getVar('dbPwd')
            . "\nhost=" . $this->config->getVar('dbHost')
            . "\nport=" . $this->config->getVar('dbPort')
            . "\n";
        fwrite($resource, $fileContents);
        fclose($resource);
        return $file;
    }
}
