<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event;

use Symfony\Contracts\EventDispatcher\Event;

/**
 * This event is dispatched when there are not loadable service classes
 * found in a services.yaml file.
 */
class ServicesYamlConfigurationErrorEvent extends Event
{
    public const NAME = self::class;

    /**
     * @var string
     */
    private $errorMessage;

    /**
     * @var string
     */
    private $configurationFilePath;

    public function __construct(string $errorMessage, string $configurationFilePath)
    {
        $this->errorMessage = $errorMessage;
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * Returns the file that is misconfigured.
     */
    public function getConfigurationFilePath(): string
    {
        return $this->configurationFilePath;
    }

    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
