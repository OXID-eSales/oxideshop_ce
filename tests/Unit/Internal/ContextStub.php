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
    private $adminLogFilePath;
    private $doLogAdminQueries;
    private $isAdmin;
    private $skipLogTags;
    private $adminUserId;

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
        $this->adminLogFilePath = $context->getAdminLogFilePath();
        $this->doLogAdminQueries = $context->isEnabledAdminQueryLog();
        $this->isAdmin = $context->isAdmin();
        $this->skipLogTags = $context->getSkipLogTags();
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


    /**
     * @param string $logFilePath
     */
    public function setAdminLogFilePath($logFilePath)
    {
        $this->adminLogFilePath = $logFilePath;
    }

    /**
     * @return string
     */
    public function getAdminLogFilePath(): string
    {
        return $this->adminLogFilePath;
    }

    /**
     * @param bool $doLogAdminQueries
     */
    public function setIsEnabledAdminQueryLog(bool $doLogAdminQueries)
    {
        $this->doLogAdminQueries = $doLogAdminQueries;
    }

    /**
     * @return bool
     */
    public function isEnabledAdminQueryLog(): bool
    {
        return $this->doLogAdminQueries;
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    /**
     * @param bool $isAdmin
     */
    public function setIsAdmin(bool $isAdmin)
    {
        $this->isAdmin = $isAdmin;
    }

    /**
     * @return string
     */
    public function getAdminUserId(): string
    {
        if (!isset($this->adminUserId)) {
            $context = ContainerFactory::getInstance()->getContainer()->get(ContextInterface::class);
            $this->adminUserId = $context->getAdminUserId();
        }

        return $this->adminUserId;
    }

    /**
     * @param string $userId
     */
    public function setAdminUserId(string $userId)
    {
        $this->adminUserId = $userId;
    }

    /**
     * @return array
     */
    public function getSkipLogTags(): array
    {
        return $this->skipLogTags;
    }

    /**
     * @param array $skipLogTags
     *
     * @return mixed
     */
    public function setSkipLogTags(array $skipLogTags)
    {
        $this->skipLogTags = $skipLogTags;
    }
}
