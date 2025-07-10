<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Model;

use OxidEsales\Eshop\Application\Model\Country;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Exception\InputException;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserTest extends IntegrationTestCase
{
    private bool $onlineVatIdCheckDisabled;
    private string $countryId = 'test_country_id';
    private string $validVatId = 'DE12345';
    private string $invalidVatId = 'DD12345';
    private User $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->onlineVatIdCheckDisabled = (bool) Registry::getConfig()->getConfigParam('blVatIdCheckDisabled');
        Registry::getConfig()->setConfigParam('blVatIdCheckDisabled', true);
        $this->user = new User();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        Registry::getConfig()->setConfigParam('blVatIdCheckDisabled', $this->onlineVatIdCheckDisabled);
    }

    public function testCheckValuesValidationPassesForValidData(): void
    {
        $this->createCountryWithVatPrefix();

        $invoiceAddress = $this->getValidInvoiceAddress();

        $this->user->checkValues('test@test.com', 'secretPassword', 'secretPassword', $invoiceAddress, []);

        $this->assertEmpty(Registry::getInputValidator()->getFieldValidationErrors());
    }

    public function testCheckValuesThrowsExceptionForMissingCountryVatPrefix(): void
    {
        $this->createCountryWithoutVatPrefix();

        $invoiceAddress = $this->getValidInvoiceAddress();

        $this->expectException(InputException::class);
        $this->expectExceptionMessage(
            Registry::getLang()->translateString('VAT_MESSAGE_MISSING_COUNTRY_PREFIX')
        );

        $this->user->checkValues('test@test.com', 'secretPassword', 'secretPassword', $invoiceAddress, []);
    }

    public function testCheckValuesThrowsExceptionForInvalidVatId(): void
    {
        $this->createCountryWithVatPrefix();

        $invoiceAddress = $this->getValidInvoiceAddress();
        $invoiceAddress['oxuser__oxustid'] = $this->invalidVatId;

        $this->expectException(InputException::class);
        $this->expectExceptionMessage(
            Registry::getLang()->translateString('VAT_MESSAGE_ID_NOT_VALID')
        );

        $this->user->checkValues('test@test.com', 'secretPassword', 'secretPassword', $invoiceAddress, []);
    }

    private function createCountryWithVatPrefix(): void
    {
        $country = new Country();
        $country->setId($this->countryId);
        $country->oxcountry__oxactive = new Field(1);
        $country->oxcountry__oxvatstatus = new Field(1);
        $country->oxcountry__oxvatinprefix = new Field('DE');
        $country->save();
    }

    private function createCountryWithoutVatPrefix()
    {
        $country = new Country();
        $country->setId($this->countryId);
        $country->oxcountry__oxactive = new Field(1);
        $country->oxcountry__oxvatstatus = new Field(1);
        $country->save();
    }

    private function getValidInvoiceAddress(): array
    {
        return [
            'oxuser__oxfname' => 'John',
            'oxuser__oxlname' => 'Doe',
            'oxuser__oxaddinfo' => 'Test Address Info',
            'oxuser__oxstreet' => 'Test Street',
            'oxuser__oxstreetnr' => '123',
            'oxuser__oxzip' => '12345',
            'oxuser__oxcity' => 'Testville',
            'oxuser__oxcountryid' => $this->countryId,
            'oxuser__oxustid' => $this->validVatId,
            'oxuser__oxcompany' => 'OXID eSales',
            'oxuser__oxfon' => '0123456789',
        ];
    }
}
