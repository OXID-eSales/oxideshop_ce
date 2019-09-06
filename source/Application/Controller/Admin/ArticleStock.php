<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use stdClass;

/**
 * Admin article inventory manager.
 * Collects such information about article as stock quantity, delivery status,
 * stock message, etc; Updates information (on user submit).
 * Admin Menu: Manage Products -> Articles -> Inventory.
 */
class ArticleStock extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article Inventory information and
     * returns the name of template file.
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $this->_aViewData["edit"] = $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oArticle->loadInLang($this->_iEditLang, $soxId);

            // load object in other languages
            $oOtherLang = $oArticle->getAvailableInLangs();
            if (!isset($oOtherLang[$this->_iEditLang])) {
                // echo "language entry doesn't exist! using: ".key($oOtherLang);
                $oArticle->loadInLang(key($oOtherLang), $soxId);
            }

            foreach ($oOtherLang as $id => $language) {
                $oLang = new stdClass();
                $oLang->sLangDesc = $language;
                $oLang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $oLang;
            }

            if ($oArticle->isDerived()) {
                $this->_aViewData['readonly'] = true;
            }

            // variant handling
            if ($oArticle->oxarticles__oxparentid->value) {
                $oParentArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                $oParentArticle->load($oArticle->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] = $oParentArticle;
                $this->_aViewData["oxparentid"] = $oArticle->oxarticles__oxparentid->value;
            }

            if ($myConfig->getConfigParam('blMallInterchangeArticles')) {
                $sShopSelect = '1';
            } else {
                $sShopID = $myConfig->getShopID();
                $sShopSelect = " oxshopid =  '$sShopID' ";
            }

            $oPriceList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oPriceList->init('oxbase', "oxprice2article");
            $sQ = "select * from oxprice2article where oxartid = :oxartid " .
                  "and {$sShopSelect} and (oxamount > 0 or oxamountto > 0) order by oxamount ";
            $oPriceList->selectstring($sQ, [
                ':oxartid' => $soxId
            ]);

            $this->_aViewData["amountprices"] = $oPriceList;
        }

        return "article_stock.tpl";
    }

    /**
     * Saves article Inventori information changes.
     */
    public function save()
    {
        parent::save();

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->loadInLang($this->_iEditLang, $soxId);

        $oArticle->setLanguage(0);

        // checkbox handling
        if (!$oArticle->oxarticles__oxparentid->value && !isset($aParams['oxarticles__oxremindactive'])) {
            $aParams['oxarticles__oxremindactive'] = 0;
        }

        $oArticle->assign($aParams);

        //tells to article to save in different language
        $oArticle->setLanguage($this->_iEditLang);
        $oArticle = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oArticle);

        $oArticle->resetRemindStatus();

        $oArticle->updateVariantsRemind();

        $oArticle->save();
    }

    /**
     * Adds or updates amount price to article
     *
     * @param string $sOXID         Object ID
     * @param array  $aUpdateParams Parameters
     *
     * @return null
     */
    public function addprice($sOXID = null, $aUpdateParams = null)
    {
        $myConfig = $this->getConfig();

        $this->resetContentCache();

        $sOxArtId = $this->getEditObjectId();
        $this->onArticleAmountPriceChange($sOxArtId);

        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        if (!is_array($aParams)) {
            return;
        }

        if (isset($aUpdateParams) && is_array($aUpdateParams)) {
            $aParams = array_merge($aParams, $aUpdateParams);
        }

        //replacing commas
        foreach ($aParams as $key => $sParam) {
            $aParams[$key] = str_replace(",", ".", $sParam);
        }

        $aParams['oxprice2article__oxshopid'] = $myConfig->getShopID();

        if (isset($sOXID)) {
            $aParams['oxprice2article__oxid'] = $sOXID;
        }

        $aParams['oxprice2article__oxartid'] = $sOxArtId;
        if (!isset($aParams['oxprice2article__oxamount']) || !$aParams['oxprice2article__oxamount']) {
            $aParams['oxprice2article__oxamount'] = "1";
        }

        if (!$myConfig->getConfigParam('blAllowUnevenAmounts')) {
            $aParams['oxprice2article__oxamount'] = round(( string ) $aParams['oxprice2article__oxamount']);
            $aParams['oxprice2article__oxamountto'] = round(( string ) $aParams['oxprice2article__oxamountto']);
        }

        $dPrice = $aParams['price'];
        $sType = $aParams['pricetype'];

        $oArticlePrice = oxNew(\OxidEsales\Eshop\Core\Model\BaseModel::class);
        $oArticlePrice->init("oxprice2article");
        $oArticlePrice->assign($aParams);

        $oArticlePrice->$sType = new \OxidEsales\Eshop\Core\Field($dPrice);

        //validating
        if ($oArticlePrice->$sType->value &&
            $oArticlePrice->oxprice2article__oxamount->value &&
            $oArticlePrice->oxprice2article__oxamountto->value &&
            is_numeric($oArticlePrice->$sType->value) &&
            is_numeric($oArticlePrice->oxprice2article__oxamount->value) &&
            is_numeric($oArticlePrice->oxprice2article__oxamountto->value) &&
            $oArticlePrice->oxprice2article__oxamount->value <= $oArticlePrice->oxprice2article__oxamountto->value
        ) {
            $oArticlePrice->save();
        }

        // check if abs price is lower than base price
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->loadInLang($this->_iEditLang, $sOxArtId);
        $sPriceField = 'oxarticles__oxprice';
        if (($aParams['price'] >= $oArticle->$sPriceField->value) &&
            ($aParams['pricetype'] == 'oxprice2article__oxaddabs')) {
            if (is_null($sOXID)) {
                $sOXID = $oArticlePrice->getId();
            }
            $this->_aViewData["errorscaleprice"][] = $sOXID;
        }
    }

    /**
     * Updates all amount prices for article at once
     */
    public function updateprices()
    {
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("updateval");
        if (is_array($aParams)) {
            foreach ($aParams as $soxId => $aStockParams) {
                $this->addprice($soxId, $aStockParams);
            }
        }

        $sOxArtId = $this->getEditObjectId();
        $this->onArticleAmountPriceChange($sOxArtId);
    }


    /**
     * Adds amount price to article
     */
    public function deleteprice()
    {
        $this->resetContentCache();

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $articleId = $this->getEditObjectId();
        $oDb->execute("delete from oxprice2article where oxid = :oxid and oxartid = :oxartid", [
            ':oxid' => \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("priceid"),
            ':oxartid' => $articleId
        ]);

        $this->onArticleAmountPriceChange($articleId);
    }

    /**
     * Method is used to bind to article amount price change.
     *
     * @param string $articleId
     */
    protected function onArticleAmountPriceChange($articleId)
    {
    }
}
