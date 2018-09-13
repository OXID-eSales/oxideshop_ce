<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Utility;

use OxidEsales\Eshop\Core\Config;

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
        return $this->getConfigParameter('sShopDir');
    }

    /**
     * @param string $name
     * @return mixed
     */
    private function getConfigParameter($name)
    {
        return $this->config->getConfigParam($name);
    }
}
