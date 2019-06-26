<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * @group module
 * @package Unit\Core
 */
class ModuleValidatorFactoryTest extends \OxidTestCase
{
    public function testModuleValidatorReturnInterface()
    {
        $oModuleValidatorFactory = oxNew('oxModuleValidatorFactory');
        $this->assertInstanceOf('oxIModuleValidator', $oModuleValidatorFactory->getModuleMetadataValidator());
    }

    public function testModuleValidatorReturnCorrectInterfaceForMetadata()
    {
        $oModuleValidatorFactory = oxNew('oxModuleValidatorFactory');
        $this->assertInstanceOf('oxModuleMetadataValidator', $oModuleValidatorFactory->getModuleMetadataValidator());
    }
}
