<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use Codeception\Util\Fixtures;
use OxidEsales\Codeception\Admin\DataObject\AdminUser;
use OxidEsales\Codeception\Admin\DataObject\AdminUserAddresses;
use OxidEsales\Codeception\Admin\DataObject\AdminUserExtendedInfo;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('admin')]
final class UserCest
{
    public function testUserMainInfo(AcceptanceTester $I): void
    {
        $I->wantToTest('User main info');

        // Main tab
        $adminUsersPage = $I->loginAdmin()->openUsers();

        $adminUser = new AdminUser();
        $adminUser->setActive(true);
        $adminUser->setUsername('example01@oxid-esales.dev');
        $adminUser->setCustomerNumber('20');
        $adminUser->setBirthday('01');
        $adminUser->setBirthMonth('12');
        $adminUser->setBirthYear('1980');
        $adminUser->setUstid('111222');

        $adminUserAddress = new AdminUserAddresses();
        $adminUserAddress->setTitle('Mrs');
        $adminUserAddress->setFirstName('Name_šÄßüл');
        $adminUserAddress->setLastName('Surname_šÄßüл');
        $adminUserAddress->setCompany('company_šÄßüл');
        $adminUserAddress->setStreet('street_šÄßüл');
        $adminUserAddress->setStreetNumber('1');
        $adminUserAddress->setZip('3000');
        $adminUserAddress->setCity('City_šÄßüл');
        $adminUserAddress->setAdditionalInfo('additional info_šÄßüл');
        $adminUserAddress->setCountryId('Germany');
        $adminUserAddress->setStateId('BW');
        $adminUserAddress->setPhone('111222333');
        $adminUserAddress->setFax('222333444');

        $adminUsersPage = $adminUsersPage->createNewUser($adminUser, $adminUserAddress);

        //By default is Customer
        $adminUser->setUserRights("Customer");

        $adminUsersPage = $adminUsersPage->seeUserInformation($adminUser, $adminUserAddress);

        $changedAdminUser = $this->getAdminUser();
        $changedAdminUserAddress = $this->getAdminUserAddress();

        $adminUsersPage = $adminUsersPage->editUser($changedAdminUser, $changedAdminUserAddress);
        //entering wrong month, set to default 01
        $changedAdminUser->setBirthMonth('01');

        $adminUsersPage = $adminUsersPage->seeUserInformation($changedAdminUser, $changedAdminUserAddress);

        $adminUserExtendedPage = $adminUsersPage->openExtendedTab()
            ->seeUserAddress($changedAdminUserAddress);

        $adminUserHistoryPage = $adminUserExtendedPage->openHistoryTab()
            ->createNewRemark("new note_šÄßüл")
            ->selectUserRemark("0");
        $I->seeInField($adminUserHistoryPage->remarkField, "new note_šÄßüл");

        $adminUserHistoryPage = $adminUserHistoryPage->deleteRemark()->selectUserRemark("0");
        $I->dontSeeInField($adminUserHistoryPage->remarkField, "new note_šÄßüл");

        $adminUserProductPage = $adminUserHistoryPage->openProductsTab();
        $adminUserPaymentPage = $adminUserProductPage->openPaymentTab();

        //checking if created user can be found
        $adminUserPaymentPage->findByUserName($changedAdminUser->getUsername())
            ->openExtendedTab()
            ->seeUserAddress($changedAdminUserAddress);
    }

