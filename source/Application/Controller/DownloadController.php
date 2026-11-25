<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Application\Model\File;
use OxidEsales\Eshop\Application\Model\OrderFile;
use OxidEsales\Eshop\Core\Exception\ExceptionToDisplay;
use OxidEsales\Eshop\Core\Exception\StandardException;
use OxidEsales\Eshop\Core\Registry;

/**
 * Article file download page.
 */
class DownloadController extends \OxidEsales\Eshop\Application\Controller\FrontendController
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
     * Checks if given token is valid and returns the file as download response.
     *
     * If token is not valid, redirects to the account downloads page.
     */
    public function render()
    {
        $orderFileId = Registry::getRequest()->getRequestEscapedParameter('sorderfileid');
        $error = "ERROR_MESSAGE_WRONG_DOWNLOAD_LINK";

        if ($orderFileId) {
            $file = oxNew(File::class);
            try {
                /** @var OrderFile $orderFile */
                $orderFile = oxNew(OrderFile::class);
                if ($orderFile->load($orderFileId)) {
                    $fileId = $orderFile->getFileId();
                    $fileExists = $file->load($fileId) && $file->exist();
                    if ($fileId && $fileExists && $orderFile->processOrderFile()) {
                        return $file->download();
                    } else {
                        $error = "ERROR_MESSAGE_FILE_DOESNOT_EXIST";
                    }
                }
            } catch (StandardException) {
                $error = "ERROR_MESSAGE_FILE_DOWNLOAD_FAILED";
            }
        }

        $exception = new ExceptionToDisplay();
        $exception->setMessage($error);
        Registry::getUtilsView()->addErrorToDisplay($exception, false);
        Registry::getUtils()->redirect(Registry::getConfig()->getShopUrl() . 'index.php?cl=account_downloads');
    }
}
