<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardsCompatibleInstanceOfNewClassUnifiedNamespaceClassName_6_Test extends \PHPUnit\Framework\TestCase
{

    /**
     * Test the backwards compatibility of class instances created with oxNew and the alias class name
     */
    public function testForwardsCompatibleInstanceOfNewClassUnifiedNamespaceClassName()
    {
        $realClassName = \OxidEsales\EshopCommunity\Application\Model\Article::class;
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $backwardsCompatibleClassAlias = 'oxArticle';

        $object = new $unifiedNamespaceClassName();

        $message = 'An object created with new \OxidEsales\Eshop\Application\Model\Article() is an instance of "oxArticle"';
        $this->assertInstanceOf($backwardsCompatibleClassAlias, $object, $message);

        $message = 'An object created with new \OxidEsales\Eshop\Application\Model\Article() is an instance of \OxidEsales\EshopCommunity\Application\Model\Article::class';
        $this->assertInstanceOf($realClassName, $object, $message);

        $message = 'An object created with new \OxidEsales\Eshop\Application\Model\Article() is an instance of \OxidEsales\Eshop\Application\Model\Article::class';
        $this->assertInstanceOf($unifiedNamespaceClassName, $object, $message);
    }
}
