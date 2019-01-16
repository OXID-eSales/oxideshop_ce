<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Storage\ArrayStorageInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\Dao\ProjectConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapper;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ProjectConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataMapper\ShopConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ProjectConfiguration;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ShopConfiguration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\NodeInterface;

/**
 * @internal
 */
class ProjectConfigurationDaoTest extends TestCase
{
    /**
     * @expectedException \OxidEsales\EshopCommunity\Internal\Module\Configuration\Exception\ProjectConfigurationIsEmptyException
     */
    public function testProjectConfigurationGetterThrowsExceptionIfStorageIsEmpty()
    {
        $arrayStorage = $this
            ->getMockBuilder(ArrayStorageInterface::class)
            ->getMock();

        $arrayStorage
            ->method('get')
            ->willReturn([]);

        $projectConfigurationDao = new ProjectConfigurationDao(
            $arrayStorage,
            $this->getProjectConfigurationDataMapper(),
            $this->getMockBuilder(NodeInterface::class)->getMock()
        );

        $projectConfigurationDao->getConfiguration();
    }

    public function testProjectConfigurationGetter()
    {
        $projectConfigurationData = [
            'environments'  => [
                'dev' => [
                    'shops' => [],
                ],
                'prod' => [
                    'shops' => [],
                ],
            ],
        ];

        $projectConfigurationDataMapper = $this->getProjectConfigurationDataMapper();

        $arrayStorage = $this
            ->getMockBuilder(ArrayStorageInterface::class)
            ->getMock();

        $arrayStorage
            ->method('get')
            ->willReturn($projectConfigurationData);

        $node = $this->getMockBuilder(NodeInterface::class)->getMock();
        $node
            ->method('normalize')
            ->willReturn($projectConfigurationData);

        $projectConfigurationDao = new ProjectConfigurationDao(
            $arrayStorage,
            $projectConfigurationDataMapper,
            $node
        );

        $this->assertEquals(
            $projectConfigurationDataMapper->fromData($projectConfigurationData),
            $projectConfigurationDao->getConfiguration()
        );
    }

    public function testProjectConfigurationSaving()
    {
        $projectConfigurationDataMapper = $this->getProjectConfigurationDataMapper();

        $arrayStorage = $this
            ->getMockBuilder(ArrayStorageInterface::class)
            ->getMock();

        $arrayStorage
            ->expects($this->atLeastOnce())
            ->method('save');

        $node = $this->getMockBuilder(NodeInterface::class)->getMock();
        $node
            ->method('normalize')
            ->willReturn([]);

        $projectConfigurationDao = new ProjectConfigurationDao(
            $arrayStorage,
            $projectConfigurationDataMapper,
            $node
        );

        $projectConfigurationDao->persistConfiguration(new ProjectConfiguration());
    }

    private function getProjectConfigurationDataMapper(): ProjectConfigurationDataMapperInterface
    {
        $shopConfigurationDataMapper = $this
            ->getMockBuilder(ShopConfigurationDataMapperInterface::class)
            ->getMock();

        $shopConfigurationDataMapper
            ->method('fromData')
            ->willReturn(new ShopConfiguration());

        return new ProjectConfigurationDataMapper($shopConfigurationDataMapper);
    }
}
