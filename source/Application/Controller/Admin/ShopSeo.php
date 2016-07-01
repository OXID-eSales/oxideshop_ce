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

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Admin shop system setting manager.
 * Collects shop system settings, updates it on user submit, etc.
 * Admin Menu: Main Menu -> Core Settings -> System.
 */
class ShopSeo extends \Shop_Config
{

    /**
     * Active seo url id
     */
    protected $_sActSeoObject = null;

    /**
     * Executes parent method parent::render() and returns name of template
     * file "shop_system.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['subjlang'] = $this->_iEditLang;

        // loading shop
        $oShop = oxNew('oxShop');
        $oShop->loadInLang($this->_iEditLang, $this->_aViewData['edit']->getId());
        $this->_aViewData['edit'] = $oShop;

        // loading static seo urls
        $sQ = "select oxstdurl, oxobjectid from oxseo where oxtype='static' and oxshopid=" . oxDb::getDb()->quote($oShop->getId()) . " group by oxobjectid order by oxstdurl";

        $oList = oxNew('oxlist');
        $oList->init('oxbase', 'oxseo');
        $oList->selectString($sQ);

        $this->_aViewData['aStaticUrls'] = $oList;

        // loading active url info
        $this->_loadActiveUrl($oShop->getId());

        return "shop_seo.tpl";
    }

    /**
     * Loads and sets active url info to view
     *
     * @param int $iShopId active shop id
     */
    protected function _loadActiveUrl($iShopId)
    {
        $sActObject = null;
        if ($this->_sActSeoObject) {
            $sActObject = $this->_sActSeoObject;
        } elseif (is_array($aStatUrl = oxRegistry::getConfig()->getRequestParameter('aStaticUrl'))) {
            $sActObject = $aStatUrl['oxseo__oxobjectid'];
        }

        if ($sActObject && $sActObject != '-1') {
            $this->_aViewData['sActSeoObject'] = $sActObject;

            $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
            $sQ = "select oxseourl, oxlang from oxseo where oxobjectid = " . $oDb->quote($sActObject) . " and oxshopid = " . $oDb->quote($iShopId);
            $oRs = $oDb->select($sQ);
            if ($oRs != false && $oRs->count() > 0) {
                while (!$oRs->EOF) {
                    $aSeoUrls[$oRs->fields['oxlang']] = array($sActObject, $oRs->fields['oxseourl']);
                    $oRs->moveNext();
                }
                $this->_aViewData['aSeoUrls'] = $aSeoUrls;
            }
        }
    }

    /**
     * Saves changed shop configuration parameters.
     */
    public function save()
    {
        // saving config params
        $this->saveConfVars();

        $oShop = oxNew('oxShop');
        if ($oShop->loadInLang($this->_iEditLang, $this->getEditObjectId())) {

            //assigning values
            $oShop->setLanguage(0);
            $oShop->assign(oxRegistry::getConfig()->getRequestParameter('editval'));
            $oShop->setLanguage($this->_iEditLang);
            $oShop->save();

            // saving static url changes
            if (is_array($aStaticUrl = oxRegistry::getConfig()->getRequestParameter('aStaticUrl'))) {
                $this->_sActSeoObject = oxRegistry::get("oxSeoEncoder")->encodeStaticUrls($this->_processUrls($aStaticUrl), $oShop->getId(), $this->_iEditLang);
            }
        }
    }

    /**
     * Goes through urls array and prepares them for saving to db
     *
     * @param array $aUrls urls to process
     *
     * @return array
     */
    protected function _processUrls($aUrls)
    {
        if (isset($aUrls['oxseo__oxstdurl']) && $aUrls['oxseo__oxstdurl']) {
            $aUrls['oxseo__oxstdurl'] = $this->_cleanupUrl($aUrls['oxseo__oxstdurl']);
        }

        if (isset($aUrls['oxseo__oxseourl']) && is_array($aUrls['oxseo__oxseourl'])) {
            foreach ($aUrls['oxseo__oxseourl'] as $iPos => $sUrl) {
                $aUrls['oxseo__oxseourl'][$iPos] = $this->_cleanupUrl($sUrl);
            }
        }

        return $aUrls;
    }

    /**
     * processes urls by fixing "&amp;", "&"
     *
     * @param string $sUrl processable url
     *
     * @return string
     */
    protected function _cleanupUrl($sUrl)
    {
        // replacing &amp; to & or removing double &&
        while ((stripos($sUrl, '&amp;') !== false) || (stripos($sUrl, '&&') !== false)) {
            $sUrl = str_replace('&amp;', '&', $sUrl);
            $sUrl = str_replace('&&', '&', $sUrl);
        }

        // converting & to &amp;
        return str_replace('&', '&amp;', $sUrl);
    }

    /**
     * Resetting SEO ids
     */
    public function dropSeoIds()
    {
        $this->resetSeoData($this->getConfig()->getShopId());
    }

    /**
     * Deletes static url.
     */
    public function deleteStaticUrl()
    {
        $aStaticUrl = oxRegistry::getConfig()->getRequestParameter('aStaticUrl');
        if (is_array($aStaticUrl)) {
            $sObjectid = $aStaticUrl['oxseo__oxobjectid'];
            if ($sObjectid && $sObjectid != '-1') {
                $this->deleteStaticUrlFromDb($sObjectid);
            }
        }
    }

    /**
     * Deletes static url from DB.
     *
     * @param string $staticUrlId
     */
    protected function deleteStaticUrlFromDb($staticUrlId)
    {
        // active shop id
        $shopId = $this->getEditObjectId();
        $db = oxDb::getDb();
        $db->execute("delete from oxseo where oxtype='static' and oxobjectid = " . $db->quote($staticUrlId) . " and oxshopid = " . $db->quote($shopId));
    }
}
