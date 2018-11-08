<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Facts\Facts;

/**
 * @internal
 */
class Context implements ContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var
     */
    private $facts;

    /**
     * Context constructor.
     * @param Config $config
     */
    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->getConfigParameter('sLogLevel');
    }

    /**
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->config->getLogsDir() . 'oxideshop.log';
    }

    /**
     * @return array
     */
    public function getRequiredContactFormFields()
    {
        $contactFormRequiredFields = $this->getConfigParameter('contactFormRequiredFields');

        return $contactFormRequiredFields === null ? [] : $contactFormRequiredFields;
    }

    /**
     * @return int
     */
    public function getCurrentShopId()
    {
        return $this->config->getShopId();
    }

    /**
     * @return string
     */
    public function getShopDir()
    {
        return $this->getFacts()->getSourcePath();
    }

    /**
     * @return string
     */
    public function getContainerCacheFile()
    {
        return $this->getConfigParameter('sCompileDir') . DIRECTORY_SEPARATOR . 'containercache.php';
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getConfigParameter($name)
    {
        return $this->config->getConfigParam($name);
    }

    /**
     * @return Facts
     */
    private function getFacts()
    {
        if ($this->facts == null) {
            $this->facts = new Facts();
        }
        return $this->facts;
    }
}