    public function testUserAddresses(AcceptanceTester $I): void
    {
        $I->wantToTest('User addresses');

        $this->createAdminTestUser(
            $I,
            $this->getAdminUser(),
            $this->getAdminUserAddress(),
            $this->getAdminUserExtendedInfo()
        );

        $adminUsersPage = $I->loginAdmin()->openUsers();

        $adminUsersPage = $adminUsersPage->findByUserName('example00@oxid-esales.dev')->openAddressesTab();

        $adminUserAddress1 = new AdminUserAddresses();
        $adminUserAddress1->setTitle('Mr');
        $adminUserAddress1->setFirstName('shipping name_šÄßüл');
        $adminUserAddress1->setLastName('shipping surname_šÄßüл');
        $adminUserAddress1->setCompany('shipping company_šÄßüл');
        $adminUserAddress1->setStreet('shipping street_šÄßüл');
        $adminUserAddress1->setStreetNumber('1');
        $adminUserAddress1->setZip('1000');
        $adminUserAddress1->setCity('shipping city_šÄßüл');
        $adminUserAddress1->setAdditionalInfo('shipping additional info_šÄßüл');
        $adminUserAddress1->setCountryId('Germany');
        $adminUserAddress1->setPhone('7778788');
        $adminUserAddress1->setFax('8887877');

        $adminUsersPage = $adminUsersPage->createNewAddress($adminUserAddress1)
            ->seeAddressInformation($adminUserAddress1);

        $adminUserAddress2 = new AdminUserAddresses();
        $adminUserAddress2->setTitle('Mrs');
        $adminUserAddress2->setFirstName('name2');
        $adminUserAddress2->setLastName('last name 2');
        $adminUserAddress2->setCompany('company 2');
        $adminUserAddress2->setStreet('street2');
        $adminUserAddress2->setStreetNumber('12');
        $adminUserAddress2->setZip('2001');
        $adminUserAddress2->setCity('city2');
        $adminUserAddress2->setAdditionalInfo('additional info2');
        $adminUserAddress2->setCountryId('United States');
        $adminUserAddress2->setPhone('999666');
        $adminUserAddress2->setFax('666999');

        $emptyAddress = new AdminUserAddresses();
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

    public function testCreateUserExtendedInfo(AcceptanceTester $I): void
    {
        $I->wantToTest('Create user extended info');

        $user = $this->getAdminUser();
        $userAddress = $this->getAdminUserAddress();
        $userOriginalExtendedInfo = $this->getAdminUserExtendedInfo();
        $this->createAdminTestUser($I, $user, $userAddress, $userOriginalExtendedInfo);

        $adminPanel = $I->loginAdmin();
        $adminUsersPage = $adminPanel->openUsers();

        $adminUsersPage = $adminUsersPage->findByUserName($user->getUsername())
            ->openExtendedTab()
            ->seeUserAddress($userAddress);

        $adminUserChangedExtendedInfo = new AdminUserExtendedInfo();
        $adminUserChangedExtendedInfo->setEveningPhone('555444555');
        $adminUserChangedExtendedInfo->setCellularPhone('666555666');
        $adminUserChangedExtendedInfo->setReceivesNewsletter(true);
        $adminUserChangedExtendedInfo->setEmailInvalid(true);
        $adminUserChangedExtendedInfo->setCreditRating('1500');
        $adminUserChangedExtendedInfo->setUrl('http://www.url.com');
        $adminUsersPage = $adminUsersPage->editExtendedInfo($adminUserChangedExtendedInfo)
            ->seeUserExtendedInformation($adminUserChangedExtendedInfo);

        $adminUsersPage->editExtendedInfo($userOriginalExtendedInfo)
            ->seeUserExtendedInformation($userOriginalExtendedInfo);
    }

    #[Group('session')]
    public function updatePassword(AcceptanceTester $I): void
    {
        $I->wantToTest('that admin can update his own password');
        $newPass = uniqid('new-pass-', true);
        $userData = Fixtures::get('adminUser');
        $adminLoginPage = $I->openAdmin();
        $I->amGoingTo('log the existing admin in, find him in the list and update his password');
        $adminLoginPage
            ->login($userData['userLoginName'], $userData['userPassword'])
            ->openUsers()
            ->findByUserName($userData['userId'])
            ->updatePassword($newPass);

        $I->amGoingTo('send any page request after password change');
        $I->reloadPage();

        $I->expect('that admin is logged out but can log in with the new pass');
        $adminLoginPage->seeLoginForm()
            ->login($userData['userLoginName'], $newPass);
    }

    private function createAdminTestUser(
        AcceptanceTester $I,
        AdminUser $user,
        AdminUserAddresses $userAddress,
        AdminUserExtendedInfo $userExtendedInfo
    ): void {
        $I->haveInDatabase(
            'oxuser',
            [
                'OXID'        => "kdiruuc",
                'OXACTIVE'    => $user->getActive(),
                'OXRIGHTS'    => 'malladmin',
                'OXSHOPID'    => 1,
                'OXUSERNAME'  => $user->getUsername(),
                'OXPASSWORD'  => '1397d0b4392f452a5bd058891c9b255e',
                'OXPASSSALT'  => '3032396331663033316535343361356231363666653666316533376235353830',
                'OXCUSTNR'    => $user->getCustomerNumber(),
                'OXUSTID'     => $user->getUstid(),
                'OXCOMPANY'   => $userAddress->getCompany(),
                'OXFNAME'     => $userAddress->getFirstName(),
                'OXLNAME'     => $userAddress->getLastName(),
                'OXSTREET'    => $userAddress->getStreet(),
                'OXSTREETNR'  => $userAddress->getStreetNumber(),
                'OXADDINFO'   => $userAddress->getAdditionalInfo(),
                'OXCITY'      => $userAddress->getCity(),
                'OXCOUNTRYID' => 'testcountry_be',
                'OXSTATEID'   => $userAddress->getStateId(),
                'OXZIP'       => $userAddress->getZip(),
                'OXFON'       => $userAddress->getPhone(),
                'OXFAX'       => $userAddress->getFax(),
                'OXSAL'       => $userAddress->getTitle(),
                'OXBONI'      => $userExtendedInfo->getCreditRating(),
                'OXCREATE'    => '2010-02-05 10:22:37',
                'OXREGISTER'  => '2010-02-05 10:22:48',
                'OXPRIVFON'   => $userExtendedInfo->getEveningPhone(),
                'OXMOBFON'    => $userExtendedInfo->getCellularPhone(),
                'OXBIRTHDATE' => $user->getBirthYear() . '-' . $user->getBirthMonth() . '-' . $user->getBirthday(),
                'OXURL'       => $userExtendedInfo->getUrl(),
                'OXUPDATEKEY' => '',
                'OXUPDATEEXP' => 0,
            ]
        );
    }

    private function getAdminUser(): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->setActive(false);
        $adminUser->setUserRights("Admin");
        $adminUser->setPassword("adminpass");
        $adminUser->setUsername('example00@oxid-esales.dev');
        $adminUser->setCustomerNumber('121');
        $adminUser->setBirthday('01');
        $adminUser->setBirthMonth('12');
        $adminUser->setBirthYear('1980');
        $adminUser->setUstid('111222');
        $adminUser->setBirthday('03');
        $adminUser->setBirthMonth('13');
        $adminUser->setBirthYear('1979');
        return $adminUser;
    }

