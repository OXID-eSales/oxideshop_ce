<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Acceptance\Admin;

use Codeception\Attribute\Group;
use OxidEsales\Codeception\Admin\DataObject\AdminUser;
use OxidEsales\Codeception\Admin\DataObject\AdminUserAddress;
use OxidEsales\Codeception\Admin\DataObject\AdminUserExtendedInfo;
use OxidEsales\EshopCommunity\Tests\Codeception\Support\AcceptanceTester;

#[Group('user')]
final class UserCest
{
    private const BELGIUM = 'Belgium';
    private const GERMANY = 'Germany';

    public function mainTab(AcceptanceTester $I): void
    {
        $I->wantToTest('functionality on the Main tab');

        $I->amGoingTo('prepare test data');
        $I->comment('this user data has no user-rights specified');
        $user1 = $this->getAdminUser1();
        $I->comment('this user data contains invalid birth-month value');
        $user2 = $this->getAdminUser2();
        $address1 = $this->getAdminUserAddress1();
        $address2 = $this->getAdminUserAddress2();
        $mainTab = $I
            ->loginAdmin()
            ->openUsers()
            ->createNewUser(
                $user1,
                $address1
            );

        $I->expect('to see user has "Customer", the default user-rights value, assigned');
        $user1->setUserRights('Customer');
        $mainTab->seeUserInformation(
            $user1,
            $address1
        );

        $I->amGoingTo('modify user info with the new data');
        $mainTab->editUser(
            $user2,
            $address2
        );
        $I->expect('to see user has "01", the default birth-month value, assigned');
        $user2->setBirthMonth('01');
        $I->expect('user can be found by the new username and has the new info saved');
        $mainTab
            ->findByUserName(
                $user2->getUsername()
            )
            ->seeUserInformation(
                $user2,
                $address2
            )
            ->openExtendedTab()
            ->seeUserAddress($address2);
    }

    public function historyTab(AcceptanceTester $I): void
    {
        $I->wantToTest('functionality on the History tab');

        $I->amGoingTo('prepare test data');
        $user = $this->getAdminUser1();
        $addressEmpty = new AdminUserAddress();
        $remarkText = 'new note_šÄßüл';
        $this->createAdminTestUser(
            $I,
            $user,
            $addressEmpty
        );

        $I->comment('the first remark "usrRegistered" is added after editing user');
        $I->amGoingTo('create/delete another remark');
        $I
            ->loginAdmin()
            ->openUsers()
            ->findByUserName(
                $user->getUsername()
            )
            ->editUserInformation(
                $user,
                $addressEmpty
            )
            ->openHistoryTab()
            ->createNewRemark($remarkText)
            ->openHistoryTab()
            ->selectUserRemark('0')
            ->seeRemarkText($remarkText)
            ->deleteRemark()
            ->selectUserRemark('0')
            ->dontSeeRemarkText($remarkText);

    }

    public function addressesTab(AcceptanceTester $I): void
    {
        $I->wantToTest('functionality on the Addresses tab');

        $I->amGoingTo('prepare test data');
        $user = $this->getAdminUser1();
        $address1 = $this->getAdminUserAddress1();
        $address2 = $this->getAdminUserAddress2();
        $addressEmpty = new AdminUserAddress();
        $this->createAdminTestUser(
            $I,
            $user,
            $addressEmpty
        );

        $I
            ->loginAdmin()
            ->openUsers()
            ->findByUserName(
                $user->getUsername()
            )
            ->openAddressesTab()
            ->createNewAddress($address1)
            ->createNewAddress($address2)
            ->selectAddress($address1)
            ->seeAddressInformation($address1)
            ->selectAddress($address2)
            ->seeAddressInformation($address2)
            ->deleteSelectedAddress()
            ->selectAddress($address1)
            ->deleteSelectedAddress()
            ->seeAddressInformation($addressEmpty);
    }

    public function extendedInfoTab(AcceptanceTester $I): void
    {
        $I->wantToTest('functionality on the Extended Info tab');

        $I->amGoingTo('prepare test data');
        $user = $this->getAdminUser1();
        $address = $this->getAdminUserAddress1();
        $extendedInfo1 = $this->getAdminUserExtendedInfo1();
        $extendedInfo2 = $this->getAdminUserExtendedInfo2();
        $this->createAdminTestUser(
            $I,
            $user,
            $address
        );

        $I->loginAdmin()
            ->openUsers()
            ->findByUserName(
                $user->getUsername()
            )
            ->openExtendedTab()
            ->seeUserAddress($address)
            ->editExtendedInfo($extendedInfo1)
            ->seeUserExtendedInformation($extendedInfo1)
            ->editExtendedInfo($extendedInfo2)
            ->seeUserExtendedInformation($extendedInfo2);
    }

