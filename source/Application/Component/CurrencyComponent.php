<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use oxRegistry;

/**
 * Currency manager class.
 *
 * @subpackage oxcmp
 */
class CurrencyComponent extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    /**
     * Array of available currencies.
     *
     * @var array
     */
    public $aCurrencies = null;

    /**
     * Active currency object.
     *
     * @var object
     */
    protected $_oActCur = null;

    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Checks for currency parameter set in URL, session or post
     * variables. If such were found - loads all currencies possible
     * in shop, searches if passed is available (if no - default
     * currency is set the first defined in admin). Then sets currency
     * parameter so session ($myConfig->setActShopCurrency($iCur)),
     * loads basket and forces ir to recalculate (oBasket->blCalcNeeded
     * = true). Finally executes parent::init().
     *
     * @return null
     */
    public function init()
    {
        // Performance
        $myConfig = $this->getConfig();
        if (!$myConfig->getConfigParam('bl_perfLoadCurrency')) {
            //#861C -  show first currency
            $aCurrencies = $myConfig->getCurrencyArray();
            $this->_oActCur = current($aCurrencies);

            return;
        }

        $iCur = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('cur');
        if (isset($iCur)) {
            $aCurrencies = $myConfig->getCurrencyArray();
            if (!isset($aCurrencies[$iCur])) {
                $iCur = 0;
            }

            // set new currency
            $myConfig->setActShopCurrency($iCur);

            // recalc basket
            $oBasket = $this->getSession()->getBasket();
            $oBasket->onUpdate();
        }

        $iActCur = $myConfig->getShopCurrency();
        $this->aCurrencies = $myConfig->getCurrencyArray($iActCur);

        $this->_oActCur = $this->aCurrencies[$iActCur];

        //setting basket currency (M:825)
        if (!isset($oBasket)) {
            $oBasket = $this->getSession()->getBasket();
        }
        $oBasket->setBasketCurrency($this->_oActCur);
        parent::init();
    }

    /**
     * Executes parent::render(), passes currency object to template
     * engine and returns currencies array.
     *
     * Template variables:
     * <b>currency</b>
     *
     * @return array
     */
    public function render()
    {
        parent::render();
        $oParentView = $this->getParent();
        $oParentView->setActCurrency($this->_oActCur);

        $oUrlUtils = \OxidEsales\Eshop\Core\Registry::getUtilsUrl();
        $sUrl = $oUrlUtils->cleanUrl($this->getConfig()->getTopActiveView()->getLink(), ["cur"]);

        if ($this->getConfig()->getConfigParam('bl_perfLoadCurrency')) {
            reset($this->aCurrencies);
            foreach ($this->aCurrencies as $oItem) {
                $oItem->link = $oUrlUtils->processUrl($sUrl, true, ["cur" => $oItem->id]);
            }
        }

        return $this->aCurrencies;
    }
}
