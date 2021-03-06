<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

use OxidEsales\Facts\Facts;

class ForwardCompatibleInstanceOfOxNewClassRealClassName_1_Test extends \OxidEsales\TestingLibrary\UnitTestCase
{

    /**
     * Test the backwards compatibility of class instances created with oxNew and the alias class name
     */
    public function testForwardCompatibleInstanceOfOxNewClassRealClassName()
    {
        if ('CE' !== (new Facts())->getEdition()) {
            // $this->markTestSkipped(
            //    'This test will fail on Travis and CI as it MUST run in an own PHP process, which is not possible.'
            // );
        }

        $realClassName = \OxidEsales\EshopCommunity\Application\Model\Article::class;
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $backwardsCompatibleClassAlias = \oxArticle::class;

        $object = oxNew($realClassName);

        $message = 'An object created with oxNew(\OxidEsales\EshopCommunity\Application\Model\Article::class) is not an instance of "\oxArticle::class"';
        $this->assertNotInstanceOf($backwardsCompatibleClassAlias, $object, $message);

        $message = 'An object created with oxNew(\OxidEsales\EshopCommunity\Application\Model\Article::class) is an instance of \OxidEsales\EshopCommunity\Application\Model\Article::class';
        $this->assertInstanceOf($realClassName, $object, $message);

        $message = 'An object created with oxNew(\OxidEsales\EshopCommunity\Application\Model\Article::class) is not an instance of \OxidEsales\Eshop\Application\Model\Article::class';
        $this->assertNotInstanceOf($unifiedNamespaceClassName, $object, $message);
    }
}
