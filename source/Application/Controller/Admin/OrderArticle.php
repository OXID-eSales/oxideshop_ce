<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\TableViewNameGenerator;

/**
 * Admin order article manager.
 * Collects order articles information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Articles.
 */
class OrderArticle extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Product which was currently found by search
     *
     * @var Article
     */
    protected $_oSearchProduct = null;

    /**
     * Product list:
     *  - if product is not variant - list contains only product which was found by search;
     *  - if product is variant - list consist with variant paret and its variants
     *
     * @var \OxidEsales\Eshop\Core\Model\ListModel
     */
    protected $_oSearchProductList = null;

    /**
     * Product found by search. If product is variant - it keeps parent object
     *
     * @var Article
     */
    protected $_oMainSearchProduct = null;

    /**
     * Active order object
     *
     * @var \OxidEsales\Eshop\Application\Model\Order
     */
    protected $_oEditObject = null;

    /** @inheritdoc */
    public function render()
    {
        parent::render();

        if ($oOrder = $this->getEditObject()) {
            $this->_aViewData["edit"] = $oOrder;
            $this->_aViewData["aProductVats"] = $oOrder->getProductVats(true);
        }

        return "order_article";
    }

    /**
     * Returns editable order object
     *
     * @return \OxidEsales\Eshop\Application\Model\Order
     */
    public function getEditObject()
    {
        $soxId = $this->getEditObjectId();
        if ($this->_oEditObject === null && isset($soxId) && $soxId != "-1") {
            $this->_oEditObject = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
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
        return Registry::getRequest()->getRequestEscapedParameter('sSearchArtNum');
    }

    /**
     * If possible returns searched/found oxarticle object
     *
     * @return Article|false
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
        if ($this->_oMainSearchProduct === null && ($articleNumber = $this->getSearchProductArtNr())) {
            $this->_oMainSearchProduct = false;

            $db = DatabaseProvider::getDb(DatabaseProvider::FETCH_MODE_ASSOC);
            $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
            $tableView = $tableViewNameGenerator->getViewName("oxarticles");
            $query = "select oxid, oxparentid from $tableView where oxartnum = :oxartnum limit 1";

            $result = $db->select($query, [
                ':oxartnum' => $articleNumber
            ]);
            if ($result != false && $result->count() > 0) {
                $result->fields = array_change_key_case($result->fields, CASE_LOWER);
                $articleId = $result->fields['oxparentid'] ?: $result->fields['oxid'];

                $product = oxNew(Article::class);
                if ($product->load($articleId)) {
                    $this->_oMainSearchProduct = $product;
                }
            }
        }

        return $this->_oMainSearchProduct;
    }

    /**
     * Returns product list containing searchable product or its parent and its variants
     *
     * @return \OxidEsales\Eshop\Core\Model\ListModel
     */
    public function getProductList()
    {
        if ($this->_oSearchProductList === null) {
            $this->_oSearchProductList = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);

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
        $sOxid = Registry::getRequest()->getRequestEscapedParameter('aid');
        $dAmount = Registry::getRequest()->getRequestEscapedParameter('am');
        $oProduct = oxNew(Article::class);

        if ($sOxid && $dAmount && $oProduct->load($sOxid)) {
            $sOrderId = $this->getEditObjectId();
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            if ($sOrderId && $oOrder->load($sOrderId)) {
                $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
                $oOrderArticle->oxorderarticles__oxartid = new \OxidEsales\Eshop\Core\Field($oProduct->getId());
                $oOrderArticle->oxorderarticles__oxartnum = new \OxidEsales\Eshop\Core\Field($oProduct->oxarticles__oxartnum->value);
                $oOrderArticle->oxorderarticles__oxamount = new \OxidEsales\Eshop\Core\Field($dAmount);
                $oOrderArticle->oxorderarticles__oxselvariant = new \OxidEsales\Eshop\Core\Field(Registry::getRequest()->getRequestEscapedParameter('sel'));
                $oOrder->recalculateOrder([$oOrderArticle]);
            }
        }
    }

    /**
     * Removes article from order list.
     */
    public function deleteThisArticle()
    {
        // get article id
        $sOrderArtId = Registry::getRequest()->getRequestEscapedParameter('sArtID');
        $sOrderId = $this->getEditObjectId();

        $oOrderArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);

        // order and order article exits?
        if ($oOrderArticle->load($sOrderArtId) && $oOrder->load($sOrderId)) {
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
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

        $sOrderArtId = Registry::getRequest()->getRequestEscapedParameter('sArtID');
        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\OrderArticle::class);
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

        $oDb = DatabaseProvider::getDb();
        $sQ = "update oxorderarticles set oxstorno = :oxstorno where oxid = :oxid";
        $oDb->execute($sQ, [':oxstorno' => $oArticle->oxorderarticles__oxstorno->value, ':oxid' => $sOrderArtId]);

        //get article id
        $sQ = "select oxartid from oxorderarticles where oxid = :oxid";
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        if (($sArtId = DatabaseProvider::getMaster()->getOne($sQ, [':oxid' => $sOrderArtId]))) {
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
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
        $aOrderArticles = Registry::getRequest()->getRequestEscapedParameter('aOrderArticles');

        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if (is_array($aOrderArticles) && $oOrder->load($this->getEditObjectId())) {
            $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
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
