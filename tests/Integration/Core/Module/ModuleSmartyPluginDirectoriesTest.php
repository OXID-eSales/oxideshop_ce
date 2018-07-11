<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Module;

use OxidEsales\EshopCommunity\Tests\Integration\Modules\Environment;
use OxidEsales\Eshop\Core\UtilsView;

/**
 * Class ModuleSmartyPluginDirectoryTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Integration\Core
 */
class ModuleSmartyPluginDirectoriesTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * Smarty should know about the smarty plugin directories of the modules being activated.
     */
    public function testModuleSmartyPluginDirectoryIsIncludedOnModuleActivation()
    {
        $modules = ['with_metadata_v21'];
        $oEnvironment = new Environment();
        $oEnvironment->prepare($modules);
        $oEnvironment->activateModules($modules);

        $utilsView = oxNew(UtilsView::class);
        $smarty = $utilsView->getSmarty(true);

        $this->assertTrue(
            $this->isPathInSmartyDirectories($smarty, 'Smarty/PluginDirectory1WithMetadataVersion21')
        );

        $this->assertTrue(
            $this->isPathInSmartyDirectories($smarty, 'Smarty/PluginDirectory2WithMetadataVersion21')
        );
    }

    private function isPathInSmartyDirectories($smarty, $path)
    {
        foreach ($smarty->plugins_dir as $directory) {
            if (strpos($directory, $path)) {
                return true;
            }
        }

        return false;
    }
}
