<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\GenericImport;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\Core\GenericImport\Fixtures\FailingImport;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Filesystem\Path;

final class GenericImportTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $csvFile;

    public function setUp(): void
    {
        parent::setUp();

        $this->csvFile = Path::join(sys_get_temp_dir(), uniqid('generic-import-', true) . '.csv');
        (new Filesystem())->dumpFile($this->csvFile, "\"some-id\";\"some-title\"\n");
        $this->signInMallAdmin();
    }

    public function tearDown(): void
    {
        (new Filesystem())->remove($this->csvFile);
        Registry::getSession()->deleteVariable('auth');

        parent::tearDown();
    }

    public function testImportFileReportsFailureWithoutOutput(): void
    {
        $this->expectOutputString('');

        $returnMessage = (new FailingImport())->importFile($this->csvFile);

        $this->assertSame('ERPGENIMPORT_ERROR_DURING_IMPORT', $returnMessage);
    }

    public function testImportFileRegistersFailureInStatistics(): void
    {
        $import = new FailingImport();

        $import->importFile($this->csvFile);

        $this->assertSame('import failed', $import->getStatistics()[0]['m']);
    }

    private function signInMallAdmin(): void
    {
        $user = oxNew(User::class);
        $user->setId('generic-import-admin');
        $user->oxuser__oxusername = new Field('generic-import-admin@example.com');
        $user->oxuser__oxshopid = new Field(Registry::getConfig()->getShopId());
        $user->oxuser__oxactive = new Field(1);
        $user->save();
        $this->get(QueryBuilderFactoryInterface::class)->create()
            ->update('oxuser')
            ->set('oxrights', ':rights')
            ->where('oxid = :oxid')
            ->setParameters(['rights' => 'malladmin', 'oxid' => 'generic-import-admin'])
            ->executeStatement();

        Registry::getSession()->setVariable('auth', 'generic-import-admin');
    }
}
