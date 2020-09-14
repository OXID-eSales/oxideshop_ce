<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\acceptanceAdmin;

use OxidEsales\Codeception\Admin\DataObject\AdminUser;
use OxidEsales\Codeception\Admin\Users;
use OxidEsales\EshopCommunity\Tests\Codeception\AcceptanceAdminTester;

/**
 * Class CreatingAdminUserCest
 */
final class CreatingAdminUserCest
{
    /**
     * @param AcceptanceAdminTester $I
     */
    public function createUserMainInfo(AcceptanceAdminTester $I): void
    {
        $I->wantToTest('create admin users');

        $adminPanel = $I->loginAdmin();
        // Main tab
        $adminUsersPage = $adminPanel->openUsers();

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

        $I->seeCheckboxIsChecked('editval[oxuser__oxactive]');
        $I->seeOptionIsSelected("editval[oxuser__oxrights]","Customer");
        $I->assertEquals("example01@oxid-esales.dev", $I->grabValueFrom("editval[oxuser__oxusername]"));
        $I->assertEquals("20", $I->grabValueFrom("editval[oxuser__oxcustnr]"));
        $I->seeOptionIsSelected("editval[oxuser__oxsal]","Mrs");
        $I->assertEquals("Name_šÄßüл", $I->grabValueFrom("editval[oxuser__oxfname]"));
        $I->assertEquals("Surname_šÄßüл", $I->grabValueFrom("editval[oxuser__oxlname]"));
        $I->assertEquals("company_šÄßüл", $I->grabValueFrom("editval[oxuser__oxcompany]"));
        $I->assertEquals("street_šÄßüл", $I->grabValueFrom("editval[oxuser__oxstreet]"));
        $I->assertEquals("1", $I->grabValueFrom("editval[oxuser__oxstreetnr]"));
        $I->assertEquals("3000", $I->grabValueFrom("editval[oxuser__oxzip]"));
        $I->assertEquals("City_šÄßüл", $I->grabValueFrom("editval[oxuser__oxcity]"));
        $I->assertEquals("111222", $I->grabValueFrom("editval[oxuser__oxustid]"));
        $I->assertEquals("additional info_šÄßüл", $I->grabValueFrom("editval[oxuser__oxaddinfo]"));
        $I->seeOptionIsSelected("editval[oxuser__oxcountryid]","Germany");
        $I->assertEquals("BW", $I->grabValueFrom("editval[oxuser__oxstateid]"));
        $I->assertEquals("111222333", $I->grabValueFrom("editval[oxuser__oxfon]"));
        $I->assertEquals("222333444", $I->grabValueFrom("editval[oxuser__oxfax]"));
        $I->assertEquals("01", $I->grabValueFrom("editval[oxuser__oxbirthdate][day]"));
        $I->assertEquals("12", $I->grabValueFrom("editval[oxuser__oxbirthdate][month]"));
        $I->assertEquals("1980", $I->grabValueFrom("editval[oxuser__oxbirthdate][year]"));
        $I->assertEquals("No", $I->grabTextFrom("#myedit table tr:nth-child(17) td:nth-child(2)"));
        $I->assertEquals("", $I->grabValueFrom("newPassword"));

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

        $I->dontSeeCheckboxIsChecked('editval[oxuser__oxactive]');
        $I->seeOptionIsSelected("editval[oxuser__oxrights]","Admin");
        $I->assertEquals("example00@oxid-esales.dev", $I->grabValueFrom("editval[oxuser__oxusername]"));
        $I->assertEquals("121", $I->grabValueFrom("editval[oxuser__oxcustnr]"));
        $I->assertEquals("Name1", $I->grabValueFrom("editval[oxuser__oxfname]"));
        $I->assertEquals("Surname1", $I->grabValueFrom("editval[oxuser__oxlname]"));
        $I->assertEquals("company1", $I->grabValueFrom("editval[oxuser__oxcompany]"));
        $I->assertEquals("street1", $I->grabValueFrom("editval[oxuser__oxstreet]"));
        $I->assertEquals("11", $I->grabValueFrom("editval[oxuser__oxstreetnr]"));
        $I->assertEquals("30001", $I->grabValueFrom("editval[oxuser__oxzip]"));
        $I->assertEquals("City11", $I->grabValueFrom("editval[oxuser__oxcity]"));
        $I->assertEquals("111222", $I->grabValueFrom("editval[oxuser__oxustid]"));
        $I->assertEquals("additional info1", $I->grabValueFrom("editval[oxuser__oxaddinfo]"));
        $I->seeOptionIsSelected("editval[oxuser__oxcountryid]","Belgium");
        $I->assertEquals("BE", $I->grabValueFrom("editval[oxuser__oxstateid]"));
        $I->assertEquals("1112223331", $I->grabValueFrom("editval[oxuser__oxfon]"));
        $I->assertEquals("2223334441", $I->grabValueFrom("editval[oxuser__oxfax]"));
        $I->assertEquals("03", $I->grabValueFrom("editval[oxuser__oxbirthdate][day]"));
        $I->assertEquals("01", $I->grabValueFrom("editval[oxuser__oxbirthdate][month]"));
        $I->assertEquals("1979", $I->grabValueFrom("editval[oxuser__oxbirthdate][year]"));
        $I->assertEquals("Yes", $I->grabTextFrom("#myedit table tr:nth-child(17) td:nth-child(2)"));
        $I->assertEquals("", $I->grabValueFrom("newPassword"));

        // Extended tab
        $adminUsersPage->openExtendedTab();

        $I->assertEquals(
            "Mr Name1 Surname1 company1 street1 11 BE 30001 City11 additional info1 Belgium 1112223331",
            $I->clearString($I->grabTextFrom("#test_userAddress"))
        );

        // History Tab
        $adminUsersPage->openHistoryTab();
        $I->dontSeeOptionIsSelected("//select[@name='rem_oxid']","");

        $adminUsersPage->createNewRemark("new note_šÄßüл");

        $I->selectOption("//select[@name='rem_oxid']","0");
        $I->assertEquals("new note_šÄßüл", $I->grabValueFrom("remarktext"));

        $adminUsersPage->deleteRemark();

        $I->dontSeeOptionIsSelected("//select[@name='rem_oxid']","");

        $I->selectOption("//select[@name='rem_oxid']","0");

        $I->assertNotEquals("new note_šÄßüл", $I->grabValueFrom("remarktext"));

        //testing if other tabs are working
        $adminUsersPage->openProductsTab();
        $adminUsersPage->openPaymentTab();

        //checking if created item can be found
        $I->selectListFrame();
        $I->fillField("where[oxuser][oxusername]", "example00");
        $I->click("submitit");
        $I->assertEquals("example00@oxid-esa...", $I->grabTextFrom("//tr[@id='row.1']/td[3]"));
        $I->dontSeeElement("//tr[@id='row.2']/td[3]");
    }

}
