<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Configuration\Module\Dao;

use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\EnvironmentConfiguration;
use OxidEsales\EshopCommunity\Internal\Configuration\Module\DataObject\ProjectConfiguration;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
class ProjectConfigurationDaoTest extends TestCase
{
    public function testProjectConfigurationSaving()
    {
        $projectConfigurationDao = $this
            ->getContainer()
            ->get(ProjectConfigurationDaoInterface::class);

        $projectConfiguration = new ProjectConfiguration();
        $projectConfiguration->setProjectName('testProject');
        $projectConfiguration->setEnvironmentConfiguration('dev', new EnvironmentConfiguration());

        $projectConfigurationDao->persistConfiguration($projectConfiguration);

        $this->assertEquals(
            $projectConfiguration,
            $projectConfigurationDao->getConfiguration()
        );
    }

    private function getContainer()
    {
        return ContainerFactory::getInstance()->getContainer();
    }
}
