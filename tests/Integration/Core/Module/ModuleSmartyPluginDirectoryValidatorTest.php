<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\Eshop\Core\Module\Module;
use OxidEsales\Eshop\Core\Module\ModuleSmartyPluginDirectories;
use OxidEsales\Eshop\Core\Module\ModuleSmartyPluginDirectoryValidator;
use OxidEsales\TestingLibrary\UnitTestCase;

class ModuleSmartyPluginDirectoryValidatorTest extends UnitTestCase
{
    /**
     * @expectedException \OxidEsales\Eshop\Core\Exception\ModuleValidationException
     */
    public function testNonExistingDirectoriesValidation()
    {
        $directories = $this->getModuleSmartyPluginDirectories();
        $directories->add(['fakeDir'], 'id');
        ;

        $validator = oxNew(ModuleSmartyPluginDirectoryValidator::class);
        $validator->validate($directories);
    }

    public function testExistingDirectoriesValidation()
    {
        $directories = $this->getModuleSmartyPluginDirectories();
        $directories->add([__DIR__], 'id');

        $validator = oxNew(ModuleSmartyPluginDirectoryValidator::class);
        $validator->validate($directories);
    }

    private function getModuleSmartyPluginDirectories()
    {
        return oxNew(
            ModuleSmartyPluginDirectories::class,
            oxNew(Module::class)
        );
    }
}
