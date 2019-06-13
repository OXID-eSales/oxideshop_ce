<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Application\Controller;

use oxArticleList;
use OxidEsales\Eshop\Core\Registry;
use oxOrderFileList;
use oxRegistry;

/**
 * Account article file download page.
 */
class AccountDownloadsController extends \OxidEsales\Eshop\Application\Controller\AccountController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/downloads.tpl';

    /**
     * Current view search engine indexing state
     *
     * @var int
     */
    protected $_iViewIndexState = VIEW_INDEXSTATE_NOINDEXNOFOLLOW;

    /**
     * @var \OxidEsales\Eshop\Application\Model\OrderFileList
     */
    protected $_oOrderFilesList = null;


    /**
     * Returns Bread Crumb - you are here page1/page2/page3...
     *
     * @return array
     */
    public function getBreadCrumb()
    {
        $aPaths = [];
        $aPath = [];

        $iBaseLanguage = Registry::getLang()->getBaseLanguage();
        /** @var \OxidEsales\Eshop\Core\SeoEncoder $oSeoEncoder */
        $oSeoEncoder = Registry::getSeoEncoder();
        $aPath['title'] = Registry::getLang()->translateString('MY_ACCOUNT', $iBaseLanguage, false);
        $aPath['link'] = $oSeoEncoder->getStaticUrl($this->getViewConfig()->getSelfLink() . "cl=account");
        $aPaths[] = $aPath;

        $aPath['title'] = Registry::getLang()->translateString('MY_DOWNLOADS', $iBaseLanguage, false);
        $aPath['link'] = $this->getLink();
        $aPaths[] = $aPath;

        return $aPaths;
    }

    /**
     * Returns article list which was ordered and has downloadable files
     *
     * @return null|oxArticleList
     */
    public function getOrderFilesList()
    {
        if ($this->_oOrderFilesList !== null) {
            return $this->_oOrderFilesList;
        }

        $oOrderFileList = oxNew(\OxidEsales\Eshop\Application\Model\OrderFileList::class);
        $oOrderFileList->loadUserFiles($this->getUser()->getId());

        $this->_oOrderFilesList = $this->_prepareForTemplate($oOrderFileList);

        return $this->_oOrderFilesList;
    }

    /**
     * Returns prepared orders files list
     *
     * @param \OxidEsales\Eshop\Application\Model\OrderFileList $oOrderFileList - list or orderfiles
     *
     * @return array
     */
    protected function _prepareForTemplate($oOrderFileList)
    {
        $oOrderArticles = [];

        foreach ($oOrderFileList as $oOrderFile) {
            $sOrderArticleIdField = 'oxorderfiles__oxorderarticleid';
            $sOrderNumberField = 'oxorderfiles__oxordernr';
            $sOrderDateField = 'oxorderfiles__oxorderdate';
            $sOrderTitleField = 'oxorderfiles__oxarticletitle';
            $sOrderArticleId = $oOrderFile->$sOrderArticleIdField->value;
            $oOrderArticles[$sOrderArticleId]['oxordernr'] = $oOrderFile->$sOrderNumberField->value;
            $oOrderArticles[$sOrderArticleId]['oxorderdate'] = substr($oOrderFile->$sOrderDateField->value, 0, 16);
            $oOrderArticles[$sOrderArticleId]['oxarticletitle'] = $oOrderFile->$sOrderTitleField->value;
            $oOrderArticles[$sOrderArticleId]['oxorderfiles'][] = $oOrderFile;
        }

        return $oOrderArticles;
    }

    /**
     * Returns error code.
     *
     * @return int
     */
    public function getDownloadError()
    {
        return $this->getConfig()->getRequestParameter('download_error');
    }
}
