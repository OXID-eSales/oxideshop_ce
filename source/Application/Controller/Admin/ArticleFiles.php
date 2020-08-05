<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use Exception;
use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article files parameters manager.
 * Collects and updates (on user submit) files.
 * Admin Menu: Manage Products -> Articles -> Files.
 */
class ArticleFiles extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'article_files';

    /**
     * Stores editing article
     *
     * @var \OxidEsales\Eshop\Application\Model\Article
     */
    protected $_oArticle = null;

    /** @inheritdoc */
    public function render()
    {
        parent::render();

        if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blEnableDownloads')) {
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_DISABLED_DOWNLOADABLE_PRODUCTS');
        }
        $oArticle = $this->getArticle();
        // variant handling
        if ($oArticle->oxarticles__oxparentid->value) {
            $oParentArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oParentArticle->load($oArticle->oxarticles__oxparentid->value);
            $oArticle->oxarticles__oxisdownloadable = new \OxidEsales\Eshop\Core\Field($oParentArticle->oxarticles__oxisdownloadable->value);
            $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Saves editing article changes (oxisdownloadable)
     * and updates oxFile object which are associated with editing object
     */
    public function save()
    {
        // save article changes
        $aArticleChanges = Registry::getRequest()->getRequestEscapedParameter('editval');
        $oArticle = $this->getArticle();
        $oArticle->assign($aArticleChanges);
        $oArticle->save();

        //update article files
        $aArticleFiles = Registry::getRequest()->getRequestEscapedParameter('article_files');
        if (is_array($aArticleFiles)) {
            foreach ($aArticleFiles as $sArticleFileId => $aArticleFileUpdate) {
                $oArticleFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
                $oArticleFile->load($sArticleFileId);
                $aArticleFileUpdate = $this->processOptions($aArticleFileUpdate);
                $oArticleFile->assign($aArticleFileUpdate);

                if ($oArticleFile->isUnderDownloadFolder()) {
                    $oArticleFile->save();
                } else {
                    \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_NOFILE');
                }
            }
        }
    }

    /**
     * Returns current oxarticle object
     *
     * @param bool $blReset Load article again
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    public function getArticle($blReset = false)
    {
        if ($this->_oArticle !== null && !$blReset) {
            return $this->_oArticle;
        }
        $sProductId = $this->getEditObjectId();

        $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oProduct->load($sProductId);

        return $this->_oArticle = $oProduct;
    }

    /**
     * Creates new oxFile object and stores newly uploaded file
     *
     * @return null
     */
    public function upload()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        if ($myConfig->isDemoShop()) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
            $oEx->setMessage('ARTICLE_EXTEND_UPLOADISDISABLED');
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false);

            return;
        }

        $soxId = $this->getEditObjectId();

        $aParams = Registry::getRequest()->getRequestEscapedParameter("newfile");
        $aParams = $this->processOptions($aParams);
        $aNewFile = \OxidEsales\Eshop\Core\Registry::getConfig()->getUploadedFile("newArticleFile");

        //uploading and processing supplied file
        $oArticleFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
        $oArticleFile->assign($aParams);

        if (!$aNewFile['name'] && !$oArticleFile->oxfiles__oxfilename->value) {
            return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_NOFILE');
        }

        if ($aNewFile['name']) {
            $oArticleFile->oxfiles__oxfilename = new \OxidEsales\Eshop\Core\Field($aNewFile['name'], \OxidEsales\Eshop\Core\Field::T_RAW);
            try {
                $oArticleFile->processFile('newArticleFile');
            } catch (Exception $e) {
                return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
            }
        }

        if (!$oArticleFile->isUnderDownloadFolder()) {
            return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_NOFILE');
        }

        //save media url
        $oArticleFile->oxfiles__oxartid = new \OxidEsales\Eshop\Core\Field($soxId, \OxidEsales\Eshop\Core\Field::T_RAW);
        $oArticleFile->save();
    }

    /**
     * Deletes article file from fileid parameter and checks if this file belongs to current article.
     *
     * @return void
     */
    public function deletefile()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        if ($myConfig->isDemoShop()) {
            $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
            $oEx->setMessage('ARTICLE_EXTEND_UPLOADISDISABLED');
            \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false);

            return;
        }

        $sArticleId = $this->getEditObjectId();
        $sArticleFileId = Registry::getRequest()->getRequestEscapedParameter('fileid');
        $oArticleFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
        $oArticleFile->load($sArticleFileId);
        if ($oArticleFile->hasValidDownloads()) {
            return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_DELETING_VALID_FILE');
        }
        if ($oArticleFile->oxfiles__oxartid->value == $sArticleId) {
            $oArticleFile->delete();
        }
    }

    /**
     * Returns real config option value
     *
     * @param int $iOption option value
     *
     * @return int
     */
    public function getConfigOptionValue($iOption)
    {
        return ($iOption < 0) ? "" : $iOption;
    }

    /**
     * Process config options. If value is not set, save as "-1" to database
     *
     * @param array $aParams params
     *
     * @return array
     */
    protected function processOptions($aParams)
    {
        if (!is_array($aParams)) {
            $aParams = [];
        }

        if (!isset($aParams["oxfiles__oxdownloadexptime"]) || $aParams["oxfiles__oxdownloadexptime"] == "") {
            $aParams["oxfiles__oxdownloadexptime"] = -1;
        }
        if (!isset($aParams["oxfiles__oxlinkexptime"]) || $aParams["oxfiles__oxlinkexptime"] == "") {
            $aParams["oxfiles__oxlinkexptime"] = -1;
        }
        if (!isset($aParams["oxfiles__oxmaxunregdownloads"]) || $aParams["oxfiles__oxmaxunregdownloads"] == "") {
            $aParams["oxfiles__oxmaxunregdownloads"] = -1;
        }
        if (!isset($aParams["oxfiles__oxmaxdownloads"]) || $aParams["oxfiles__oxmaxdownloads"] == "") {
            $aParams["oxfiles__oxmaxdownloads"] = -1;
        }

        return $aParams;
    }
}
