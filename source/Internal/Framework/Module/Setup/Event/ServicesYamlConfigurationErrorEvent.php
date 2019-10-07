<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * This event is dispatched when there are not loadable service classes
 * found in a services.yaml file.
 */
class ServicesYamlConfigurationErrorEvent extends Event
{
    const NAME = self::class;

    /**
     * @var string $errorMessage
     */
    private $errorMessage;

    /**
     * @var string $configurationFilePath
     */
    private $configurationFilePath;

    /**
     * @param int    $errorLevel
     * @param string $errorMessage
     * @param string $configurationFilePath
     */
    public function __construct(string $errorMessage, string $configurationFilePath)
    {
        $this->errorMessage = $errorMessage;
        $this->configurationFilePath = $configurationFilePath;
    }

    /**
     * Returns the file that is misconfigured
     *
     * @return string
     */
    public function getConfigurationFilePath(): string
    {
        return $this->configurationFilePath;
    }

    /**
     * @return string
     */
    public function getErrorMessage(): string
    {
        return $this->errorMessage;
    }
}
