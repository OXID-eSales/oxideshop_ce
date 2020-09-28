<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use OxidEsales\Codeception\Admin\DataObject\AdminUser;
use OxidEsales\Codeception\Admin\DataObject\AdminUserAddresses;
use OxidEsales\Codeception\Admin\DataObject\AdminUserExtendedInfo;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

final class AdminUserCest
{
    /**
     * @param AcceptanceAdminTester $I
     */
    public function testUserMainInfo(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('User main info');

        // Main tab
        $adminUsersPage = $I->loginAdmin()->openUsers();

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
        $adminUsersPage = $adminUsersPage->createNewUser($adminUser);

        //By default is Customer
        $adminUser->setUserRights("Customer");

        $adminUsersPage = $adminUsersPage->seeUserInformation($adminUser);

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

        $adminUsersPage = $adminUsersPage->editUser($adminUser);
        //entering wrong month, set to default 01
        $adminUser->setBirthMonth('01');

        $adminUsersPage = $adminUsersPage->seeUserInformation($adminUser);

        $adminUserExtendedPage = $adminUsersPage->openExtendedTab()
            ->seeUserAddress("Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331");

        $adminUserHistoryPage = $adminUserExtendedPage->openHistoryTab()
            ->createNewRemark("new note_šÄßüл")
            ->selectUserRemark("0");
        $I->seeInField($adminUserHistoryPage->remarkField, "new note_šÄßüл");

        $adminUserHistoryPage = $adminUserHistoryPage->deleteRemark()->selectUserRemark("0");
        $I->dontSeeInField($adminUserHistoryPage->remarkField, "new note_šÄßüл");

        $adminUserProductPage = $adminUserHistoryPage->openProductsTab();
        $adminUserPaymentPage = $adminUserProductPage->openPaymentTab();

        //checking if created user can be found
        $adminUserPaymentPage->findByUserName("example00@oxid-esales.dev")
            ->openExtendedTab()
            ->seeUserAddress("Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331");
    }

    public function testUserAddresses(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('User addresses');

        $this->createAdminTestUser($I);

        $adminUsersPage = $I->loginAdmin()->openUsers();

        $adminUsersPage = $adminUsersPage->findByUserName("example00@oxid-esales.dev")->openAddressesTab();

        $adminUserAddress1 = new AdminUserAddresses();
        $adminUserAddress1->setTitle("Mr");
        $adminUserAddress1->setFirstName("shipping name_šÄßüл");
        $adminUserAddress1->setLastName("shipping surname_šÄßüл");
        $adminUserAddress1->setCompany("shipping company_šÄßüл");
        $adminUserAddress1->setStreet("shipping street_šÄßüл");
        $adminUserAddress1->setStreetNumber("1");
        $adminUserAddress1->setZip("1000");
        $adminUserAddress1->setCity("shipping city_šÄßüл");
        $adminUserAddress1->setAdditionalInfo("shipping additional info_šÄßüл");
        $adminUserAddress1->setCountryId("Germany");
        $adminUserAddress1->setPhone("7778788");
        $adminUserAddress1->setFax("8887877");

        $adminUsersPage = $adminUsersPage->createNewAddress($adminUserAddress1)
            ->seeAddressInformation($adminUserAddress1);

        $adminUserAddress2 = new AdminUserAddresses();
        $adminUserAddress2->setTitle("Mrs");
        $adminUserAddress2->setFirstName("name2");
        $adminUserAddress2->setLastName("last name 2");
        $adminUserAddress2->setCompany("company 2");
        $adminUserAddress2->setStreet("street2");
        $adminUserAddress2->setStreetNumber("12");
        $adminUserAddress2->setZip("2001");
        $adminUserAddress2->setCity("city2");
        $adminUserAddress2->setAdditionalInfo("additional info2");
        $adminUserAddress2->setCountryId("United States");
        $adminUserAddress2->setPhone("999666");
        $adminUserAddress2->setFax("666999");

        $emptyAddress = new AdminUserAddresses();
        $emptyAddress->setFirstName("");
        $emptyAddress->setLastName("");
        $emptyAddress->setCompany("");
        $emptyAddress->setStreet("");
        $emptyAddress->setStreetNumber("");
        $emptyAddress->setZip("");
        $emptyAddress->setCity("");
        $emptyAddress->setAdditionalInfo("");
        $emptyAddress->setPhone("");
        $emptyAddress->setFax("");

        $adminUsersPage->createNewAddress($adminUserAddress2)
            ->selectAddress($emptyAddress)
            ->selectAddress($adminUserAddress2)
            ->seeAddressInformation($adminUserAddress2)
            ->selectAddress($adminUserAddress1)
            ->seeAddressInformation($adminUserAddress1)
            ->deleteSelectedAddress()
            ->selectAddress($adminUserAddress2)
            ->deleteSelectedAddress()
            ->seeAddressInformation($emptyAddress);
    }

    public function testCreateUserExtendedInfo(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('Create user extended info');

        $this->createAdminTestUser($I);

        $adminPanel = $I->loginAdmin();
        $adminUsersPage = $adminPanel->openUsers();

        $adminUsersPage = $adminUsersPage->findByUserName("example00@oxid-esales.dev")
            ->openExtendedTab()
            ->seeUserAddress("Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331");

        $adminUserExtendedInfo = new AdminUserExtendedInfo();
        $adminUserExtendedInfo->setEveningPhone('555444555');
        $adminUserExtendedInfo->setCelluarPhone('666555666');
        $adminUserExtendedInfo->setRecievesNewsletter(true);
        $adminUserExtendedInfo->setEmailInvalid(true);
        $adminUserExtendedInfo->setCreditRating('1500');
        $adminUserExtendedInfo->setUrl('http://www.url.com');
        $adminUsersPage = $adminUsersPage->editExtendedInfo($adminUserExtendedInfo)
            ->seeUserExtendedInformation($adminUserExtendedInfo);

        $adminUserExtendedInfo = new AdminUserExtendedInfo();
        $adminUserExtendedInfo->setEveningPhone('5554445551');
        $adminUserExtendedInfo->setCelluarPhone('6665556661');
        $adminUserExtendedInfo->setRecievesNewsletter(false);
        $adminUserExtendedInfo->setEmailInvalid(false);
        $adminUserExtendedInfo->setCreditRating('1000');
        $adminUserExtendedInfo->setUrl('http://www.url1.com');
        $adminUsersPage->editExtendedInfo($adminUserExtendedInfo)
            ->seeUserExtendedInformation($adminUserExtendedInfo);
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
}
