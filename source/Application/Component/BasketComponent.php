<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use OxidEsales\Eshop\Core\Exception\ArticleInputException;
use OxidEsales\Eshop\Core\Exception\NoArticleException;
use OxidEsales\Eshop\Core\Exception\OutOfStockException;
use OxidEsales\Eshop\Core\Registry;
use oxRegistry;
use Psr\Log\LoggerInterface;
use stdClass;
use oxOutOfStockException;
use oxArticleInputException;
use oxNoArticleException;

/**
 * Main shopping basket manager. Arranges shopping basket
 * contents, updates amounts, prices, taxes etc.
 *
 * @subpackage oxcmp
 */
class BasketComponent extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Last call function name
     *
     * @var string
     */
    protected $_sLastCallFnc = null;

    /**
     * Parameters which are kept when redirecting after user
     * puts something to basket
     *
     * @var array
     */
    public $aRedirectParams = ['cnid', // category id
                                    'mnid', // manufacturer id
                                    'anid', // active article id
                                    'tpl', // spec. template
                                    'listtype', // list type
                                    'searchcnid', // search category
                                    'searchvendor', // search vendor
                                    'searchmanufacturer', // search manufacturer
                                    // @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
                                    'searchrecomm', // search recomendation
                                    'recommid' // recomm. list id
                                    // END deprecated
    ];

    /**
     * Initiates component.
     */
    public function init()
    {
        $oConfig = $this->getConfig();
        if ($oConfig->getConfigParam('blPsBasketReservationEnabled')) {
            if ($oReservations = $this->getSession()->getBasketReservations()) {
                if (!$oReservations->getTimeLeft()) {
                    $oBasket = $this->getSession()->getBasket();
                    if ($oBasket && $oBasket->getProductsCount()) {
                        $this->emptyBasket($oBasket);
                    }
                }
                $iLimit = (int) $oConfig->getConfigParam('iBasketReservationCleanPerRequest');
                if (!$iLimit) {
                    $iLimit = 200;
                }
                $oReservations->discardUnusedReservations($iLimit);
            }
        }

        parent::init();

        // Basket exclude
        if ($this->getConfig()->getConfigParam('blBasketExcludeEnabled')) {
            if ($oBasket = $this->getSession()->getBasket()) {
                $this->getParent()->setRootCatChanged($this->isRootCatChanged() && $oBasket->getContents());
            }
        }
    }

    /**
     * Loads basket ($oBasket = $mySession->getBasket()), calls oBasket->calculateBasket,
     * executes parent::render() and returns basket object.
     *
     * @return object   $oBasket    basket object
     */
    public function render()
    {
        // recalculating
        if ($oBasket = $this->getSession()->getBasket()) {
            $oBasket->calculateBasket(false);
        }

        parent::render();

        return $oBasket;
    }

    /**
     * Basket content update controller.
     * Before adding article - check if client is not a search engine. If
     * yes - exits method by returning false. If no - executes
     * oxcmp_basket::_addItems() and puts article to basket.
     * Returns position where to redirect user browser.
     *
     * @param string $sProductId Product ID (default null)
     * @param double $dAmount    Product amount (default null)
     * @param array  $aSel       (default null)
     * @param array  $aPersParam (default null)
     * @param bool   $blOverride If true amount in basket is replaced by $dAmount otherwise amount is increased by
     *                           $dAmount (default false)
     *
     * @return mixed
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseConnectionException
     * @throws \OxidEsales\Eshop\Core\Exception\DatabaseErrorException
     */
    public function toBasket($sProductId = null, $dAmount = null, $aSel = null, $aPersParam = null, $blOverride = false)
    {
        if (Registry::getSession()->getId() &&
            Registry::getSession()->isActualSidInCookie() &&
            !Registry::getSession()->checkSessionChallenge()
        ) {
            $this->getContainer()->get(LoggerInterface::class)->warning('EXCEPTION_NON_MATCHING_CSRF_TOKEN');
            Registry::getUtilsView()->addErrorToDisplay('ERROR_MESSAGE_NON_MATCHING_CSRF_TOKEN');
            return;
        }

        // adding to basket is not allowed ?
        $myConfig = $this->getConfig();
        if (Registry::getUtils()->isSearchEngine()) {
            return;
        }

        // adding articles
        if ($aProducts = $this->_getItems($sProductId, $dAmount, $aSel, $aPersParam, $blOverride)) {
            $this->_setLastCallFnc('tobasket');

            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $database->startTransaction();
            try {
                $oBasketItem = $this->_addItems($aProducts);
                //reserve active basket
                if (Registry::getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
                    $basket = Registry::getSession()->getBasket();
                    Registry::getSession()->getBasketReservations()->reserveBasket($basket);
                }
            } catch (\Exception $exception) {
                $database->rollbackTransaction();
                unset($oBasketItem);
                throw $exception;
            }
            $database->commitTransaction();

            // new basket item marker
            if ($oBasketItem && $myConfig->getConfigParam('iNewBasketItemMessage') != 0) {
                $oNewItem = new stdClass();
                $oNewItem->sTitle = $oBasketItem->getTitle();
                $oNewItem->sId = $oBasketItem->getProductId();
                $oNewItem->dAmount = $oBasketItem->getAmount();
                $oNewItem->dBundledAmount = $oBasketItem->getdBundledAmount();

                // passing article
                Registry::getSession()->setVariable('_newitem', $oNewItem);
            }

            // redirect to basket
            $redirectUrl = $this->_getRedirectUrl();
            $this->dispatchEvent(new \OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BasketChangedEvent($this));

            return $redirectUrl;
        }
    }

    /**
     * Similar to tobasket, except that as product id "bindex" parameter is (can be) taken
     *
     * @param string $sProductId Product ID (default null)
     * @param double $dAmount    Product amount (default null)
     * @param array  $aSel       (default null)
     * @param array  $aPersParam (default null)
     * @param bool   $blOverride If true means increase amount of chosen article (default false)
     *
     * @return mixed
     */
    public function changeBasket(
        $sProductId = null,
        $dAmount = null,
        $aSel = null,
        $aPersParam = null,
        $blOverride = true
    ) {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        // adding to basket is not allowed ?
        if (Registry::getUtils()->isSearchEngine()) {
            return;
        }

        // fetching item ID
        if (!$sProductId) {
            $sBasketItemId = Registry::getConfig()->getRequestParameter('bindex');

            if ($sBasketItemId) {
                $oBasket = $this->getSession()->getBasket();
                //take params
                $aBasketContents = $oBasket->getContents();
                $oItem = $aBasketContents[$sBasketItemId];

                $sProductId = isset($oItem) ? $oItem->getProductId() : null;
            } else {
                $sProductId = Registry::getConfig()->getRequestParameter('aid');
            }
        }

        // fetching other needed info
        $dAmount = isset($dAmount) ? $dAmount : Registry::getConfig()->getRequestParameter('am');
        $aSel = isset($aSel) ? $aSel : Registry::getConfig()->getRequestParameter('sel');
        $aPersParam = $aPersParam ? $aPersParam : Registry::getConfig()->getRequestParameter('persparam');

        // adding articles
        if ($aProducts = $this->_getItems($sProductId, $dAmount, $aSel, $aPersParam, $blOverride)) {
            // information that last call was changebasket
            $oBasket = $this->getSession()->getBasket();
            $oBasket->onUpdate();
            $this->_setLastCallFnc('changebasket');

            $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
            $database->startTransaction();
            try {
                $oBasketItem = $this->_addItems($aProducts);
                // reserve active basket
                if (Registry::getConfig()->getConfigParam('blPsBasketReservationEnabled')) {
                    Registry::getSession()->getBasketReservations()->reserveBasket($oBasket);
                }
            } catch (\Exception $exception) {
                $database->rollbackTransaction();
                unset($oBasketItem);
                throw $exception;
            }
            $database->commitTransaction();
        }
    }

    /**
     * Formats and returns redirect URL where shop must be redirected after
     * storing something to basket
     *
     * @return string   $sClass.$sPosition  redirection URL
     */
    protected function _getRedirectUrl()
    {

        // active controller id
        $controllerId = Registry::getConfig()->getRequestControllerId();
        $controllerId = $controllerId ? $controllerId . '?' : 'start?';
        $sPosition = '';

        // setting redirect parameters
        foreach ($this->aRedirectParams as $sParamName) {
            $sParamVal = Registry::getConfig()->getRequestParameter($sParamName);
            $sPosition .= $sParamVal ? $sParamName . '=' . $sParamVal . '&' : '';
        }

        // special treatment
        // search param
        $sParam = rawurlencode(Registry::getConfig()->getRequestParameter('searchparam', true));
        $sPosition .= $sParam ? 'searchparam=' . $sParam . '&' : '';

        // current page number
        $iPageNr = (int) Registry::getConfig()->getRequestParameter('pgNr');
        $sPosition .= ($iPageNr > 0) ? 'pgNr=' . $iPageNr . '&' : '';

        // reload and backbutton blocker
        if ($this->getConfig()->getConfigParam('iNewBasketItemMessage') == 3) {
            // saving return to shop link to session
            Registry::getSession()->setVariable('_backtoshop', $controllerId . $sPosition);

            // redirecting to basket
            $controllerId = 'basket?';
        }

        return $controllerId . $sPosition;
    }

    /**
     * Cleans and returns persisted parameters.
     *
     * @param array $persistedParameters key-value parameters (optional). If not passed - takes parameters from request.
     *
     * @return array|null cleaned up parameters or null, if there are no non-empty parameters
     */
    protected function getPersistedParameters($persistedParameters = null)
    {
        $persistedParameters = ($persistedParameters ?: Registry::getConfig()->getRequestParameter('persparam'));
        if (!is_array($persistedParameters)) {
            return null;
        }
        return array_filter($persistedParameters) ?: null;
    }

    /**
     * Collects and returns array of items to add to basket. Product info is taken not only from
     * given parameters, but additionally from request 'aproducts' parameter
     *
     * @param string $sProductId product ID
     * @param double $dAmount    product amount
     * @param array  $aSel       product select lists
     * @param array  $aPersParam product persistent parameters
     * @param bool   $blOverride amount override status
     *
     * @return mixed
     */
    protected function _getItems(
        $sProductId = null,
        $dAmount = null,
        $aSel = null,
        $aPersParam = null,
        $blOverride = false
    ) {
        // collecting items to add
        $aProducts = Registry::getConfig()->getRequestParameter('aproducts');

        // collecting specified item
        $sProductId = $sProductId ? $sProductId : Registry::getConfig()->getRequestParameter('aid');
        if ($sProductId) {
            // additionally fetching current product info
            $dAmount = isset($dAmount) ? $dAmount : Registry::getConfig()->getRequestParameter('am');

            // select lists
            $aSel = isset($aSel) ? $aSel : Registry::getConfig()->getRequestParameter('sel');

            // persistent parameters
            if (empty($aPersParam)) {
                $aPersParam = $this->getPersistedParameters();
            }

            $sBasketItemId = Registry::getConfig()->getRequestParameter('bindex');

            $aProducts[$sProductId] = ['am'           => $dAmount,
                                            'sel'          => $aSel,
                                            'persparam'    => $aPersParam,
                                            'override'     => $blOverride,
                                            'basketitemid' => $sBasketItemId
            ];
        }

        if (is_array($aProducts) && count($aProducts)) {
            if (Registry::getConfig()->getRequestParameter('removeBtn') !== null) {
                //setting amount to 0 if removing article from basket
                foreach ($aProducts as $sProductId => $aProduct) {
                    if (isset($aProduct['remove']) && $aProduct['remove']) {
                        $aProducts[$sProductId]['am'] = 0;
                    } else {
                        unset($aProducts[$sProductId]);
                    }
                }
            }

            return $aProducts;
        }

        return false;
    }

    /**
     * Adds all articles user wants to add to basket. Returns
     * last added to basket item.
     *
     * @param array $products products to add array
     *
     * @return  object  $oBasketItem    last added basket item
     */
    protected function _addItems($products)
    {
        $activeView = $this->getConfig()->getActiveView();
        $errorDestination = $activeView->getErrorDestination();

        $basket = $this->getSession()->getBasket();
        $basketInfo = $basket->getBasketSummary();

        $basketItemAmounts = [];

        foreach ($products as $addProductId => $productInfo) {
            $data = $this->prepareProductInformation($addProductId, $productInfo);
            $productAmount = 0;
            if (isset($basketInfo->aArticles[$data['id']])) {
                $productAmount = $basketInfo->aArticles[$data['id']];
            }
            $products[$addProductId]['oldam'] = $productAmount;

            //If we already changed articles so they now exactly match existing ones,
            //we need to make sure we get the amounts correct
            if (isset($basketItemAmounts[$data['oldBasketItemId']])) {
                $data['amount'] = $data['amount'] + $basketItemAmounts[$data['oldBasketItemId']];
            }

            $basketItem = $this->addItemToBasket($basket, $data, $errorDestination);

            if (($basketItem instanceof \OxidEsales\Eshop\Application\Model\BasketItem)) {
                $basketItemKey = $basketItem->getBasketItemKey();
                if ($basketItemKey) {
                    if (! isset($basketItemAmounts[$basketItemKey])) {
                        $basketItemAmounts[$basketItemKey] = 0;
                    }
                    $basketItemAmounts[$basketItemKey] += $data['amount'];
                }
            }

            if (!$basketItem) {
                $info = $basket->getBasketSummary();
                $productAmount = $info->aArticles[$data['id']];
                $products[$addProductId]['am'] = isset($productAmount) ? $productAmount : 0;
            }
        }

        //if basket empty remove possible gift card
        if ($basket->getProductsCount() == 0) {
            $basket->setCardId(null);
        }

        // information that last call was tobasket
        $this->_setLastCall($this->_getLastCallFnc(), $products, $basketInfo);

        return $basketItem;
    }

    /**
     * Setting last call data to session (data used by econda)
     *
     * @param string $sCallName    name of action ('tobasket', 'changebasket')
     * @param array  $aProductInfo data which comes from request when you press button "to basket"
     * @param array  $aBasketInfo  array returned by \OxidEsales\Eshop\Application\Model\Basket::getBasketSummary()
     */
    protected function _setLastCall($sCallName, $aProductInfo, $aBasketInfo)
    {
        Registry::getSession()->setVariable('aLastcall', [$sCallName => $aProductInfo]);
    }

    /**
     * Setting last call function name (data used by econda)
     *
     * @param string $sCallName name of action ('tobasket', 'changebasket')
     */
    protected function _setLastCallFnc($sCallName)
    {
        $this->_sLastCallFnc = $sCallName;
    }

    /**
     * Getting last call function name (data used by econda)
     *
     * @return string
     */
    protected function _getLastCallFnc()
    {
        return $this->_sLastCallFnc;
    }

    /**
     * Returns true if active root category was changed
     *
     * @return bool
     */
    public function isRootCatChanged()
    {
        // in Basket
        $oBasket = $this->getSession()->getBasket();
        if ($oBasket->showCatChangeWarning()) {
            $oBasket->setCatChangeWarningState(false);

            return true;
        }

        // in Category, only then category is empty ant not equal to default category
        $sDefCat = Registry::getConfig()->getActiveShop()->oxshops__oxdefcat->value;
        $sActCat = Registry::getConfig()->getRequestParameter('cnid');
        $oActCat = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);
        if ($sActCat && $sActCat != $sDefCat && $oActCat->load($sActCat)) {
            $sActRoot = $oActCat->oxcategories__oxrootid->value;
            if ($oBasket->getBasketRootCatId() && $sActRoot != $oBasket->getBasketRootCatId()) {
                return true;
            }
        }

        return false;
    }

    /**
     * Executes user choice:
     *
     * - if user clicked on "Proceed to checkout" - redirects to basket,
     * - if clicked "Continue shopping" - clear basket
     *
     * @return mixed
     */
    public function executeUserChoice()
    {
        $this->dispatchEvent(new \OxidEsales\EshopCommunity\Internal\Transition\ShopEvents\BasketChangedEvent($this));

        // redirect to basket
        if (Registry::getConfig()->getRequestParameter("tobasket")) {
            return "basket";
        } else {
            // clear basket
            $this->getSession()->getBasket()->deleteBasket();
            $this->getParent()->setRootCatChanged(false);
        }
    }

    /**
     * Deletes user basket object from session and saved one from DB if needed.
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket
     */
    protected function emptyBasket($oBasket)
    {
        $oBasket->deleteBasket();
    }

    /**
     * Prepare information for adding product to basket.
     *
     * @param string $addProductId
     * @param array  $productInfo
     *
     * @return array
     */
    protected function prepareProductInformation($addProductId, $productInfo)
    {
        $return = [];

        $return['id'] = isset($productInfo['aid']) ? $productInfo['aid'] : $addProductId;
        $return['amount'] = isset($productInfo['am']) ? $productInfo['am'] : 0;
        $return['selectList'] = isset($productInfo['sel']) ? $productInfo['sel'] : null;

        $return['persistentParameters'] = $this->getPersistedParameters($productInfo['persparam']);
        $return['override'] = isset($productInfo['override']) ? $productInfo['override'] : null;
        $return['bundle'] = isset($productInfo['bundle']) ? true : false;
        $return['oldBasketItemId'] = isset($productInfo['basketitemid']) ? $productInfo['basketitemid'] : null;

        return $return;
    }

    /**
     * Add one item to basket. Handle eventual errors.
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $basket
     * @param array                                      $itemData
     * @param string                                     $errorDestination
     *
     * @return null|oxBasketItem
     */
    protected function addItemToBasket($basket, $itemData, $errorDestination)
    {
        $basketItem = null;

        try {
            $basketItem = $basket->addToBasket(
                $itemData['id'],
                $itemData['amount'],
                $itemData['selectList'],
                $itemData['persistentParameters'],
                $itemData['override'],
                $itemData['bundle'],
                $itemData['oldBasketItemId']
            );
        } catch (OutOfStockException $exception) {
            $exception->setDestination($errorDestination);
            // #950 Change error destination to basket popup
            if (!$errorDestination && $this->getConfig()->getConfigParam('iNewBasketItemMessage') == 2) {
                $errorDestination = 'popup';
            }
            Registry::getUtilsView()->addErrorToDisplay($exception, false, (bool) $errorDestination, $errorDestination);
        } catch (ArticleInputException $exception) {
            //add to display at specific position
            $exception->setDestination($errorDestination);
            Registry::getUtilsView()->addErrorToDisplay($exception, false, (bool) $errorDestination, $errorDestination);
        } catch (NoArticleException $exception) {
            //ignored, best solution F ?
        }

        return $basketItem;
    }
}
