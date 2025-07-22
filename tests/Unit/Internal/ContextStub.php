<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\Context;
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
    private string $adminUserId;
    private bool $productiveMode;
    private bool $demoMode;

    private ContextInterface $context;

    public function __construct()
    {
        $this->context = new Context();
        parent::__construct();
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
        return $this->logLevel ?? $this->context->getLogLevel();
    }

    public function getLogFilePath(): string
    {
        return $this->logFilePath ?? $this->context->getLogFilePath();
    }

    public function getRequiredContactFormFields(): array
    {
        return $this->requiredContactFormFields ?? $this->context->getRequiredContactFormFields();
    }

    public function setRequiredContactFormFields(array $requiredContactFormFields): void
    {
        $this->requiredContactFormFields = $requiredContactFormFields;
    }

    public function getAllShopIds(): array
    {
        return $this->shopIds ?? $this->context->getAllShopIds();
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
        return $this->adminLogFilePath ?? $this->context->getAdminLogFilePath();
    }

    public function setIsEnabledAdminQueryLog(bool $doLogAdminQueries): void
    {
        $this->doLogAdminQueries = $doLogAdminQueries;
    }

    public function isEnabledAdminQueryLog(): bool
    {
        return $this->doLogAdminQueries ?? $this->context->isEnabledAdminQueryLog();
    }

    public function isAdmin(): bool
    {
        return $this->isAdmin ?? $this->context->isAdmin();
    }

    public function setIsAdmin(bool $isAdmin): void
    {
        $this->isAdmin = $isAdmin;
    }

    public function getAdminUserId(): string
    {
        return $this->adminUserId ?? $this->context->getAdminUserId();
    }

    public function setAdminUserId(string $userId): void
    {
        $this->adminUserId = $userId;
    }

    public function getSkipLogTags(): array
    {
        return $this->skipLogTags ?? $this->context->getSkipLogTags();
    }

    public function setSkipLogTags(array $skipLogTags): void
    {
        $this->skipLogTags = $skipLogTags;
    }

    public function isShopInProductiveMode(): bool
    {
        return $this->productiveMode ?? $this->context->isShopInProductiveMode();
    }

    public function setShopInProductiveMode(bool $productiveMode): void
    {
        $this->productiveMode = $productiveMode;
    }

    public function isShopInDemoMode(): bool
    {
        return $this->demoMode ?? $this->context->isShopInDemoMode();
    }

    public function setShopInDemoMode(bool $demoMode): void
    {
        $this->demoMode = $demoMode;
    }

    public function getCurrentShopId(): int
    {
        return $this->context->getCurrentShopId();
    }
}