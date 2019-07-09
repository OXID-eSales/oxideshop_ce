<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Utility\ContextInterface;

class ContextStub extends BasicContextStub implements ContextInterface
{
    private $logLevel = 'error';

    private $logFilePath = 'log.txt';

    private $currentShopId = 1;

    private $shopIds = [1];

    /**
     * @var array
     */
    private $requiredContactFormFields = [];

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
    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    /**
     * @return string
     */
    public function getLogFilePath(): string
    {
        return $this->logFilePath;
    }

    /**
     * @return array
     */
    public function getRequiredContactFormFields(): array
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
    public function getCurrentShopId(): int
    {
        return $this->currentShopId;
    }

    /**
     * @return string
     */
    public function getConfigurationEncryptionKey(): string
    {
        return '';
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return $this->shopIds;
    }
    /**
     * @return string
     */
    public function getContainerCacheFile(): string
    {
        return '';
    }


    /**
     * @return integer
     */
    public function getPasswordHashingBcryptCost(): int
    {
        /** The 'cost' option defines the CPU cost of hash generation. For testing the minimal possible value is chosen */
        return 4;
    }

    /**
     * @return int
     */
    public function getPasswordHashingArgon2MemoryCost(): int
    {
        return 1024;
    }

    /**
     * @return int
     */
    public function getPasswordHashingArgon2TimeCost(): int
    {
        return 2;
    }

    /**
     * @return int
     */
    public function getPasswordHashingArgon2Threads(): int
    {
        return 2;
    }
}
