<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Setup\ConfigFile;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;

class ConfigFileDao implements ConfigFileDaoInterface
{
    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
    }

    public function replacePlaceholder(string $placeholderName, string $value): void
    {
        $configFileContent = str_replace(
            "<{$placeholderName}>",
            $value,
            $this->getConfigFileContent()
        );

        file_put_contents($this->context->getConfigFilePath(), $configFileContent);
    }

    private function getConfigFileContent(): string
    {
        if (!file_exists($this->context->getConfigFilePath())) {
            throw new ConfigFileNotFoundException('File not found ' . $this->context->getConfigFilePath());
        }

        return file_get_contents($this->context->getConfigFilePath());
    }
}
