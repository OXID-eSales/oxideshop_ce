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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */

namespace Integration\Core;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Database;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;
use ReflectionClass;
use OxidEsales\Eshop\Core\Exception\DatabaseConnectionException;
use OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException;

/**
 * Class DatabaseTest
 * 
 * @group database-adapter
 * @covers OxidEsales\Eshop\Core\Database
 */
class DatabaseTest extends UnitTestCase
{
    public function testGetDbThrowsDatabaseConnectionException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored  */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        Registry::set('oxConfigFile',$configFile);
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , null);

        $this->setExpectedException('OxidEsales\Eshop\Core\Exception\DatabaseConnectionException');

        try {
            Database::getDb();
        } catch (DatabaseConnectionException $exception ) {
            /** Restore original configFile object */
            Registry::set('oxConfigFile',$configFileBackup);
            throw $exception;
        }
    }

    public function testGetDbThrowsDatabaseNotConfiguredException()
    {
        /** @var ConfigFile $configFileBackup Backup of the configFile as stored in Registry. This object must be restored  */
        $configFileBackup = Registry::get('oxConfigFile');

        $configFile = $this->getBlankConfigFile();
        $configFile->setVar('dbHost','<');
        Registry::set('oxConfigFile',$configFile);
        $this->setProtectedClassProperty(Database::getInstance(), 'db' , null);

        $this->setExpectedException('OxidEsales\Eshop\Core\Exception\DatabaseNotConfiguredException');
        
        try {
            Database::getDb();            
        } catch (DatabaseNotConfiguredException $exception ) {
            /** Restore original configFile object */
            Registry::set('oxConfigFile',$configFileBackup);
            throw $exception;
        }
    }

    /**
     * Helper methods
     */

    /**
     * @return ConfigFile
     */
    protected function getBlankConfigFile()
    {
        return new ConfigFile($this->createFile('config.inc.php', '<?php '));
    }
}
