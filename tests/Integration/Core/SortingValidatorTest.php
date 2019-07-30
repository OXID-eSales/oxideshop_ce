<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core;

use OxidEsales\EshopCommunity\Core\SortingValidator;
use OxidEsales\TestingLibrary\UnitTestCase;

/**
 * @covers \OxidEsales\EshopCommunity\Core\SortingValidator
 */
class SortingValidatorTest extends UnitTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->setAllowedSortingColumns();
    }

    public function testWhenSortOrderIsValid()
    {
        $sortingValidator = new SortingValidator();
        $sortingOrders = $sortingValidator->getSortingOrders();
        $this->assertTrue($sortingValidator->isValid('testColumn1', $sortingOrders[0]));
    }

    public function testWhenSortColumnIsValid()
    {
        $sortingValidator = new SortingValidator();
        $sortingOrders = $sortingValidator->getSortingOrders();
        $this->assertTrue($sortingValidator->isValid('testColumn1', $sortingOrders[0]));
    }

    public function invalidValuesDataProvider()
    {
        return [
            ['invalid_value'],
            [''],
            [null],
        ];
    }

    /**
     * @param $value
     * @dataProvider invalidValuesDataProvider
     */
    public function testWhenSortOrderInvalid($value)
    {
        $sortingValidator = new SortingValidator();
        $this->assertFalse($sortingValidator->isValid('testColumn1', $value));
    }

    /**
     * @param $value
     * @dataProvider invalidValuesDataProvider
     */
    public function testWhenSortColumnInvalid($value)
    {
        $sortingValidator = new SortingValidator();
        $sortingOrders = $sortingValidator->getSortingOrders();
        $this->assertFalse($sortingValidator->isValid($value, $sortingOrders[0]));
    }

    private function setAllowedSortingColumns()
    {
        $this->setConfigParam(
            'aSortCols',
            [
                'testColumn1',
                'testColumn2',
            ]
        );
    }
}
