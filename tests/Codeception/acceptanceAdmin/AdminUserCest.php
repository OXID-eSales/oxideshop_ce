<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use OxidEsales\Codeception\Admin\DataObject\AdminUser;
use OxidEsales\Codeception\Admin\DataObject\AdminUserAddresses;
use OxidEsales\Codeception\Admin\Users;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminUserCest
{
    /**
     * @param AcceptanceAdminTester $I
     */
    public function testUserMainInfo(AcceptanceAdminTester $I): void
    {
        $adminPanel = $I->loginAdmin();
        // Main tab
        $adminUsersPage = $adminPanel->openUsers();

        $this->createNewAdminUser($I, $adminUsersPage);
        $this->updateAdminUser($I, $adminUsersPage);

        // Extended tab
        $adminUsersPage->openExtendedTab();

        $I->assertEquals(
            "Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331",
            $I->clearString($I->grabTextFrom($adminUsersPage->extendedTabUserAddress))
        );

        // History Tab
        $adminUsersPage->openHistoryTab();
        $I->dontSeeOptionIsSelected($adminUsersPage->historyTabRemarkSelect,"");

        $adminUsersPage->createNewRemark("new note_šÄßüл");

        $I->selectOption($adminUsersPage->historyTabRemarkSelect,"0");
        $I->assertEquals("new note_šÄßüл", $adminUsersPage->getHistoryRemarkTextValue());

        $adminUsersPage->deleteRemark();

        $I->dontSeeOptionIsSelected($adminUsersPage->historyTabRemarkSelect,"");

        $I->selectOption($adminUsersPage->historyTabRemarkSelect,"0");

        $I->assertNotEquals("new note_šÄßüл", $adminUsersPage->getHistoryRemarkTextValue());

        $adminUsersPage->openProductsTab();
        $adminUsersPage->openPaymentTab();

        //checking if created user can be found
        $I->selectListFrame();
        $I->fillField("where[oxuser][oxusername]", "example00");
        $I->click("submitit");
        $I->assertEquals("example00@oxid-esa...", $I->grabTextFrom("//tr[@id='row.1']/td[3]"));
        $I->dontSeeElement("//tr[@id='row.2']/td[3]");
    }

    public function testUserAddresses(AcceptanceAdminTester $I): void
    {
        $this->createAdminTestUser($I);

        $adminPanel = $I->loginAdmin();

        $adminUsersPage = $adminPanel->openUsers();
        $I->selectListFrame();
        $adminUsersPage->find("where[oxuser][oxusername]", "example00@oxid-esales.dev");
        $adminUsersPage->openAddressesTab();

        $I->seeOptionIsSelected($adminUsersPage->addressesTabAddressSelect, "-");

        $adminUserAddress = new AdminUserAddresses();
        $adminUserAddress->setTitle("Mr");
        $adminUserAddress->setFirstName("shipping name_šÄßüл");
        $adminUserAddress->setLastName("shipping surname_šÄßüл");
        $adminUserAddress->setCompany("shipping company_šÄßüл");
        $adminUserAddress->setStreet("shipping street_šÄßüл");
        $adminUserAddress->setStreetNumber("1");
        $adminUserAddress->setZip("1000");
        $adminUserAddress->setCity("shipping city_šÄßüл");
        $adminUserAddress->setAdditionalInfo("shipping additional info_šÄßüл");
        $adminUserAddress->setCountryId("Germany");
        $adminUserAddress->setPhone("7778788");
        $adminUserAddress->setFax("8887877");
        $adminUsersPage->createNewAddress($adminUserAddress);

        $I->seeOptionIsSelected($adminUsersPage->addressesTabAddressSelect, "shipping name_šÄßüл shipping surname_šÄßüл, shipping street_šÄßüл, shipping city_šÄßüл");
        $I->seeOptionIsSelected($adminUsersPage->addressTitleField, "Mr");
        $I->assertEquals("shipping name_šÄßüл",$I->grabValueFrom( $adminUsersPage->addressFirstNameField));
        $I->assertEquals("shipping surname_šÄßüл", $I->grabValueFrom($adminUsersPage->addressLastNameField));
        $I->assertEquals("shipping company_šÄßüл", $I->grabValueFrom($adminUsersPage->addressCompanyField));
        $I->assertEquals("shipping street_šÄßüл", $I->grabValueFrom($adminUsersPage->addressStreetField));
        $I->assertEquals("1", $I->grabValueFrom($adminUsersPage->addressStreetNumberField));
        $I->assertEquals("1000", $I->grabValueFrom($adminUsersPage->addressZipCodeField));
        $I->assertEquals("shipping city_šÄßüл", $I->grabValueFrom($adminUsersPage->addressCityField));
        $I->seeOptionIsSelected($adminUsersPage->addressCountryIdField, "Germany");
        $I->assertEquals("7778788", $I->grabValueFrom($adminUsersPage->addressPhoneField));
        $I->assertEquals("8887877", $I->grabValueFrom($adminUsersPage->addressFaxField));

        $adminUserAddress = new AdminUserAddresses();
        $adminUserAddress->setTitle("Mrs");
        $adminUserAddress->setFirstName("name2");
        $adminUserAddress->setLastName("last name 2");
        $adminUserAddress->setCompany("company 2");
        $adminUserAddress->setStreet("street2");
        $adminUserAddress->setStreetNumber("12");
        $adminUserAddress->setZip("2001");
        $adminUserAddress->setCity("city2");
        $adminUserAddress->setAdditionalInfo("additional info2");
        $adminUserAddress->setCountryId("United States");
        $adminUserAddress->setPhone("999666");
        $adminUserAddress->setFax("666999");
        $adminUsersPage->createNewAddress($adminUserAddress);

        //check by choosing select show correct values
        $I->selectOption($adminUsersPage->addressesTabAddressSelect, "-");

        $I->selectOption($adminUsersPage->addressesTabAddressSelect, "name2 last name 2, street2, city2");
        $I->seeOptionIsSelected($adminUsersPage->addressTitleField,"Mrs");
        $I->assertEquals("name2", $I->grabValueFrom($adminUsersPage->addressFirstNameField));
        $I->assertEquals("last name 2", $I->grabValueFrom($adminUsersPage->addressLastNameField));
        $I->assertEquals("company 2", $I->grabValueFrom($adminUsersPage->addressCompanyField));
        $I->assertEquals("street2", $I->grabValueFrom($adminUsersPage->addressStreetField));
        $I->assertEquals("12", $I->grabValueFrom($adminUsersPage->addressStreetNumberField));
        $I->assertEquals("2001", $I->grabValueFrom($adminUsersPage->addressZipCodeField));
        $I->assertEquals("city2", $I->grabValueFrom($adminUsersPage->addressCityField));
        $I->assertEquals("additional info2", $I->grabValueFrom($adminUsersPage->addressAdditonalInformationField));
        $I->seeOptionIsSelected($adminUsersPage->addressCountryIdField,"United States");
        $I->assertEquals("999666", $I->grabValueFrom($adminUsersPage->addressPhoneField));
        $I->assertEquals("666999", $I->grabValueFrom($adminUsersPage->addressFaxField));

        $I->selectOption($adminUsersPage->addressesTabAddressSelect, "shipping name_šÄßüл shipping surname_šÄßüл, shipping street_šÄßüл, shipping city_šÄßüл");
        $I->seeOptionIsSelected($adminUsersPage->addressTitleField,"Mr");
        $I->assertEquals("shipping name_šÄßüл", $I->grabValueFrom($adminUsersPage->addressFirstNameField));
        $I->assertEquals("shipping surname_šÄßüл", $I->grabValueFrom($adminUsersPage->addressLastNameField));
        $I->assertEquals("shipping company_šÄßüл", $I->grabValueFrom($adminUsersPage->addressCompanyField));
        $I->assertEquals("shipping street_šÄßüл", $I->grabValueFrom($adminUsersPage->addressStreetField));
        $I->assertEquals("1", $I->grabValueFrom($adminUsersPage->addressStreetNumberField));
        $I->assertEquals("1000", $I->grabValueFrom($adminUsersPage->addressZipCodeField));
        $I->assertEquals("shipping city_šÄßüл", $I->grabValueFrom($adminUsersPage->addressCityField));
        $I->assertEquals("shipping additional info_šÄßüл", $I->grabValueFrom($adminUsersPage->addressAdditonalInformationField));
        $I->seeOptionIsSelected($adminUsersPage->addressCountryIdField,"Germany");
        $I->assertEquals("7778788", $I->grabValueFrom($adminUsersPage->addressPhoneField));
        $I->assertEquals("8887877", $I->grabValueFrom($adminUsersPage->addressFaxField));

        // delete addresses
        $adminUsersPage->deleteSelectedAddress();

        $I->seeOptionIsSelected($adminUsersPage->addressesTabAddressSelect, "-");
        $I->selectOption($adminUsersPage->addressesTabAddressSelect, "name2 last name 2, street2, city2");

        $adminUsersPage->deleteSelectedAddress();

        $I->seeOptionIsSelected($adminUsersPage->addressesTabAddressSelect, "-");
        $I->seeOptionIsSelected($adminUsersPage->addressTitleField, "Mr");
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressFirstNameField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressLastNameField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressCompanyField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressStreetField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressStreetNumberField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressZipCodeField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressCityField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressAdditonalInformationField));
        $I->seeOptionIsSelected($adminUsersPage->addressCountryIdField, "");
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressPhoneField));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->addressFaxField));
    }

    private function createAdminTestUser(AcceptanceAdminTester $I): void
    {
        $I->haveInDatabase(
            'oxuser',
            [
                'OXID'        => "kdiruuc",
                'OXACTIVE'    => 0,
                'OXRIGHTS'    => 'malladmin',
                'OXSHOPID'    => 1,
                'OXUSERNAME'  => 'example00@oxid-esales.dev',
                'OXPASSWORD'  => '1397d0b4392f452a5bd058891c9b255e',
                'OXPASSSALT'  => '3032396331663033316535343361356231363666653666316533376235353830',
                'OXCUSTNR'    => 121,
                'OXUSTID'     => '111222',
                'OXCOMPANY'   => 'company1',
                'OXFNAME'     => 'Name1',
                'OXLNAME'     => 'Surname1',
                'OXSTREET'    => 'street1',
                'OXSTREETNR'  => '11',
                'OXADDINFO'   => 'additional info1',
                'OXCITY'      => 'City11',
                'OXCOUNTRYID' => 'a7c40f632e04633c9.47194042',
                'OXSTATEID'   => 'BE',
                'OXZIP'       => '30001',
                'OXFON'       => '1112223331',
                'OXFAX'       => '2223334441',
                'OXSAL'       => 'MR',
                'OXBONI'      => 1000,
                'OXCREATE'    => '2010-02-05 10:22:37',
                'OXREGISTER'  => '2010-02-05 10:22:48',
                'OXPRIVFON'   => '5554445551',
                'OXMOBFON'    => '6665556661',
                'OXBIRTHDATE' => '1979-01-03',
                'OXURL'       => 'http://www.url1.com',
                'OXUPDATEKEY' => '',
                'OXUPDATEEXP' => 0,
            ]
        );
    }

    private function createNewAdminUser(AcceptanceAdminTester $I, Users $adminUsersPage): void
    {
        $adminUser = new AdminUser();
        $adminUser->setActive(true);
        $adminUser->setUsername('example01@oxid-esales.dev');
        $adminUser->setCustomerNumber('20');
        $adminUser->setTitle('Mrs');
        $adminUser->setFistName('Name_šÄßüл');
        $adminUser->setFamilyName('Surname_šÄßüл');
        $adminUser->setCompany('company_šÄßüл');
        $adminUser->setStreet('street_šÄßüл');
        $adminUser->setStreetNumber('1');
        $adminUser->setZipCode('3000');
        $adminUser->setCity('City_šÄßüл');
        $adminUser->setUstid('111222');
        $adminUser->setAdditionalInfo('additional info_šÄßüл');
        $adminUser->setCountryId('Germany');
        $adminUser->setStateId('BW');
        $adminUser->setPhone('111222333');
        $adminUser->setFax('222333444');
        $adminUser->setBirthday('01');
        $adminUser->setBirthMonth('12');
        $adminUser->setBirthYear('1980');
        $adminUsersPage->createNewUser($adminUser);

        $I->seeCheckboxIsChecked($adminUsersPage->userActiveField);
        $I->seeOptionIsSelected($adminUsersPage->userRightsField,"Customer");
        $I->assertEquals("example01@oxid-esales.dev", $I->grabValueFrom($adminUsersPage->usernameField));
        $I->assertEquals("20", $I->grabValueFrom($adminUsersPage->userCustomerNumberField));
        $I->seeOptionIsSelected($adminUsersPage->userTitleField,"Mrs");
        $I->assertEquals("Name_šÄßüл", $I->grabValueFrom($adminUsersPage->userFirstNameField));
        $I->assertEquals("Surname_šÄßüл", $I->grabValueFrom($adminUsersPage->userLastNameField));
        $I->assertEquals("company_šÄßüл", $I->grabValueFrom($adminUsersPage->userCompanyField));
        $I->assertEquals("street_šÄßüл", $I->grabValueFrom($adminUsersPage->userStreetField));
        $I->assertEquals("1", $I->grabValueFrom($adminUsersPage->userStreetNumberField));
        $I->assertEquals("3000", $I->grabValueFrom($adminUsersPage->userZipCodeField));
        $I->assertEquals("City_šÄßüл", $I->grabValueFrom($adminUsersPage->userCityField));
        $I->assertEquals("111222", $I->grabValueFrom($adminUsersPage->userUstidField));
        $I->assertEquals("additional info_šÄßüл", $I->grabValueFrom($adminUsersPage->userAdditonalInformationField));
        $I->seeOptionIsSelected($adminUsersPage->userCountryIdField,"Germany");
        $I->assertEquals("BW", $I->grabValueFrom($adminUsersPage->userStateIdField));
        $I->assertEquals("111222333", $I->grabValueFrom($adminUsersPage->userPhoneField));
        $I->assertEquals("222333444", $I->grabValueFrom($adminUsersPage->userFaxField));
        $I->assertEquals("01", $I->grabValueFrom($adminUsersPage->userBirthDayField));
        $I->assertEquals("12", $I->grabValueFrom($adminUsersPage->userBirthMonthField));
        $I->assertEquals("1980", $I->grabValueFrom($adminUsersPage->userBirthYearField));
        $I->assertEquals("No", $I->grabTextFrom($adminUsersPage->userHasPasswordSelector));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->userPasswordField));
    }

    private function updateAdminUser(AcceptanceAdminTester $I, Users $adminUsersPage): void
    {
        $adminUser = new AdminUser();
        $adminUser->setActive(false);
        $adminUser->setUserRights("Admin");
        $adminUser->setPassword("adminpass");
        $adminUser->setUsername('example00@oxid-esales.dev');
        $adminUser->setCustomerNumber('121');
        $adminUser->setTitle('Mr');
        $adminUser->setFistName('Name1');
        $adminUser->setFamilyName('Surname1');
        $adminUser->setCompany('company1');
        $adminUser->setStreet('street1');
        $adminUser->setStreetNumber('11');
        $adminUser->setZipCode('30001');
        $adminUser->setCity('City11');
        $adminUser->setAdditionalInfo('additional info1');
        $adminUser->setCountryId('Belgium');
        $adminUser->setStateId('BE');
        $adminUser->setPhone('1112223331');
        $adminUser->setFax('2223334441');
        $adminUser->setBirthday('03');
        $adminUser->setBirthMonth('13');
        $adminUser->setBirthYear('1979');
        $adminUsersPage->editUser($adminUser);

        $I->dontSeeCheckboxIsChecked($adminUsersPage->userActiveField);
        $I->seeOptionIsSelected($adminUsersPage->userRightsField, "Admin");
        $I->assertEquals("example00@oxid-esales.dev", $I->grabValueFrom($adminUsersPage->usernameField));
        $I->assertEquals("121", $I->grabValueFrom($adminUsersPage->userCustomerNumberField));
        $I->assertEquals("Name1", $I->grabValueFrom($adminUsersPage->userFirstNameField));
        $I->assertEquals("Surname1", $I->grabValueFrom($adminUsersPage->userLastNameField));
        $I->assertEquals("company1", $I->grabValueFrom($adminUsersPage->userCompanyField));
        $I->assertEquals("street1", $I->grabValueFrom($adminUsersPage->userStreetField));
        $I->assertEquals("11", $I->grabValueFrom($adminUsersPage->userStreetNumberField));
        $I->assertEquals("30001", $I->grabValueFrom($adminUsersPage->userZipCodeField));
        $I->assertEquals("City11", $I->grabValueFrom($adminUsersPage->userCityField));
        $I->assertEquals("111222", $I->grabValueFrom($adminUsersPage->userUstidField));
        $I->assertEquals("additional info1", $I->grabValueFrom($adminUsersPage->userAdditonalInformationField));
        $I->seeOptionIsSelected($adminUsersPage->userCountryIdField, "Belgium");
        $I->assertEquals("BE", $I->grabValueFrom($adminUsersPage->userStateIdField));
        $I->assertEquals("1112223331", $I->grabValueFrom($adminUsersPage->userPhoneField));
        $I->assertEquals("2223334441", $I->grabValueFrom($adminUsersPage->userFaxField));
        $I->assertEquals("03", $I->grabValueFrom($adminUsersPage->userBirthDayField));
        $I->assertEquals("01", $I->grabValueFrom($adminUsersPage->userBirthMonthField));
        $I->assertEquals("1979", $I->grabValueFrom($adminUsersPage->userBirthYearField));
        $I->assertEquals("Yes", $I->grabTextFrom($adminUsersPage->userHasPasswordSelector));
        $I->assertEquals("", $I->grabValueFrom($adminUsersPage->userPasswordField));
    }

}
