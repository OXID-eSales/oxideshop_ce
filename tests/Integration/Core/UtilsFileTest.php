<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\Eshop\Core\UtilsFile;
use OxidEsales\TestingLibrary\UnitTestCase;

class UtilsFileTest extends UnitTestCase
{
    /**
     * @group slow-tests
     */
    public function testUrlValidate(): void
    {
        $utilsFile = oxNew(UtilsFile::class);
        $this->assertFalse($utilsFile->urlValidate("test/notvalid"));
        $this->assertFalse($utilsFile->urlValidate("http://www.oxid_non_existing_page.com"));

        $this->activateTheme('azure');
        $shopUrl = $this->getTestConfig()->getShopUrl();
        $this->assertTrue($utilsFile->urlValidate($shopUrl . "?param=value"));
    }
}
