<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

class NoarticleexceptionTest extends \OxidEsales\TestingLibrary\UnitTestCase
{

    // We check on class name and message only - rest is not checked yet
    public function testGetString()
    {
        $oTestObject = oxNew(\OxidEsales\Eshop\Core\Exception\NoArticleException::class);
        $this->assertEquals(\OxidEsales\Eshop\Core\Exception\NoArticleException::class, get_class($oTestObject));
        $sStringOut = $oTestObject->getString(); // (string)$oTestObject; is not PHP 5.2 compatible (__toString() for string convertion is PHP >= 5.2
        $this->assertContains('NoArticleException', $sStringOut);
    }

    /**
     * Test type getter.
     */
    public function testGetType()
    {
        $class = 'oxNoArticleException';
        $exception = oxNew($class);
        $this->assertSame($class, $exception->getType());
    }
}
