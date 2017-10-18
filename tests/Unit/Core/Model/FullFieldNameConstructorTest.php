<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Model;

use OxidEsales\Eshop\Core\Model\FieldNameHelper;
use OxidEsales\TestingLibrary\UnitTestCase;

class FullFieldNameConstructorTest extends UnitTestCase
{
    public function testGetFullName()
    {
        $helper = oxNew(FieldNameHelper::class);
        $this->assertSame(
            ['fieldname', 'tablename__fieldname', 'fieldname2', 'tablename__fieldname2'],
            $helper->getFullFieldNames('tableName', ['fieldName', 'fieldName2'])
        );
    }

    public function testGetFullNameWhenFieldContainsTableName()
    {
        $helper = oxNew(FieldNameHelper::class);
        $this->assertSame(
            ['fieldname', 'tablename__fieldname', 'fieldname2', 'tablename__fieldname2'],
            $helper->getFullFieldNames('tableName', ['tablename__fieldName', 'fieldName2'])
        );
    }
}
