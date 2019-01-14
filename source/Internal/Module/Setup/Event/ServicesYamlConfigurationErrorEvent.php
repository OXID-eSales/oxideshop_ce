<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Setup\Event;

use OxidEsales\EshopCommunity\Internal\Application\Events\ConfigurationErrorEvent;

/**
 * Class ServicesYamlConfigurationErrorEvent
 *
 * This event is dispatched when there are not loadable service classes
 * found in a services.yaml file.
 *
 * @package OxidEsales\EshopCommunity\Internal\ProjectDIConfig\Event
 */
class ServicesYamlConfigurationErrorEvent extends ConfigurationErrorEvent
{
    const NAME = self::class;

    /**
     * ServicesYamlConfigurationErrorEvent constructor.
     *
     * @param string $configurationFilePath
     */
    public function __construct($configurationFilePath)
    {
        parent::__construct(
            self::ERROR_LEVEL_ERROR,
            'There are undefined classes in the config.yaml file',
            $configurationFilePath
        );
    }
}
