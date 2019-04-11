<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\Path;

use OxidEsales\EshopCommunity\Internal\Application\Utility\BasicContextInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ModuleConfigurationDaoInterface;
use Webmozart\PathUtil\Path;

/**
 * @internal
 */
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
     * @param string $moduleId
     * @param int    $shopId
     *
     * @return string
     */
    public function getFullModulePath(string $moduleId, int $shopId): string
    {
        $moduleConfiguration = $this->moduleConfigurationDao->get($moduleId, $shopId);

        return Path::join($this->context->getModulesPath(), $moduleConfiguration->getPath());
    }
}
