<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use OxidEsales\Codeception\Admin\DataObject\AdminUserAddresses;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

/**
 * Class CreateUserAddressesCest
 */
class CreateUserAddressesCest
{
    /**
     * @param AcceptanceAdminTester $I
     */
    public function _before(AcceptanceAdminTester $I)
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

    public function testCreateUserAddresses(AcceptanceAdminTester $I): void
    {
        $adminPanel = $I->loginAdmin();

        $adminUsersPage = $adminPanel->openUsers();
        $I->selectListFrame();
        $adminUsersPage->find("where[oxuser][oxusername]", "example00@oxid-esales.dev");
        $adminUsersPage->openAddressesTab();

        $I->seeOptionIsSelected("//select", "-");

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

        $I->seeOptionIsSelected("//select", "shipping name_šÄßüл shipping surname_šÄßüл, shipping street_šÄßüл, shipping city_šÄßüл");
        $I->seeOptionIsSelected("editval[oxaddress__oxsal]", "Mr");
        $I->assertEquals("shipping name_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxfname]"));
        $I->assertEquals("shipping surname_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxlname]"));
        $I->assertEquals("shipping company_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxcompany]"));
        $I->assertEquals("shipping street_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxstreet]"));
        $I->assertEquals("1", $I->grabValueFrom("editval[oxaddress__oxstreetnr]"));
        $I->assertEquals("1000", $I->grabValueFrom("editval[oxaddress__oxzip]"));
        $I->assertEquals("shipping city_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxcity]"));
        $I->seeOptionIsSelected("editval[oxaddress__oxcountryid]", "Germany");
        $I->assertEquals("7778788", $I->grabValueFrom("editval[oxaddress__oxfon]"));
        $I->assertEquals("8887877", $I->grabValueFrom("editval[oxaddress__oxfax]"));

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
        $I->selectOption("oxaddressid", "-");
        $I->selectOption("oxaddressid", "name2 last name 2, street2, city2");
        $I->seeOptionIsSelected("editval[oxaddress__oxsal]","Mrs");
        $I->assertEquals("name2", $I->grabValueFrom("editval[oxaddress__oxfname]"));
        $I->assertEquals("last name 2", $I->grabValueFrom("editval[oxaddress__oxlname]"));
        $I->assertEquals("company 2", $I->grabValueFrom("editval[oxaddress__oxcompany]"));
        $I->assertEquals("street2", $I->grabValueFrom("editval[oxaddress__oxstreet]"));
        $I->assertEquals("12", $I->grabValueFrom("editval[oxaddress__oxstreetnr]"));
        $I->assertEquals("2001", $I->grabValueFrom("editval[oxaddress__oxzip]"));
        $I->assertEquals("city2", $I->grabValueFrom("editval[oxaddress__oxcity]"));
        $I->assertEquals("additional info2", $I->grabValueFrom("editval[oxaddress__oxaddinfo]"));
        $I->seeOptionIsSelected("editval[oxaddress__oxcountryid]","United States");
        $I->assertEquals("999666", $I->grabValueFrom("editval[oxaddress__oxfon]"));
        $I->assertEquals("666999", $I->grabValueFrom("editval[oxaddress__oxfax]"));

        $I->selectOption("oxaddressid", "shipping name_šÄßüл shipping surname_šÄßüл, shipping street_šÄßüл, shipping city_šÄßüл");
        $I->seeOptionIsSelected("editval[oxaddress__oxsal]","Mr");
        $I->assertEquals("shipping name_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxfname]"));
        $I->assertEquals("shipping surname_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxlname]"));
        $I->assertEquals("shipping company_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxcompany]"));
        $I->assertEquals("shipping street_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxstreet]"));
        $I->assertEquals("1", $I->grabValueFrom("editval[oxaddress__oxstreetnr]"));
        $I->assertEquals("1000", $I->grabValueFrom("editval[oxaddress__oxzip]"));
        $I->assertEquals("shipping city_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxcity]"));
        $I->assertEquals("shipping additional info_šÄßüл", $I->grabValueFrom("editval[oxaddress__oxaddinfo]"));
        $I->seeOptionIsSelected("editval[oxaddress__oxcountryid]","Germany");
        $I->assertEquals("7778788", $I->grabValueFrom("editval[oxaddress__oxfon]"));
        $I->assertEquals("8887877", $I->grabValueFrom("editval[oxaddress__oxfax]"));

        // delete addresses
        $adminUsersPage->deleteSelectedAddress();

        $I->seeOptionIsSelected("oxaddressid", "-");
        $I->selectOption('oxaddressid', "name2 last name 2, street2, city2");

        $adminUsersPage->deleteSelectedAddress();

        $I->seeOptionIsSelected("oxaddressid", "-");
        $I->seeOptionIsSelected('editval[oxaddress__oxsal]', "Mr");
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxfname]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxlname]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxcompany]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxstreet]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxstreetnr]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxzip]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxcity]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxaddinfo]"));
        $I->seeOptionIsSelected('editval[oxaddress__oxcountryid]', "Austria");
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxfon]"));
        $I->assertEquals("", $I->grabValueFrom("editval[oxaddress__oxfax]"));
    }
}
