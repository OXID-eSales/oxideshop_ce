<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;


use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

class ContextStub implements ContextInterface
{
    private $logLevel = 'error';

    private $logFilePath = 'log.txt';

    private $currentShopId = 1;

    private $shopDir = '/tmp';

    /**
     * @var array
     */
    private $requiredContactFormFields;

    /**
     * @param string $logLevel
     */
    public function setLogLevel($logLevel)
    {
        $this->logLevel = $logLevel;
    }

    /**
     * @param string $logFilePath
     */
    public function setLogFilePath($logFilePath)
    {
        $this->logFilePath = $logFilePath;
    }

    /**
     * @return string
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @return string
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    /**
     * @return array
     */
    public function getRequiredContactFormFields()
    {
        return $this->requiredContactFormFields;
    }

    /**
     * @param array $requiredContactFormFields
     */
    public function setRequiredContactFormFields(array $requiredContactFormFields)
    {
        $this->requiredContactFormFields = $requiredContactFormFields;
    }

    /**
     * @param int $shopId
     */
    public function setCurrentShopId($shopId)
    {
        $this->currentShopId = $shopId;
    }

    /**
     * @return int
     */
    public function getCurrentShopId()
    {
        return $this->currentShopId;
    }

    /**
     * @param string $shopDir
     */
    public function setShopDir($shopDir)
    {
        $this->shopDir = $shopDir;
    }

    /**
     * @return string
     */
    public function getShopDir()
    {
        return $this->shopDir;
    }

}