<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Install;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ProjectConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ShopConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Install\Service\ProjectConfigurationGenerator;
use OxidEsales\EshopCommunity\Internal\Transition\Utility\ContextInterface;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

/**
 * @internal
 */
class ProjectConfigurationGeneratorTest extends TestCase
{
    private $shops = [1, 2, 3];

    public function testGenerateDefaultConfiguration()
    {
        $projectConfigurationDao = $this->getMockBuilder(ProjectConfigurationDaoInterface::class)->getMock();
        $projectConfigurationDao
            ->expects($this->once())
            ->method('save')
            ->with($this->getExpectedDefaultProjectConfiguration($this->shops));

        $context = $this->getContext();

        $generator = new ProjectConfigurationGenerator($projectConfigurationDao, $context);

        $generator->generate();
    }

    private function getExpectedDefaultProjectConfiguration(array $shops): ProjectConfiguration
    {
        $projectConfiguration = new ProjectConfiguration();

        foreach ($shops as $shopId) {
            $projectConfiguration->addShopConfiguration($shopId, new ShopConfiguration());
        }

        return $projectConfiguration;
    }

    /**
     * @return ContextInterface | MockObject
     */
    private function getContext(): MockObject
    {
        $context = $this->getMockBuilder(ContextInterface::class)->getMock();
        $context->method('getAllShopIds')->willReturn($this->shops);

        return $context;
    }
}
