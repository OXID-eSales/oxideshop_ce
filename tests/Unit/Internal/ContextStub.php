<?php declare(strict_types=1);
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

    private $containerCacheFile = '/tmp/containercache.php';

    /**
     * @var array
     */
    private $requiredContactFormFields;

    /**
     * @return string
     */
    public function getEnvironment(): string
    {
        return 'dev';
    }

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
     * @param string $shopDir
     */
    public function setShopDir($shopDir)
    {
        $this->shopDir = $shopDir;
    }

    /**
     * @return string
     */
    public function getShopDir(): string
    {
        return $this->shopDir;
    }

    /**
     * @return string
     */
    public function getConfigurationEncryptionKey(): string
    {
        return '';
    }

    /**
     * @return string
     */
    public function getContainerCacheFile(): string
    {
        return $this->containerCacheFile;
    }

    /**
     * @param string $containerCacheFile
     */
    public function setContainerCacheFile(string $containerCacheFile)
    {
        $this->containerCacheFile = $containerCacheFile;
    }
}