    private function getAdminUserAddress(): AdminUserAddresses
    {
        $adminUserAddress = new AdminUserAddresses();
        $adminUserAddress->setTitle('Mr');
        $adminUserAddress->setFirstName('Name1');
        $adminUserAddress->setLastName('Surname1');
        $adminUserAddress->setCompany('company1');
        $adminUserAddress->setStreet('street1');
        $adminUserAddress->setStreetNumber('11');
        $adminUserAddress->setZip('30001');
        $adminUserAddress->setCity('City11');
        $adminUserAddress->setAdditionalInfo('additional info1');
        $adminUserAddress->setCountryId('Belgium');
        $adminUserAddress->setStateId('BE');
        $adminUserAddress->setPhone('1112223331');
        $adminUserAddress->setFax('2223334441');
        return $adminUserAddress;
    }

    private function getAdminUserExtendedInfo(): AdminUserExtendedInfo
    {
        $adminUserExtendedInfo = new AdminUserExtendedInfo();
        $adminUserExtendedInfo->setEveningPhone('5554445551');
        $adminUserExtendedInfo->setCellularPhone('6665556661');
        $adminUserExtendedInfo->setReceivesNewsletter(false);
        $adminUserExtendedInfo->setEmailInvalid(false);
        $adminUserExtendedInfo->setCreditRating('1000');
        $adminUserExtendedInfo->setUrl('http://www.url1.com');
        return $adminUserExtendedInfo;
    }
}
