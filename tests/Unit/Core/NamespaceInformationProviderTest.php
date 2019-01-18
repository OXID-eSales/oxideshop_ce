<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * Test \OxidEsales\Eshop\Core\EditionSelector.
 * Class NamespaceInformationProviderTest
 *
 * @package OxidEsales\EshopCommunity\Tests\Unit\Core
 */
class NamespaceInformationProviderTest extends UnitTestCase
{
    /**
     * Test getter for shop edition namespaces.
     */
    public function testGetShopEditionNamespaces()
    {
        $expected = ['CE' => 'OxidEsales\EshopCommunity\\',
                     'PE' => 'OxidEsales\EshopProfessional\\',
                     'EE' => 'OxidEsales\EshopEnterprise\\'];
        $this->assertEquals($expected, \OxidEsales\Eshop\Core\NamespaceInformationProvider::getShopEditionNamespaces());
    }

    /**
     * Test getter for Unified Namespace.
     */
    public function testGetUnifiedNamespace()
    {
        $this->assertEquals('OxidEsales\Eshop\\', \OxidEsales\Eshop\Core\NamespaceInformationProvider::getUnifiedNamespace());
    }

    /**
     * Test method isNamespacedClass.
     */
    public function testIsNamespacedClass()
    {
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass(\OxidEsales\Eshop\Application\Model\Article::class));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::isNamespacedClass('oxArticle'));
    }

    /**
     * Test method classBelongsToShopUnifiedNamespace.
     */
    public function testClassBelongsToShopUnifiedNamespace()
    {
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace('oxArticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace('oxarticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace(\OxidEsales\EshopCommunity\Application\Model\Article::class));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace('OxidEsales\EshopCommunity\Application\Model\Article'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace('OxidEsales\Eshop\Application\Model\Article'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopUnifiedNamespace(\OxidEsales\Eshop\Application\Model\Article::class));
    }

    /**
     * Test method classBelongsToShopEditionNamespace.
     */
    public function testClassBelongsToShopEditionNamespace()
    {
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('oxArticle'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('oxarticle'));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace(\OxidEsales\EshopCommunity\Application\Model\Article::class));
        $this->assertTrue(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('OxidEsales\EshopCommunity\Application\Model\Article'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace('OxidEsales\Eshop\Application\Model\Article'));
        $this->assertFalse(\OxidEsales\Eshop\Core\NamespaceInformationProvider::classBelongsToShopEditionNamespace(\OxidEsales\Eshop\Application\Model\Article::class));
    }
}
