<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * License key managing class.
 */
class SerialCeTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    /**
     * This test makes sure \OxidEsales\Eshop\Core\Serial class does not exist in CE edition
     */
    public function testOxSerialClassDoesNotExist()
    {
        if ($this->getTestConfig()->getShopEdition() != 'CE') {
            $this->markTestSkipped('This test is for Community edition only.');
        }

        if (class_exists(\OxidEsales\Eshop\Core\Serial::class)) {
            $this->fail("Serial class should not be included in CE eddition!!");
        }
    }
}
