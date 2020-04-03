<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\MetaData\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\DataObject\ModuleConfiguration;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\ModuleConfigurationDao;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\DataMapper\MetaDataToModuleConfigurationDataMapperInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\MetaData\Dao\MetaDataProviderInterface;
use PHPUnit\Framework\TestCase;

class ModuleConfigurationDaoTest extends TestCase
{
    /**
     * @var string
     */
    private $metadataFileName = 'metadata.php';

    public function testGet(): void
    {
        $path = 'packagePath';

        $metaDataProvider = $this->prophesize(MetaDataProviderInterface::class);
        $metaDataProvider->getData($this->getMetadataFilePath($path))->willReturn([]);

        $metaDataToModuleConfigurationDataMapper = $this
            ->prophesize(MetaDataToModuleConfigurationDataMapperInterface::class);
        $metaDataToModuleConfigurationDataMapper->fromData([])
            ->willReturn($this->prophesize(ModuleConfiguration::class));


        $moduleConfigurationDao = new ModuleConfigurationDao(
            $metaDataProvider->reveal(),
            $metaDataToModuleConfigurationDataMapper->reveal()
        );

        $this->assertInstanceOf(
            ModuleConfiguration::class,
            $moduleConfigurationDao->get($path)
        );
    }

    /**
     * @param string $moduleFullPath
     *
     * @return string
     */
    private function getMetadataFilePath(string $moduleFullPath): string
    {
        return $moduleFullPath . DIRECTORY_SEPARATOR . $this->metadataFileName;
    }

}
