<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Container\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ContextStub extends BasicContextStub implements ContextInterface
{
    private $logLevel;
    private $logFilePath;
    private $currentShopId;
    private $shopIds;
    private $requiredContactFormFields = [];
    private $adminLogFilePath;
    private $doLogAdminQueries;
    private $isAdmin;
    private $skipLogTags;
    private $adminUserId;
    private bool $productiveMode;
    private $demoMode;

    /**
     * ContextStub constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $context = ContainerFactory::getInstance()->getContainer()->get(ContextInterface::class);
        $this->logLevel = $context->getLogLevel();
        $this->shopIds = $context->getAllShopIds();
        $this->logFilePath = $context->getLogFilePath();
        $this->adminLogFilePath = $context->getAdminLogFilePath();
        $this->doLogAdminQueries = $context->isEnabledAdminQueryLog();
        $this->isAdmin = $context->isAdmin();
        $this->skipLogTags = $context->getSkipLogTags();
        $this->demoMode = $context->isShopInDemoMode();
        $this->productiveMode = $context->isShopInProductiveMode();
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

    public function setSkipLogTags(array $skipLogTags)
    {
        $this->skipLogTags = $skipLogTags;
    }

    public function isShopInProductiveMode(): bool
    {
        return $this->productiveMode;
    }

    public function setShopInProductiveMode(bool $productiveMode): void
    {
        $this->productiveMode = $productiveMode;
    }

    /**
     * @return bool
     */
    public function isShopInDemoMode(): bool
    {
        return $this->demoMode;
    }

    /**
     * @param bool $demoMode
     */
    public function setShopInDemoMode(bool $demoMode)
    {
        $this->demoMode = $demoMode;
    }
}
