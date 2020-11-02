<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use Exception;

/**
 * Admin article files parameters manager.
 * Collects and updates (on user submit) files.
 * Admin Menu: Manage Products -> Articles -> Files.
 */
class ArticleFiles extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'article_files.tpl';

    /**
     * Stores editing article.
     *
     * @var \OxidEsales\Eshop\Application\Model\Article
     */
    protected $_oArticle = null;

    /**
     * Collects available article axtended parameters, passes them to
     * Smarty engine and returns tamplate file name "article_extend.tpl".
     *
     * @return string
     */
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
            $this->_aViewData['oxparentid'] = $oArticle->oxarticles__oxparentid->value;
        }

        return $this->_sThisTemplate;
    }

    /**
     * Saves editing article changes (oxisdownloadable)
     * and updates oxFile object which are associated with editing object.
     */
    public function save(): void
    {
        // save article changes
        $aArticleChanges = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval');
        $oArticle = $this->getArticle();
        $oArticle->assign($aArticleChanges);
        $oArticle->save();

        //update article files
        $aArticleFiles = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('article_files');
        if (\is_array($aArticleFiles)) {
            foreach ($aArticleFiles as $sArticleFileId => $aArticleFileUpdate) {
                $oArticleFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
                $oArticleFile->load($sArticleFileId);
                $aArticleFileUpdate = $this->_processOptions($aArticleFileUpdate);
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
     * Returns current oxarticle object.
     *
     * @param bool $blReset Load article again
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    public function getArticle($blReset = false)
    {
        if (null !== $this->_oArticle && !$blReset) {
            return $this->_oArticle;
        }
        $sProductId = $this->getEditObjectId();

        $oProduct = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oProduct->load($sProductId);

        return $this->_oArticle = $oProduct;
    }

    /**
     * Creates new oxFile object and stores newly uploaded file.
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

        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('newfile');
        $aParams = $this->_processOptions($aParams);
        $aNewFile = \OxidEsales\Eshop\Core\Registry::getConfig()->getUploadedFile('newArticleFile');

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
        $sArticleFileId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('fileid');
        $oArticleFile = oxNew(\OxidEsales\Eshop\Application\Model\File::class);
        $oArticleFile->load($sArticleFileId);
        if ($oArticleFile->hasValidDownloads()) {
            return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_DELETING_VALID_FILE');
        }
        if ($oArticleFile->oxfiles__oxartid->value === $sArticleId) {
            $oArticleFile->delete();
        }
    }

    /**
     * Returns real config option value.
     *
     * @param int $iOption option value
     *
     * @return int
     */
    public function getConfigOptionValue($iOption)
    {
        return $iOption < 0 ? '' : $iOption;
    }

    /**
     * Process config options. If value is not set, save as "-1" to database.
     *
     * @param array $aParams params
     *
     * @return array
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "processOptions" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _processOptions($aParams)
    {
        if (!\is_array($aParams)) {
            $aParams = [];
        }

        if (!isset($aParams['oxfiles__oxdownloadexptime']) || '' === $aParams['oxfiles__oxdownloadexptime']) {
            $aParams['oxfiles__oxdownloadexptime'] = -1;
        }
        if (!isset($aParams['oxfiles__oxlinkexptime']) || '' === $aParams['oxfiles__oxlinkexptime']) {
            $aParams['oxfiles__oxlinkexptime'] = -1;
        }
        if (!isset($aParams['oxfiles__oxmaxunregdownloads']) || '' === $aParams['oxfiles__oxmaxunregdownloads']) {
            $aParams['oxfiles__oxmaxunregdownloads'] = -1;
        }
        if (!isset($aParams['oxfiles__oxmaxdownloads']) || '' === $aParams['oxfiles__oxmaxdownloads']) {
            $aParams['oxfiles__oxmaxdownloads'] = -1;
        }

        return $aParams;
    }
}
