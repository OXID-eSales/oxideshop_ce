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

require_once LIBRARY_PATH.'/FileUploader.php';
require_once 'DbHandler.php';

/**
 * Shop constructor class for modifying shop environment during testing
 * Class ShopConstructor
 */
class ShopPreparation implements ShopServiceInterface
{
    /** @var DbHandler Database communicator object */
    private $_dbHandler = null;

    /**
     * Handles request parameters.
     */
    public function init()
    {
        $oConfig = oxRegistry::getConfig();

        if ($oConfig->getUploadedFile('importSql')) {
            $this->_importSqlFromUploadedFile();
        }

        if ($oConfig->getRequestParameter('dumpDB')) {
            $oDbHandler = $this->_getDbHandler();
            $oDbHandler->dumpDB($oConfig->getRequestParameter('dump-prefix'));
        }

        if ($oConfig->getRequestParameter('restoreDB')) {
            $oDbHandler = $this->_getDbHandler();
            $oDbHandler->restoreDB($oConfig->getRequestParameter('dump-prefix'));
        }
    }

    /**
     * Imports uploaded file with containing sql to shop.
     */
    private function _importSqlFromUploadedFile()
    {
        $oFileUploader = new FileUploader();
        $sFilePath = TEMP_PATH.'/import.sql';
        $oFileUploader->uploadFile('importSql', $sFilePath);

        $oDbHandler = $this->_getDbHandler();
        $oDbHandler->import($sFilePath);
    }

    /**
     * Returns Database handler object.
     *
     * @return DbHandler
     */
    private function _getDbHandler()
    {
        if (!$this->_dbHandler) {
            $this->_dbHandler = new DbHandler();
            $this->_dbHandler->setTemporaryFolder(TEMP_PATH);
        }

        return $this->_dbHandler;
    }
}
