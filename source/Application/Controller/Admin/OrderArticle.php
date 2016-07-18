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
use oxField;

/**
 * Admin order article manager.
 * Collects order articles information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Articles.
 */
class OrderArticle extends \oxAdminDetails
{

    /**
     * Product which was currently found by search
     *
     * @var oxarticle
     */
    protected $_oSearchProduct = null;

    /**
     * Product list:
     *  - if product is not variant - list contains only product which was found by search;
     *  - if product is variant - list consist with variant paret and its variants
     *
     * @var oxlist
     */
    protected $_oSearchProductList = null;

    /**
     * Product found by search. If product is variant - it keeps parent object
     *
     * @var oxarticle
     */
    protected $_oMainSearchProduct = null;

    /**
     * Active order object
     *
     * @var oxorder
     */
    protected $_oEditObject = null;

    /**
     * Executes parent method parent::render(), creates oxorder and oxvoucherlist
     * objects, appends voucherlist information to order object and passes data
     * to Smarty engine, returns name of template file "order_article.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ($oOrder = $this->getEditObject()) {
            $this->_aViewData["edit"] = $oOrder;
            $this->_aViewData["aProductVats"] = $oOrder->getProductVats(true);
        }

        return "order_article.tpl";
    }

    /**
     * Returns editable order object
     *
     * @return oxorder
     */
    public function getEditObject()
    {
        $soxId = $this->getEditObjectId();
        if ($this->_oEditObject === null && isset($soxId) && $soxId != "-1") {
            $this->_oEditObject = oxNew("oxorder");
            $this->_oEditObject->load($soxId);
        }

        return $this->_oEditObject;
    }

    /**
     * Returns user written product number
     *
     * @return string
     */
    public function getSearchProductArtNr()
    {
        return oxRegistry::getConfig()->getRequestParameter('sSearchArtNum');
    }

    /**
     * If possible returns searched/found oxarticle object
     *
     * @return oxarticle | false
     */
    public function getSearchProduct()
    {
        if ($this->_oSearchProduct === null) {
            $this->_oSearchProduct = false;
            $sSearchArtNum = $this->getSearchProductArtNr();

            foreach ($this->getProductList() as $oProduct) {
                if ($oProduct->oxarticles__oxartnum->value == $sSearchArtNum) {
                    $this->_oSearchProduct = $oProduct;
                    break;
                }
            }
        }

        return $this->_oSearchProduct;
    }

    /**
     * Returns product found by search. If product is variant - returns parent object
     *
     * @return object
     */
    public function getMainProduct()
    {
        if ($this->_oMainSearchProduct === null && ($sArtNum = $this->getSearchProductArtNr())) {
            $this->_oMainSearchProduct = false;
            $sArtId = null;

            //get article id
            $oDb = oxDb::getDb(oxDB::FETCH_MODE_ASSOC);
            $sTable = getViewName("oxarticles");
            $sQ = "select oxid, oxparentid from $sTable where oxartnum = " . $oDb->quote($sArtNum) . " limit 1";

            $rs = $oDb->select($sQ);
            if ($rs != false && $rs->count() > 0) {
                $sArtId = $rs->fields['OXPARENTID'] ? $rs->fields['OXPARENTID'] : $rs->fields['OXID'];

                $oProduct = oxNew("oxArticle");
                if ($oProduct->load($sArtId)) {
                    $this->_oMainSearchProduct = $oProduct;
                }
            }
        }

        return $this->_oMainSearchProduct;
    }

    /**
     * Returns product list containing searchable product or its parent and its variants
     *
     * @return oxlist
     */
    public function getProductList()
    {
        if ($this->_oSearchProductList === null) {
            $this->_oSearchProductList = oxNew("oxlist");

            // main search product is found?
            if ($oMainSearchProduct = $this->getMainProduct()) {
                // storing self to first list position
                $this->_oSearchProductList->offsetSet($oMainSearchProduct->getId(), $oMainSearchProduct);

                // adding variants..
                foreach ($oMainSearchProduct->getVariants() as $oVariant) {
                    $this->_oSearchProductList->offsetSet($oVariant->getId(), $oVariant);
                }
            }
        }

        return $this->_oSearchProductList;
    }

