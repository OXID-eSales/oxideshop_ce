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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxException;
use oxExceptionToDisplay;
use oxRegistry;

/**
 * Article file download page.
 *
 */
class DownloadController extends \oxUBase
{
    /**
     * Prevents from loading any component as this controller
     * only returns file content if token is valid
     */
    public function init()
    {
        // empty for performance reasons
    }

    /**
     * Checks if given token is valid, formats HTTP headers,
     * and outputs file to buffer.
     *
     * If token is not valid, redirects to start page.
     */
    public function render()
    {
        $sFileOrderId = oxRegistry::getConfig()->getRequestParameter('sorderfileid');

        if ($sFileOrderId) {
            $oArticleFile = oxNew('oxFile');
            try {
                /** @var oxOrderFile $oOrderFile */
                $oOrderFile = oxNew('oxOrderFile');
                if ($oOrderFile->load($sFileOrderId)) {
                    $sFileId = $oOrderFile->getFileId();
                    $blLoadedAndExists = $oArticleFile->load($sFileId) && $oArticleFile->exist();
                    if ($sFileId && $blLoadedAndExists && $oOrderFile->processOrderFile()) {
                        $oArticleFile->download();
                    } else {
                        $sError = "ERROR_MESSAGE_FILE_DOESNOT_EXIST";
                    }
                }
            } catch (oxException $oEx) {
                $sError = "ERROR_MESSAGE_FILE_DOWNLOAD_FAILED";
            }
        } else {
            $sError = "ERROR_MESSAGE_WRONG_DOWNLOAD_LINK";
        }
        if ($sError) {
            $oEx = new oxExceptionToDisplay();
            $oEx->setMessage($sError);
            oxRegistry::get("oxUtilsView")->addErrorToDisplay($oEx, false);
            oxRegistry::getUtils()->redirect(oxRegistry::getConfig()->getShopUrl() . 'index.php?cl=account_downloads');
        }
    }
}
