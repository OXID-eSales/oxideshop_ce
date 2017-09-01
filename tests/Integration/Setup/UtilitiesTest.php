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

class UtilitiesTest extends \OxidTestCase
{
    protected function getOxModuleColumnInformation()
    {
        $database = DatabaseProvider::getDb();
        $columns = $database->metaColumns('oxtplblocks');
        foreach($columns as $column) {
            if ($column->name === 'OXMODULE') {

                return $column;
            }
        }
    }

    public function testExecuteExternalDatabaseMigrationCommand()
    {
        $column = $this->getOxModuleColumnInformation();
        $this->assertEquals(32, $column->max_length);

        $utilities = new Utilities();
        $utilities->executeExternalDatabaseMigrationCommand();

        $column = $this->getOxModuleColumnInformation();
        $this->assertEquals(100, $column->max_length);
    }
}
