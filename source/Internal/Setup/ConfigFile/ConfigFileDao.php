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
    private const PLACEHOLDERS = [
        'dbHost',
        'dbName',
        'dbPort',
        'dbUser',
        'dbPwd',
        'sShopURL',
        'sShopDir',
        'sCompileDir',
    ];

    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(BasicContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * {@inheritdoc}
     */
    public function replacePlaceholder(string $placeholderName, string $value): void
    {
        $placeholder = $this->getPlaceholder($placeholderName);
        $originalContents = $this->getConfigFileContent();
        $this->checkPlaceholderPresent($placeholder, $originalContents);
        $replacedContents = str_replace($placeholder, $value, $originalContents);

        file_put_contents($this->context->getConfigFilePath(), $replacedContents);
    }

    /**
     * {@inheritdoc}
     */
    public function checkIsEditable(): void
    {
        $fileContents = $this->getConfigFileContent();
        foreach (self::PLACEHOLDERS as $placeholderName) {
            $this->checkPlaceholderPresent($this->getPlaceholder($placeholderName), $fileContents);
        }
    }

    /**
     * @throws ConfigFileNotFoundException
     */
    private function getConfigFileContent(): string
    {
        if (!file_exists($this->context->getConfigFilePath())) {
            throw new ConfigFileNotFoundException('File not found ' . $this->context->getConfigFilePath());
        }

        return file_get_contents($this->context->getConfigFilePath());
    }

    private function getPlaceholder(string $placeholderName): string
    {
        return "<{$placeholderName}>";
    }

    /**
     * @throws FileNotEditableException
     */
    private function checkPlaceholderPresent(string $placeholder, string $fileContents): void
    {
        if (false === strpos($fileContents, $placeholder)) {
            throw new FileNotEditableException("Configuration file is not editable - value for $placeholder can not be set.");
        }
    }
}
