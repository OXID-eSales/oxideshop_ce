<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;

class ContextStub extends BasicContextStub implements ContextInterface
{
    private $logLevel;
    private $logFilePath;
    private $shopIds;
    private array $requiredContactFormFields = [];
    private $adminLogFilePath;
    private bool $doLogAdminQueries;
    private bool $isAdmin;
    private $skipLogTags;
    private $adminUserId;
    private bool $productiveMode;
    private bool $demoMode;

    public function __construct()
    {
        parent::__construct();
        $context = ContainerFacade::get(ContextInterface::class);
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
    public function setLogLevel($logLevel): void
    {
        $this->logLevel = $logLevel;
    }

    /**
     * @param string $logFilePath
     */
    public function setLogFilePath($logFilePath): void
    {
        $this->logFilePath = $logFilePath;
    }

    public function getLogLevel(): string
    {
        return $this->logLevel;
    }

    public function getLogFilePath(): string
    {
        return $this->logFilePath;
    }

    public function getRequiredContactFormFields(): array
    {
        return $this->requiredContactFormFields;
    }

    public function setRequiredContactFormFields(array $requiredContactFormFields): void
    {
        $this->requiredContactFormFields = $requiredContactFormFields;
    }

    public function getAllShopIds(): array
    {
        return $this->shopIds;
    }

    public function setAllShopIds(array $shopIds): void
    {
        $this->shopIds = $shopIds;
    }


    /**
     * @param string $logFilePath
     */
    public function setAdminLogFilePath($logFilePath): void
    {
        $this->adminLogFilePath = $logFilePath;
    }

    public function getAdminLogFilePath(): string
    {
        return $this->adminLogFilePath;
    }

    public function setIsEnabledAdminQueryLog(bool $doLogAdminQueries): void
    {
        $this->doLogAdminQueries = $doLogAdminQueries;
    }

    public function isEnabledAdminQueryLog(): bool
    {
        return $this->doLogAdminQueries;
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin;
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getAdminUserId(): string
    {
        if ($this->adminUserId === null) {
            $this->adminUserId = ContainerFacade::get(ContextInterface::class)
                ->getAdminUserId();
        }

        return $this->adminUserId;
    }

    public function setAdminUserId(string $userId): void
    {
        $this->adminUserId = $userId;
    }

    public function getSkipLogTags(): array
    {
        return $this->skipLogTags;
    }

    public function setSkipLogTags(array $skipLogTags): void
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

    public function isShopInDemoMode(): bool
    {
        return $this->demoMode;
    }

    public function setShopInDemoMode(bool $demoMode): void
    {
        $this->demoMode = $demoMode;
    }
}
