<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\ToolsList;
use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ToolsListTest extends IntegrationTestCase
{
    public function tearDown(): void
    {
        unset($_FILES['myfile']);
        Registry::getSession()->deleteVariable('auth');
        Registry::getSession()->deleteVariable('Errors');

        parent::tearDown();
    }

    public function testSqlFileWithNotAllowedTypeShowsTheMessageAndRunsNothing(): void
    {
        $this->signInMallAdmin();
        $_FILES['myfile'] = [
            'name' => ['sqlfile' => 'dump.php'],
            'tmp_name' => ['sqlfile' => '/tmp/dump.php'],
        ];
        $toolsList = oxNew(ToolsList::class);

        $toolsList->performsql();

        $this->assertSame(
            [[Registry::getLang()->translateString('ERROR_MESSAGE_WRONG_IMAGE_FILE_TYPE')], false],
            [
                array_map(
                    static fn (string $error): string => unserialize($error)->getOxMessage(),
                    Registry::getSession()->getVariable('Errors')['default']
                ),
                array_key_exists('aQueries', $toolsList->getViewData()),
            ]
        );
    }

    private function signInMallAdmin(): void
    {
        $user = oxNew(User::class);
        $user->setId('tools-list-admin');
        $user->oxuser__oxusername = new Field('tools-list-admin@example.com');
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->save();
        ContainerFacade::get(QueryBuilderFactoryInterface::class)->create()
            ->update('oxuser')
            ->set('oxrights', ':rights')
            ->where('oxid = :oxid')
            ->setParameters(['rights' => 'malladmin', 'oxid' => 'tools-list-admin'])
            ->executeStatement();

        Registry::getSession()->setVariable('auth', 'tools-list-admin');
    }
}
