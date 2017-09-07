<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Setup;

use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\EshopCommunity\Setup\Utilities;
use OxidEsales\Facts\Facts;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;

use oxDatabaseHelper;
require_once TEST_LIBRARY_HELPERS_PATH . 'oxDatabaseHelper.php';

class UtilitiesTest extends \OxidEsales\TestingLibrary\UnitTestCase
{
    public function setUp()
    {
        parent::setUp();

        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());
        $databaseHelper->adjustTemplateBlocksOxModuleColumn();

        $this->resetMigrationTable();
    }

    public function testExecuteExternalRegenerateViewsCommand()
    {
        $this->assertViewExists('oxdiscount');

        $this->dropOxDiscountView();
        $this->assertViewNotExists('oxdiscount');

        $utilities = new Utilities();
        $utilities->executeExternalRegenerateViewsCommand();

        $this->assertViewExists('oxdiscount');
    }

    public function testExecuteExternalDatabaseMigrationCommand()
    {
        $output = new ConsoleOutput();
        $output->setVerbosity(ConsoleOutputInterface::VERBOSITY_QUIET);

        $this->assertOxModuleColumnHasMaxLength(32);

        $utilities = new Utilities();
        $utilities->executeExternalDatabaseMigrationCommand($output);

        $this->assertOxModuleColumnHasMaxLength(100);
    }

    public function testExecuteExternalDemodataAssetsInstallCommand()
    {
        $utilities = new Utilities();
        $packagePath = $utilities->getActiveEditionDemodataPackagePath();
        $demodataShouldBeInstalled = file_exists($packagePath);

        if ($demodataShouldBeInstalled) {
            $errorCode = $utilities->executeExternalDemodataAssetsInstallCommand();

            $this->assertEquals(0, $errorCode);

            $database = DatabaseProvider::getDb();

            $sql = "SELECT `OXTITLE` FROM `oxdelivery` WHERE oxid = ?";
            $oxtitle = $database->getOne($sql, ['1b842e734b62a4775.45738618']);

            $this->assertEquals('Versandkosten für Standard: Versandkostenfrei ab 80,-', $oxtitle);

            $facts = new Facts();
            $expectedFile = $facts->getOutPath() . '/pictures/media/dir.txt';

            $this->assertFileExists($expectedFile);
        }
    }

    /**
     * @param int $expectedMaxLength
     */
    private function assertOxModuleColumnHasMaxLength($expectedMaxLength)
    {
        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());

        $fieldInformation = $databaseHelper->getFieldInformation('oxtplblocks', 'OXMODULE');

        $this->assertEquals($expectedMaxLength, $fieldInformation->max_length);
    }

    protected function dropOxDiscountView()
    {
        $databaseHelper = new oxDatabaseHelper(DatabaseProvider::getDb());

        $databaseHelper->dropView('oxdiscount');
    }

    /** @todo: Needs rewrite / will most likely be removed */
    protected function resetMigrationTable()
    {
        $database = DatabaseProvider::getDb();
        $database->execute("TRUNCATE oxmigrations_ce");
    }
}
