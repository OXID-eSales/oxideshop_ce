<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ContextStub extends BasicContextStub implements ContextInterface
{
    private $logLevel;
    private $logFilePath;
    private $currentShopId;
    private $shopIds;
    private $configurationEncryptionKey;
    private $requiredContactFormFields = [];

    /**
     * ContextStub constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $context = ContainerFactory::getInstance()->getContainer()->get(ContextInterface::class);
        $this->logLevel = $context->getLogLevel();
        $this->shopIds = $context->getAllShopIds();
        $this->currentShopId = $context->getCurrentShopId();
        $this->configurationEncryptionKey = $context->getConfigurationEncryptionKey();
        $this->logFilePath = $context->getLogFilePath();
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
        return $this->configurationEncryptionKey;
    }

    /**
     * @return array
     */
    public function getAllShopIds(): array
    {
        return $this->shopIds;
    }

    /**
     * @param array $shopIds
     */
    public function setAllShopIds(array $shopIds)
    {
        $this->shopIds = $shopIds;
    }
}
