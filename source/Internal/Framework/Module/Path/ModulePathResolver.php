<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Path;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use Webmozart\PathUtil\Path;

class ModulePathResolver implements ModulePathResolverInterface
{
    /**
     * @var ModuleConfigurationDaoInterface
     */
    private $moduleConfigurationDao;

    /**
     * @var BasicContextInterface
     */
    private $context;

    public function __construct(ModuleConfigurationDaoInterface $moduleConfiguration, BasicContextInterface $context)
    {
        $this->moduleConfigurationDao = $moduleConfiguration;
        $this->context = $context;
    }

    /**
     * This method does not validate if the path returned exists. It returns more or less the value from the project
     * configuration.
     */
    public function getFullModulePathFromConfiguration(string $moduleId, int $shopId): string
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        return Path::join($this->context->getShopRootPath(), $moduleConfiguration->getModuleSource());
    }
}
