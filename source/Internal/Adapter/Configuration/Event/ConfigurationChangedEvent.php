<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Event;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConfigurationChangedEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\Adapter\Configuration\Event
 */
class ConfigurationChangedEvent extends Event
{
    const NAME = self::class;

    /**
     * Configuration variable that was changed.
     *
     * @var string
     */
    private $configurationVariable;

    /**
     * Constructor
     *
     * @param string $configurationVariable Config varname.
     */
    public function __construct(string $configurationVariable)
    {
        $this->configurationVariable = $configurationVariable;
    }

    /**
     * Getter for configuration variable name.
     *
     * @return string
     */
    public function getConfigurationVariable(): string
    {
        return $this->configurationVariable;
    }
}
