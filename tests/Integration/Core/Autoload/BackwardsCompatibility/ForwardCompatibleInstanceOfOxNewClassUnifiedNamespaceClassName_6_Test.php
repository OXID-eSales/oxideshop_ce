<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Autoload\BackwardsCompatibility;

class ForwardCompatibleInstanceOfOxNewClassUnifiedNamespaceClassName_6_Test extends \PHPUnit_Framework_TestCase
{

    /**
     * Test the backwards compatibility of class instances created with oxNew and the alias class name
     */
    public function testForwardCompatibleInstanceOfOxNewClassUnifiedNamespaceClassName()
    {
        $realClassName = \OxidEsales\EshopCommunity\Application\Model\Article::class;
        $unifiedNamespaceClassName = \OxidEsales\Eshop\Application\Model\Article::class;
        $backwardsCompatibleClassAlias = 'oxArticle';
        $message = 'Backwards compatible class name - CamelCase string';
        
        $object = oxNew($unifiedNamespaceClassName);

        $message = 'An object created with oxNew(\OxidEsales\Eshop\Application\Model\Article::class) is an instance of "oxArticle"';
        $this->assertInstanceOf($backwardsCompatibleClassAlias, $object, $message);

        $message = 'An object created with oxNew(\OxidEsales\Eshop\Application\Model\Article::class) is an instance of \OxidEsales\EshopCommunity\Application\Model\Article::class';
        $this->assertInstanceOf($realClassName, $object, $message);

        $message = 'An object created with oxNew(\OxidEsales\Eshop\Application\Model\Article::class) is an instance of \OxidEsales\Eshop\Application\Model\Article::class';
        $this->assertInstanceOf($unifiedNamespaceClassName, $object, $message);
    }
}