    /**
     * Adds article to order list.
     */
    public function addThisArticle()
    {
        $sOxid = oxRegistry::getConfig()->getRequestParameter('aid');
        $dAmount = oxRegistry::getConfig()->getRequestParameter('am');
        $oProduct = oxNew("oxArticle");

        if ($sOxid && $dAmount && $oProduct->load($sOxid)) {

            $sOrderId = $this->getEditObjectId();
            $oOrder = oxNew('oxorder');
            if ($sOrderId && $oOrder->load($sOrderId)) {
                $oOrderArticle = oxNew('oxorderArticle');
                $oOrderArticle->oxorderarticles__oxartid = new oxField($oProduct->getId());
                $oOrderArticle->oxorderarticles__oxartnum = new oxField($oProduct->oxarticles__oxartnum->value);
                $oOrderArticle->oxorderarticles__oxamount = new oxField($dAmount);
                $oOrderArticle->oxorderarticles__oxselvariant = new oxField(oxRegistry::getConfig()->getRequestParameter('sel'));
                $oOrder->recalculateOrder(array($oOrderArticle));
            }
        }
    }

    /**
     * Removes article from order list.
     */
    public function deleteThisArticle()
    {
        // get article id
        $sOrderArtId = oxRegistry::getConfig()->getRequestParameter('sArtID');
        $sOrderId = $this->getEditObjectId();

        $oOrderArticle = oxNew('oxorderarticle');
        $oOrder = oxNew('oxorder');

        // order and order article exits?
        if ($oOrderArticle->load($sOrderArtId) && $oOrder->load($sOrderId)) {
            $myConfig = $this->getConfig();

            // deleting record
            $oOrderArticle->delete();

            // recalculating order
            $oOrder->recalculateOrder();
        }
    }

    /**
     * Cancels order item
     */
    public function storno()
    {
        $myConfig = $this->getConfig();

        $sOrderArtId = oxRegistry::getConfig()->getRequestParameter('sArtID');
        $oArticle = oxNew('oxorderarticle');
        $oArticle->load($sOrderArtId);

        if ($oArticle->oxorderarticles__oxstorno->value == 1) {
            $oArticle->oxorderarticles__oxstorno->setValue(0);
            $sStockSign = -1;
        } else {
            $oArticle->oxorderarticles__oxstorno->setValue(1);
            $sStockSign = 1;
        }

        // stock information
        if ($myConfig->getConfigParam('blUseStock')) {
            $oArticle->updateArticleStock($oArticle->oxorderarticles__oxamount->value * $sStockSign, $myConfig->getConfigParam('blAllowNegativeStock'));
        }

        $oDb = oxDb::getDb();
        $sQ = "update oxorderarticles set oxstorno = " . $oDb->quote($oArticle->oxorderarticles__oxstorno->value) . " where oxid = " . $oDb->quote($sOrderArtId);
        $oDb->execute($sQ);

        //get article id
        $sQ = "select oxartid from oxorderarticles where oxid = " . $oDb->quote($sOrderArtId);
        //must read from master, see ESDEV-3804 for details
        if (($sArtId = oxDb::getDb()->getOne($sQ, false, false))) {
            $oOrder = oxNew('oxorder');
            if ($oOrder->load($this->getEditObjectId())) {
                $oOrder->recalculateOrder();
            }
        }
    }

    /**
     * Updates order articles stock and recalculates order
     */
    public function updateOrder()
    {
        $aOrderArticles = oxRegistry::getConfig()->getRequestParameter('aOrderArticles');

        $oOrder = oxNew('oxorder');
        if (is_array($aOrderArticles) && $oOrder->load($this->getEditObjectId())) {

            $myConfig = $this->getConfig();
            $oOrderArticles = $oOrder->getOrderArticles(true);

            $blUseStock = $myConfig->getConfigParam('blUseStock');
            foreach ($oOrderArticles as $oOrderArticle) {
                $sItemId = $oOrderArticle->getId();
                if (isset($aOrderArticles[$sItemId])) {

                    // update stock
                    if ($blUseStock) {
                        $oOrderArticle->setNewAmount($aOrderArticles[$sItemId]['oxamount']);
                    } else {
                        $oOrderArticle->assign($aOrderArticles[$sItemId]);
                        $oOrderArticle->save();
                    }
                }
            }

            // recalculating order
            $oOrder->recalculateOrder();
        }
    }
}