    private function createAdminTestUser(
        AcceptanceTester $I,
        AdminUser $user,
        AdminUserAddress $userAddress
    ): void {
        $I->haveInDatabase(
            'oxuser',
            [
                'OXID'        => 'kdiruuc',
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
                'OXCOUNTRYID' => $this->mapAddressToDatabaseCountryId(
                    $userAddress->getCountryId()
                ),
                'OXSTATEID'   => $userAddress->getStateId(),
                'OXZIP'       => $userAddress->getZip(),
                'OXFON'       => $userAddress->getPhone(),
                'OXFAX'       => $userAddress->getFax(),
                'OXSAL'       => $userAddress->getTitle(),
                'OXBIRTHDATE' => "{$user->getBirthYear()}-{$user->getBirthMonth()}-{$user->getBirthday()}",
            ]
        );
    }

    private function getAdminUser1(): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->setActive(true);
        $adminUser->setUsername('example01@oxid-esales.dev');
        $adminUser->setCustomerNumber('20');
        $adminUser->setBirthday('01');
        $adminUser->setBirthMonth('12');
        $adminUser->setBirthYear('1980');
        $adminUser->setUstid('111222');

        return $adminUser;
    }

    private function getAdminUser2(): AdminUser
    {
        $adminUser = new AdminUser();
        $adminUser->setActive(false);
        $adminUser->setUserRights('Admin');
        $adminUser->setPassword('adminpass');
        $adminUser->setUsername('example00@oxid-esales.dev');
        $adminUser->setCustomerNumber('121');
        $adminUser->setBirthday('01');
        $adminUser->setBirthYear('1980');
        $adminUser->setUstid('111222');
        $adminUser->setBirthday('03');
        $adminUser->setBirthMonth('13');
        $adminUser->setBirthYear('1979');
        return $adminUser;
    }

    private function getAdminUserAddress1(): AdminUserAddress
    {
        $adminUserAddress = new AdminUserAddress();
        $adminUserAddress->setTitle('Mrs');
        $adminUserAddress->setFirstName('Name_šÄßüл');
        $adminUserAddress->setLastName('Surname_šÄßüл');
        $adminUserAddress->setCompany('company_šÄßüл');
        $adminUserAddress->setStreet('street_šÄßüл');
        $adminUserAddress->setStreetNumber('1');
        $adminUserAddress->setZip('3000');
        $adminUserAddress->setCity('City_šÄßüл');
        $adminUserAddress->setAdditionalInfo('additional info_šÄßüл');
        $adminUserAddress->setCountryId(self::GERMANY);
        $adminUserAddress->setStateId('BW');
        $adminUserAddress->setPhone('111222333');
        $adminUserAddress->setFax('222333444');

        return $adminUserAddress;
    }

    private function getAdminUserAddress2(): AdminUserAddress
    {
        $adminUserAddress = new AdminUserAddress();
        $adminUserAddress->setTitle('Mr');
        $adminUserAddress->setFirstName('Name1');
        $adminUserAddress->setLastName('Surname1');
        $adminUserAddress->setCompany('company1');
        $adminUserAddress->setStreet('street1');
        $adminUserAddress->setStreetNumber('11');
        $adminUserAddress->setZip('30001');
        $adminUserAddress->setCity('City11');
        $adminUserAddress->setAdditionalInfo('additional info1');
        $adminUserAddress->setCountryId(self::BELGIUM);
        $adminUserAddress->setStateId('BE');
        $adminUserAddress->setPhone('1112223331');
        $adminUserAddress->setFax('2223334441');

        return $adminUserAddress;
    }

    private function getAdminUserExtendedInfo1(): AdminUserExtendedInfo
    {
        $adminUserExtendedInfo = new AdminUserExtendedInfo();
        $adminUserExtendedInfo->setEveningPhone('5554445551');
        $adminUserExtendedInfo->setCellularPhone('6665556661');
        $adminUserExtendedInfo->setReceivesNewsletter(false);
        $adminUserExtendedInfo->setEmailInvalid(false);
        $adminUserExtendedInfo->setCreditRating('1000');
        $adminUserExtendedInfo->setUrl('https://www.example1.com');
        return $adminUserExtendedInfo;
    }

    private function getAdminUserExtendedInfo2(): AdminUserExtendedInfo
    {
        $adminUserChangedExtendedInfo = new AdminUserExtendedInfo();
        $adminUserChangedExtendedInfo->setEveningPhone('555444555');
        $adminUserChangedExtendedInfo->setCellularPhone('666555666');
        $adminUserChangedExtendedInfo->setReceivesNewsletter(true);
        $adminUserChangedExtendedInfo->setEmailInvalid(true);
        $adminUserChangedExtendedInfo->setCreditRating('1500');
        $adminUserChangedExtendedInfo->setUrl('https://www.example.com');

        return $adminUserChangedExtendedInfo;
    }

    private function mapAddressToDatabaseCountryId(string $countryName): string
    {
        return match ($countryName) {
            self::GERMANY => 'testcountry_de',
            self::BELGIUM => 'testcountry_be',
            '' => '',
        };
    }
}
