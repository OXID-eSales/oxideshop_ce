<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardsCompatibleInstanceOfNewClassRealClassName_2_Test extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Test the backwards compatibility of class instances created with oxNew and the alias class name
     */
    public function testForwardsCompatibleInstanceOfNewClassRealClassName()
    {
        if ('CE' !== $this->getConfig()->getEdition()) {
            //$this->markTestSkipped(
            //    'This test will fail on Travis and CI as it MUST run in an own PHP process, which is not possible.'
            //);
        }

        $realClassName = \OxidEsales\EshopCommunity\Application\Model\Article::class;
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $backwardsCompatibleClassAlias = 'oxarticle';

        $object = new $realClassName();

        $message = 'An object created with new \OxidEsales\EshopCommunity\Application\Model\Article() is not an instance of "oxarticle"';
        $this->assertNotInstanceOf($backwardsCompatibleClassAlias, $object, $message);

        $message = 'An object created with new \OxidEsales\EshopCommunity\Application\Model\Article() is an instance of \OxidEsales\EshopCommunity\Application\Model\Article::class';
        $this->assertInstanceOf($realClassName, $object, $message);

        $message = 'An object created with new \OxidEsales\EshopCommunity\Application\Model\Article() is not an instance of \OxidEsales\Eshop\Application\Model\Article::class';
        $this->assertNotInstanceOf($unifiedNamespaceClassName, $object, $message);
    }
}
