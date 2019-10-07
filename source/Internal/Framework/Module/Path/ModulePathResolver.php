<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Path;

use OxidEsales\EshopCommunity\Internal\Transition\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
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

    /**
     * @param ModuleConfigurationDaoInterface $moduleConfiguration
     * @param BasicContextInterface           $context
     */
    public function __construct(ModuleConfigurationDaoInterface $moduleConfiguration, BasicContextInterface $context)
    {
        $this->moduleConfigurationDao = $moduleConfiguration;
        $this->context = $context;
    }

    /**
     * This method does not validate if the path returned exists. It returns more or less the value from the project
     * configuration.
     *
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return string
     */
    public function getFullModulePathFromConfiguration(string $moduleId, int $shopId): string
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        return Path::join($this->context->getModulesPath(), $moduleConfiguration->getPath());
    }
}
