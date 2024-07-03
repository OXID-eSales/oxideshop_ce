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
    private string $logLevel;
    private string $logFilePath;
    private array $shopIds;
    private array $requiredContactFormFields = [];
    private string $adminLogFilePath;
    private bool $doLogAdminQueries;
    private bool $isAdmin;
    private array $skipLogTags;
    private ?string $adminUserId;
    private bool $productiveMode;
    private bool $demoMode;

    public function __construct(private int $shopId = 1)
    {
        parent::__construct();
        $context = ContainerFacade::get(ContextInterface::class);
        $this->logLevel = $context->getLogLevel();
        $this->shopIds = $context->getAllShopIds();
        $this->logFilePath = $context->getLogFilePath();
        $this->adminLogFilePath = $context->getAdminLogFilePath();
        $this->doLogAdminQueries = $context->isEnabledAdminQueryLog();
        $this->isAdmin = $context->isAdmin();
        $this->adminUserId = null;
        $this->skipLogTags = $context->getSkipLogTags();
        $this->demoMode = $context->isShopInDemoMode();
        $this->productiveMode = $context->isShopInProductiveMode();

        $this->activeModuleServicesFilePath = $context->getActiveModuleServicesFilePath($this->getCurrentShopId());
    }

    public function setLogLevel(string $logLevel): void
    {
        $this->logLevel = $logLevel;
    }

    public function setLogFilePath(string $logFilePath): void
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

    public function setAdminLogFilePath(string $logFilePath): void
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

    public function getCurrentShopId(): int
    {
        return $this->shopId;
    }

    public function setCurrentShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }
}
