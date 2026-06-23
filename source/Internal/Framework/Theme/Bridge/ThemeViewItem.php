<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge;

readonly class ThemeViewItem
{
    public function __construct(
        private array $info,
        private ?ThemeViewItem $parent,
        private string $activationError
    ) {
    }

    public function getInfo(string $name): mixed
    {
        return $this->info[$name] ?? null;
    }

    public function getId(): string
    {
        return (string) ($this->info['id'] ?? '');
    }

    public function getParent(): ?ThemeViewItem
    {
        return $this->parent;
    }

    public function checkForActivationErrors(): string
    {
        return $this->activationError;
    }
}
