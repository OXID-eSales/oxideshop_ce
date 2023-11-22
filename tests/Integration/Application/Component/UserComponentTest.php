<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Component;

use OxidEsales\Eshop\Application\Component\UserComponent;
use OxidEsales\Eshop\Application\Controller\FrontendController;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Session;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class UserComponentTest extends IntegrationTestCase
{
    public function testCreateUserFields(): void
    {
        Registry::getConfig()->setConfigParam('blPsLoginEnabled', true);

        $userComponentMock = $this->getUserComponent();
        $userComponentMock->createUser();
        $user = $this->fetchUserData();

        $this->assertEquals('fname', $user['OXFNAME']);
        $this->assertEquals('lname', $user['OXLNAME']);
        $this->assertEquals('street', $user['OXSTREET']);
        $this->assertEquals('zip', $user['OXZIP']);
        $this->assertEquals('nr', $user['OXSTREETNR']);
        $this->assertEquals('city', $user['OXCITY']);
        $this->assertEquals('a7c40f631fc920687.20179984', $user['OXCOUNTRYID']);
    }

    public function testCreateUserResponse(): void
    {
        Registry::getConfig()->setConfigParam('blPsLoginEnabled', true);
        $userComponentMock = $this->getUserComponent();
        $createUserReturn = $userComponentMock->createUser();

        $this->assertEquals('payment?new_user=1&success=1', $createUserReturn);
    }

    public function testCreateUserPrivateSales(): void
    {
        Registry::getConfig()->setConfigParam('blPsLoginEnabled', true);

        $userComponentMock = $this->getUserComponent();
        $userComponentMock->createUser();
        $user = $this->fetchUserData();

        $this->assertEquals(0, $user['OXACTIVE']);
    }

    public function testCreateUser(): void
    {
        Registry::getConfig()->setConfigParam('blPsLoginEnabled', false);

        $userComponentMock = $this->getUserComponent();
        $userComponentMock->createUser();
        $user = $this->fetchUserData();

        $this->assertEquals(1, $user['OXACTIVE']);
    }

    private function getUserComponent(): UserComponent
    {
        $rawVal = [
            'oxuser__oxfname' => 'fname',
            'oxuser__oxlname' => 'lname',
            'oxuser__oxstreetnr' => 'nr',
            'oxuser__oxstreet' => 'street',
            'oxuser__oxzip' => 'zip',
            'oxuser__oxcity' => 'city',
            'oxuser__oxcountryid' => 'a7c40f631fc920687.20179984',
            'oxuser__oxactive' => 1
        ];

        $_POST = array_merge($_POST,
            [
                'lgn_usr' => 'test@oxid-esales.com',
                'lgn_pwd' => 'Test@oxid-esales.com',
                'lgn_pwd2' => 'Test@oxid-esales.com',
                'invadr' => $rawVal
            ]
        );

        $fronendController = oxNew(FrontendController::class);
        $userComponent = oxNew(UserComponent::class);
        $userComponent->setParent($fronendController);
        $this->setSessionChallenge();

        return $userComponent;
    }

    private function fetchUserData(): array
    {
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();

        return $queryBuilder
            ->select('*')
            ->from('oxuser')
            ->where('oxusername = :oxusername')
            ->setParameters([
                'oxusername' => 'test@oxid-esales.com',
            ])
        ->execute()
        ->fetch();
    }

    private function setSessionChallenge(): void
    {
        Registry::set(
            Session::class,
            $this->createConfiguredMock(
                Session::class,
                ['checkSessionChallenge' => true]
            )
        );
    }
}
