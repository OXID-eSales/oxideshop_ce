<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

class Object2CategoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Tests setter and getter of product id
     */
    public function testSetGetProductId()
    {
        $oObject2Category = oxNew('oxObject2Category');
        $oObject2Category->setProductId('_testProduct');
        $this->assertSame('_testProduct', $oObject2Category->getProductId());
        $this->assertSame('_testProduct', $oObject2Category->oxobject2category__oxobjectid->value);
    }

    /**
     * Tests setter and getter of category id
     */
    public function testSetGetCategoryId()
    {
        $oObject2Category = oxNew('oxObject2Category');
        $oObject2Category->setCategoryId('_testProduct');
        $this->assertSame('_testProduct', $oObject2Category->getCategoryId());
        $this->assertSame('_testProduct', $oObject2Category->oxobject2category__oxcatnid->value);
    }
}
