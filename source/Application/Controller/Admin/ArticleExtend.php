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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use stdClass;
use Exception;

/**
 * Admin article extended parameters manager.
 * Collects and updates (on user submit) extended article properties ( such as
 * weight, dimensions, purchase Price and etc.). There is ability to assign article
 * to any chosen article group.
 * Admin Menu: Manage Products -> Articles -> Extended.
 */
class ArticleExtend extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Unit array
     *
     * @var array
     */
    protected $_aUnitsArray = null;

    /**
     * Collects available article extended parameters, passes them to
     * Smarty engine and returns template file name "article_extend.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        $oxId = $this->getEditObjectId();

        $this->_createCategoryTree("artcattree");

        // all categories
        if (isset($oxId) && $oxId != "-1") {
            // load object
            $article->loadInLang($this->_iEditLang, $oxId);

            $article = $this->updateArticle($article);

            // load object in other languages
            $otherLang = $article->getAvailableInLangs();
            if (!isset($otherLang[$this->_iEditLang])) {
                $article->loadInLang(key($otherLang), $oxId);
            }

            foreach ($otherLang as $id => $language) {
                $lang = new stdClass();
                $lang->sLangDesc = $language;
                $lang->selected = ($id == $this->_iEditLang);
                $this->_aViewData["otherlang"][$id] = clone $lang;
            }

            // variant handling
            if ($article->oxarticles__oxparentid->value) {
                $parentArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                $parentArticle->load($article->oxarticles__oxparentid->value);
                $this->_aViewData["parentarticle"] = $parentArticle;
                $this->_aViewData["oxparentid"] = $article->oxarticles__oxparentid->value;
            }
        }

        $this->prepareBundledArticlesDataForView($article);

        $iAoc = $this->getConfig()->getRequestParameter("aoc");
        if ($iAoc == 1) {
            $oArticleExtendAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleExtendAjax::class);
            $this->_aViewData['oxajax'] = $oArticleExtendAjax->getColumns();

            return "popups/article_extend.tpl";
        } elseif ($iAoc == 2) {
            $oArticleBundleAjax = oxNew(\OxidEsales\Eshop\Application\Controller\Admin\ArticleBundleAjax::class);
            $this->_aViewData['oxajax'] = $oArticleBundleAjax->getColumns();

            return "popups/article_bundle.tpl";
        }

        //load media files
        $this->_aViewData['aMediaUrls'] = $article->getMediaUrls();

        return "article_extend.tpl";
    }

    /**
     * Saves modified extended article parameters.
     *
     * @return mixed
     */
    public function save()
    {
        parent::save();

        $aMyFile = $this->getConfig()->getUploadedFile("myfile");
        $aMediaFile = $this->getConfig()->getUploadedFile("mediaFile");
        if (is_array($aMyFile['name']) && reset($aMyFile['name']) || $aMediaFile['name']) {
            $myConfig = $this->getConfig();
            if ($myConfig->isDemoShop()) {
                $oEx = oxNew(\OxidEsales\Eshop\Core\Exception\ExceptionToDisplay::class);
                $oEx->setMessage('ARTICLE_EXTEND_UPLOADISDISABLED');
                \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($oEx, false);

                return;
            }
        }

        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");
        // checkbox handling
        if (!isset($aParams['oxarticles__oxissearch'])) {
            $aParams['oxarticles__oxissearch'] = 0;
        }
        if (!isset($aParams['oxarticles__oxblfixedprice'])) {
            $aParams['oxarticles__oxblfixedprice'] = 0;
        }

        // new way of handling bundled articles
        //#1517C - remove possibility to add Bundled Product
        //$this->setBundleId($aParams, $soxId);

        // default values
        $aParams = $this->addDefaultValues($aParams);

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->loadInLang($this->_iEditLang, $soxId);
        $sTPriceField = 'oxarticles__oxtprice';
        $sPriceField = 'oxarticles__oxprice';
        $dTPrice = $aParams['oxarticles__oxtprice'];
        if ($dTPrice && $dTPrice != $oArticle->$sTPriceField->value && $dTPrice <= $oArticle->$sPriceField->value) {
            $this->_aViewData["errorsavingtprice"] = 1;
        }

        $oArticle->setLanguage(0);
        $oArticle->assign($aParams);
        $oArticle->setLanguage($this->_iEditLang);
        $oArticle = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFiles($oArticle);
        $oArticle->save();

        //saving media file
        $sMediaUrl = $this->getConfig()->getRequestParameter("mediaUrl");
        $sMediaDesc = $this->getConfig()->getRequestParameter("mediaDesc");

        if (($sMediaUrl && $sMediaUrl != 'http://') || $aMediaFile['name'] || $sMediaDesc) {
            if (!$sMediaDesc) {
                return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_NODESCRIPTIONADDED');
            }

            if ((!$sMediaUrl || $sMediaUrl == 'http://') && !$aMediaFile['name']) {
                return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay('EXCEPTION_NOMEDIAADDED');
            }

            $oMediaUrl = oxNew(\OxidEsales\Eshop\Application\Model\MediaUrl::class);
            $oMediaUrl->setLanguage($this->_iEditLang);
            $oMediaUrl->oxmediaurls__oxisuploaded = new \OxidEsales\Eshop\Core\Field(0, \OxidEsales\Eshop\Core\Field::T_RAW);

            //handle uploaded file
            if ($aMediaFile['name']) {
                try {
                    $sMediaUrl = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->processFile('mediaFile', 'out/media/');
                    $oMediaUrl->oxmediaurls__oxisuploaded = new \OxidEsales\Eshop\Core\Field(1, \OxidEsales\Eshop\Core\Field::T_RAW);
                } catch (Exception $e) {
                    return \OxidEsales\Eshop\Core\Registry::getUtilsView()->addErrorToDisplay($e->getMessage());
                }
            }

            //save media url
            $oMediaUrl->oxmediaurls__oxobjectid = new \OxidEsales\Eshop\Core\Field($soxId, \OxidEsales\Eshop\Core\Field::T_RAW);
            $oMediaUrl->oxmediaurls__oxurl = new \OxidEsales\Eshop\Core\Field($sMediaUrl, \OxidEsales\Eshop\Core\Field::T_RAW);
            $oMediaUrl->oxmediaurls__oxdesc = new \OxidEsales\Eshop\Core\Field($sMediaDesc, \OxidEsales\Eshop\Core\Field::T_RAW);
            $oMediaUrl->save();
        }

        // renew price update time
        oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class)->renewPriceUpdateTime();
    }

    /**
     * Deletes media url (with possible linked files)
     */
    public function deletemedia()
    {
        $soxId = $this->getEditObjectId();
        $sMediaId = $this->getConfig()->getRequestParameter("mediaid");
        if ($sMediaId && $soxId) {
            $oMediaUrl = oxNew(\OxidEsales\Eshop\Application\Model\MediaUrl::class);
            $oMediaUrl->load($sMediaId);
            $oMediaUrl->delete();
        }
    }

    /**
     * Adds default values for extended article parameters. Returns modified
     * parameters array.
     *
     * @param array $aParams Article parameters array
     *
     * @return array
     */
    public function addDefaultValues($aParams)
    {
        $aParams['oxarticles__oxexturl'] = str_replace("http://", "", $aParams['oxarticles__oxexturl']);

        return $aParams;
    }

    /**
     * Updates existing media descriptions
     */
    public function updateMedia()
    {
        $aMediaUrls = $this->getConfig()->getRequestParameter('aMediaUrls');
        if (is_array($aMediaUrls)) {
            foreach ($aMediaUrls as $sMediaId => $aMediaParams) {
                $oMedia = oxNew(\OxidEsales\Eshop\Application\Model\MediaUrl::class);
                if ($oMedia->load($sMediaId)) {
                    $oMedia->setLanguage(0);
                    $oMedia->assign($aMediaParams);
                    $oMedia->setLanguage($this->_iEditLang);
                    $oMedia->save();
                }
            }
        }
    }

    /**
     * Returns array of possible unit combination and its translation for edit language
     *
     * @return array
     */
    public function getUnitsArray()
    {
        if ($this->_aUnitsArray === null) {
            $this->_aUnitsArray = \OxidEsales\Eshop\Core\Registry::getLang()->getSimilarByKey("_UNIT_", $this->_iEditLang, false);
        }

        return $this->_aUnitsArray;
    }

    /**
     * Method used to overload and update article.
     *
     * @param \oxArticle $article
     *
     * @return \oxArticle
     */
    protected function updateArticle($article)
    {
        return $article;
    }

    /**
     * Adds data to _aViewData for later use in templates.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     */
    protected function prepareBundledArticlesDataForView($article)
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDB();
        $config = $this->getConfig();

        $articleTable = getViewName('oxarticles', $this->_iEditLang);
        $query = "select {$articleTable}.oxtitle, {$articleTable}.oxartnum, {$articleTable}.oxvarselect " .
            "from {$articleTable} where 1 ";
        // #546
        $isVariantSelectionEnabled = $config->getConfigParam('blVariantsSelection');
        $bundleIdField = 'oxarticles__oxbundleid';
        $query .= $isVariantSelectionEnabled ? '' : " and {$articleTable}.oxparentid = '' ";
        $query .= " and {$articleTable}.oxid = " . $database->quote($article->$bundleIdField->value);

        $resultFromDatabase = $database->select($query);
        if ($resultFromDatabase != false && $resultFromDatabase->count() > 0) {
            while (!$resultFromDatabase->EOF) {
                $articleNumber = new \OxidEsales\Eshop\Core\Field($resultFromDatabase->fields[1]);
                $articleTitle = new \OxidEsales\Eshop\Core\Field($resultFromDatabase->fields[0] . " " . $resultFromDatabase->fields[2]);
                $resultFromDatabase->fetchRow();
            }
        }
        $this->_aViewData['bundle_artnum'] = $articleNumber;
        $this->_aViewData['bundle_title'] = $articleTitle;
    }
}
