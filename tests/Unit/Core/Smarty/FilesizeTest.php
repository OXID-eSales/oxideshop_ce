<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Smarty;

use \oxRegistry;

$filePath = oxRegistry::getConfig()->getConfigParam('sCoreDir') . 'Smarty/Plugin/modifier.oxfilesize.php';
if (file_exists($filePath)) {
    require_once $filePath;
} else {
    require_once dirname(__FILE__) . '/../../../../source/Core/Smarty/Plugin/modifier.oxfilesize.php';
}


/**
 * Smarty modifier test case
 */
class FilesizeTest extends \OxidTestCase
{

    /**
     * Byte result test
     *
     * @return null
     */
    public function testOxFileSizeBytes()
    {
        $iSize = 1023;
        $sRes = smarty_modifier_oxfilesize($iSize);
        $this->assertEquals("1023 B", $sRes);
    }

    /**
     * KiloByte result test
     *
     * @return null
     */
    public function testOxFileSizeKiloBytes()
    {
        $iSize = 1025;
        $sRes = smarty_modifier_oxfilesize($iSize);
        $this->assertEquals("1.0 KB", $sRes);
    }

    /**
     * MegaByte result test
     *
     * @return null
     */
    public function testOxFileSizeMegaBytes()
    {
        $iSize = 1024 * 1024 * 1.1;
        $sRes = smarty_modifier_oxfilesize($iSize);

        $this->assertEquals("1.1 MB", $sRes);
    }

    /**
     * GigaByte result test
     *
     * @return null
     */
    public function testOxFileSizeGigaBytes()
    {
        $iSize = 1024 * 1024 * 1024 * 1.3;
        $sRes = smarty_modifier_oxfilesize($iSize);

        $this->assertEquals("1.3 GB", $sRes);
    }
}
