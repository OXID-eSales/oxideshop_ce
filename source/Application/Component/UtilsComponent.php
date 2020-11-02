<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

use OxidEsales\Eshop\Core\Registry;

/**
 * Transparent shop utilities class.
 * Some specific utilities, such as fetching article info, etc. (Class may be used
 * for overriding).
 */
class UtilsComponent extends \OxidEsales\Eshop\Core\Controller\BaseController
{
    /**
     * Marking object as component.
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Adds/removes chosen article to/from article comparison list.
     *
     * @param object $sProductId product id
     * @param float  $dAmount    amount
     * @param array  $aSel       (default null)
     * @param bool   $blOverride allow override
     * @param bool   $blBundle   bundled
     */
    public function toCompareList(
        $sProductId = null,
        $dAmount = null,
        $aSel = null,
        $blOverride = false,
        $blBundle = false
    ): void {
        // only if enabled and not search engine..
        if ($this->getViewConfig()->getShowCompareList() && !Registry::getUtils()->isSearchEngine()) {
            // #657 special treatment if we want to put on comparelist
            $blAddCompare = Registry::getConfig()->getRequestParameter('addcompare');
            $blRemoveCompare = Registry::getConfig()->getRequestParameter('removecompare');
            $sProductId = $sProductId ?: Registry::getConfig()->getRequestParameter('aid');
            if (($blAddCompare || $blRemoveCompare) && $sProductId) {
                // toggle state in session array
                $aItems = Registry::getSession()->getVariable('aFiltcompproducts');
                if ($blAddCompare && !isset($aItems[$sProductId])) {
                    $aItems[$sProductId] = true;
                }

                if ($blRemoveCompare) {
                    unset($aItems[$sProductId]);
                }

                Registry::getSession()->setVariable('aFiltcompproducts', $aItems);
                $oParentView = $this->getParent();

                // #843C there was problem then field "blIsOnComparisonList" was not set to article object
                if (($oProduct = $oParentView->getViewProduct())) {
                    if (isset($aItems[$oProduct->getId()])) {
                        $oProduct->setOnComparisonList(true);
                    } else {
                        $oProduct->setOnComparisonList(false);
                    }
                }

                $aViewProds = $oParentView->getViewProductList();
                if (\is_array($aViewProds) && \count($aViewProds)) {
                    foreach ($aViewProds as $oProduct) {
                        if (isset($aItems[$oProduct->getId()])) {
                            $oProduct->setOnComparisonList(true);
                        } else {
                            $oProduct->setOnComparisonList(false);
                        }
                    }
                }
            }
        }
    }

    /**
     * If session user is set loads user noticelist (\OxidEsales\Eshop\Application\Model\User::GetBasket())
     * and adds article to it.
     *
     * @param string $sProductId Product/article ID (default null)
     * @param float  $dAmount    amount of good (default null)
     * @param array  $aSel       product selection list (default null)
     *
     * @return bool
     */
    public function toNoticeList($sProductId = null, $dAmount = null, $aSel = null)
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        $this->_toList('noticelist', $sProductId, $dAmount, $aSel);
    }

    /**
     * If session user is set loads user wishlist (\OxidEsales\Eshop\Application\Model\User::GetBasket()) and
     * adds article to it.
     *
     * @param string $sProductId Product/article ID (default null)
     * @param float  $dAmount    amount of good (default null)
     * @param array  $aSel       product selection list (default null)
     *
     * @return false
     */
    public function toWishList($sProductId = null, $dAmount = null, $aSel = null)
    {
        if (!Registry::getSession()->checkSessionChallenge()) {
            return;
        }

        // only if enabled
        if ($this->getViewConfig()->getShowWishlist()) {
            $this->_toList('wishlist', $sProductId, $dAmount, $aSel);
        }
    }

    /**
     * Adds chosen product to defined user list. if amount is 0, item is removed from the list.
     *
     * @param string $sListType  user product list type
     * @param string $sProductId product id
     * @param float  $dAmount    product amount
     * @param array  $aSel       product selection list
     *
     * @deprecated underscore prefix violates PSR12, will be renamed to "toList" in next major
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _toList($sListType, $sProductId, $dAmount, $aSel): void
    {
        // only if user is logged in
        if ($oUser = $this->getUser()) {
            $sProductId = $sProductId ?: Registry::getConfig()->getRequestParameter('itmid');
            $sProductId = $sProductId ?: Registry::getConfig()->getRequestParameter('aid');
            $dAmount = $dAmount ?? Registry::getConfig()->getRequestParameter('am');
            $aSel = $aSel ?: Registry::getConfig()->getRequestParameter('sel');

            // processing amounts
            $dAmount = str_replace(',', '.', $dAmount);
            if (!\OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('blAllowUnevenAmounts')) {
                $dAmount = round((string)$dAmount);
            }

            $oBasket = $oUser->getBasket($sListType);
            $oBasket->addItemToBasket($sProductId, abs($dAmount), $aSel, (0 === $dAmount));

            // recalculate basket count
            $oBasket->getItemCount(true);
        }
    }

    /**
     *  Set view data, call parent::render.
     */
    public function render(): void
    {
        parent::render();

        $oParentView = $this->getParent();

        // add content for main menu
        $oContentList = oxNew(\OxidEsales\Eshop\Application\Model\ContentList::class);
        $oContentList->loadMainMenulist();
        $oParentView->setMenueList($oContentList);

        return;
    }
}
