<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

use Exception;
use OxidEsales\Eshop\Core\DatabaseProvider;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\UtilsObject;
use oxRegistry;
use oxField;
use oxDb;
use oxuserbasket;

/**
 * Basket reservations handler class
 *
 */
class BasketReservation extends \OxidEsales\Eshop\Core\Base
{
    /**
     * Reservations list
     *
     * @var \OxidEsales\EshopCommunity\Application\Model\UserBasket
     */
    protected $_oReservations = null;

    /**
     * Currently reserved products array
     *
     * @var array
     */
    protected $_aCurrentlyReserved = null;

    /**
     * return the ID of active resevations user basket
     *
     * @return string
     * @deprecated underscore prefix violates PSR12, will be renamed to "getReservationsId" in next major
     */
    protected function _getReservationsId() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $sId = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('basketReservationToken');
        if (!$sId) {
            $utilsObject = $this->getUtilsObjectInstance();
            $sId = $utilsObject->generateUId();
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable('basketReservationToken', $sId);
        }

        return $sId;
    }

    /**
     * load reservation or create new reservation user basket
     *
     * @param string $sBasketId basket id for this user basket
     *
     * @return \OxidEsales\EshopCommunity\Application\Model\UserBasket
     * @deprecated underscore prefix violates PSR12, will be renamed to "loadReservations" in next major
     */
    protected function _loadReservations($sBasketId) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $oReservations = oxNew(\OxidEsales\Eshop\Application\Model\UserBasket::class);
        $aWhere = ['oxuserbaskets.oxuserid' => $sBasketId, 'oxuserbaskets.oxtitle' => 'reservations'];

        // creating if it does not exist
        if (!$oReservations->assignRecord($oReservations->buildSelectString($aWhere))) {
            $oReservations->oxuserbaskets__oxtitle = new \OxidEsales\Eshop\Core\Field('reservations');
            $oReservations->oxuserbaskets__oxuserid = new \OxidEsales\Eshop\Core\Field($sBasketId);
            // marking basket as new (it will not be saved in DB yet)
            $oReservations->setIsNewBasket();
        }

        return $oReservations;
    }

    /**
     * get reservations collection
     *
     * @return \OxidEsales\EshopCommunity\Application\Model\UserBasket
     */
    public function getReservations()
    {
        if ($this->_oReservations) {
            return $this->_oReservations;
        }

        if (!$sBasketId = $this->_getReservationsId()) {
            return null;
        }

        $this->_oReservations = $this->_loadReservations($sBasketId);

        return $this->_oReservations;
    }

    /**
     * return currently reserved items in an array format array (artId => amount)
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "getReservedItems" in next major
     */
    protected function _getReservedItems() // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        if (isset($this->_aCurrentlyReserved)) {
            return $this->_aCurrentlyReserved;
        }

        $oReserved = $this->getReservations();
        if (!$oReserved) {
            return [];
        }

        $this->_aCurrentlyReserved = [];
        foreach ($oReserved->getItems(false, false) as $oItem) {
            if (!isset($this->_aCurrentlyReserved[$oItem->oxuserbasketitems__oxartid->value])) {
                $this->_aCurrentlyReserved[$oItem->oxuserbasketitems__oxartid->value] = 0;
            }
            $this->_aCurrentlyReserved[$oItem->oxuserbasketitems__oxartid->value] += $oItem->oxuserbasketitems__oxamount->value;
        }

        return $this->_aCurrentlyReserved;
    }

    /**
     * return currently reserved amount for an article
     *
     * @param string $sArticleId article id
     *
     * @return double
     */
    public function getReservedAmount($sArticleId)
    {
        $aCurrentlyReserved = $this->_getReservedItems();
        if (isset($aCurrentlyReserved[$sArticleId])) {
            return $aCurrentlyReserved[$sArticleId];
        }

        return 0;
    }

    /**
     * compute difference of reserved amounts vs basket items
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket basket object
     *
     * @return array
     * @deprecated underscore prefix violates PSR12, will be renamed to "basketDifference" in next major
     */
    protected function _basketDifference(\OxidEsales\Eshop\Application\Model\Basket $oBasket) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $aDiff = $this->_getReservedItems();
        // refreshing history
        foreach ($oBasket->getContents() as $oItem) {
            $sProdId = $oItem->getProductId();
            if (!isset($aDiff[$sProdId])) {
                $aDiff[$sProdId] = -$oItem->getAmount();
            } else {
                $aDiff[$sProdId] -= $oItem->getAmount();
            }
        }

        return $aDiff;
    }

    /**
     * reserve articles given the basket difference array
     *
     * @param array $aBasketDiff basket difference array
     *
     * @see oxBasketReservation::_basketDifference
     * @deprecated underscore prefix violates PSR12, will be renamed to "reserveArticles" in next major
     */
    protected function _reserveArticles($aBasketDiff) // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    {
        $blAllowNegativeStock = $this->getConfig()->getConfigParam('blAllowNegativeStock');

        $oReserved = $this->getReservations();
        foreach ($aBasketDiff as $sId => $dAmount) {
            if ($dAmount != 0) {
                $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
                if ($oArticle->load($sId)) {
                    $oArticle->reduceStock(-$dAmount, $blAllowNegativeStock);
                    $oReserved->addItemToBasket($sId, -$dAmount);
                }
            }
        }
        $this->_aCurrentlyReserved = null;
    }

    /**
     * reserve given basket items, only when not in admin mode
     *
     * @param \OxidEsales\Eshop\Application\Model\Basket $oBasket basket object
     */
    public function reserveBasket(\OxidEsales\Eshop\Application\Model\Basket $oBasket)
    {
        if (!$this->isAdmin()) {
            $this->_reserveArticles($this->_basketDifference($oBasket));
        }
    }

    /**
     * commit reservation of given article amount
     * deletes this amount from active reservations userBasket,
     * update sold amount
     *
     * @param string $sArticleId article id
     * @param double $dAmount    amount to use
     */
    public function commitArticleReservation($sArticleId, $dAmount)
    {
        $dReserved = $this->getReservedAmount($sArticleId);

        if ($dReserved < $dAmount) {
            $dAmount = $dReserved;
        }

        $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $oArticle->load($sArticleId);

        $this->getReservations()->addItemToBasket($sArticleId, -$dAmount);
        $oArticle->beforeUpdate();
        $oArticle->updateSoldAmount($dAmount);
        $this->_aCurrentlyReserved = null;
    }

    /**
     * discard one article reservation
     * return the reserved stock to article
     *
     * @param string $sArticleId article id
     */
    public function discardArticleReservation($sArticleId)
    {
        $dReserved = $this->getReservedAmount($sArticleId);
        if ($dReserved) {
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            if ($oArticle->load($sArticleId)) {
                $oArticle->reduceStock(-$dReserved, true);
                $this->getReservations()->addItemToBasket($sArticleId, 0, null, true);
                $this->_aCurrentlyReserved = null;
            }
        }
    }

    /**
     * discard all reserved articles
     */
    public function discardReservations()
    {
        foreach (array_keys($this->_getReservedItems()) as $sArticleId) {
            $this->discardArticleReservation($sArticleId);
        }
        if ($this->_oReservations) {
            $this->_oReservations->delete();
            $this->_oReservations = null;
            $this->_aCurrentlyReserved = null;
        }
    }

    /**
     * periodic cleanup: discards timed out reservations even if they are not
     * for the current user
     *
     * @param int $iLimit limit for discarding (performance related)
     *
     * @throws Exception
     *
     * @return null
     */
    public function discardUnusedReservations($iLimit)
    {
        $database = DatabaseProvider::getMaster(DatabaseProvider::FETCH_MODE_ASSOC);

        $psBasketReservationTimeout = (int)$this->getConfig()->getConfigParam('iPsBasketReservationTimeout');
        $startTime = Registry::getUtilsDate()->getTime() - $psBasketReservationTimeout;

        $parameters = [
            ':oxtitle'  => 'reservations',
            ':oxupdate' => $startTime
        ];

        $reservation = $database->select("select oxid from oxuserbaskets 
            where oxtitle = :oxtitle and oxupdate <= :oxupdate limit $iLimit", $parameters);
        if ($reservation->EOF) {
            return;
        }

        $finished = [];
        while (!$reservation->EOF) {
            $finished[] = $database->quote($reservation->fields['oxid']);
            $reservation->fetchRow();
        }

        $database->startTransaction();
        try {
            $finished = implode(',', $finished);

            $reservation = $database->select(
                'select oxartid, oxamount from oxuserbasketitems where oxbasketid in (' . $finished . ')',
                false
            );

            while (!$reservation->EOF) {
                $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

                if ($article->load($reservation->fields['oxartid'])) {
                    $article->reduceStock(-$reservation->fields['oxamount'], true);
                }

                $reservation->fetchRow();
            }

            $shopId = Registry::getConfig()->getShopId();

            $database->execute('delete from oxuserbasketitems where oxbasketid in (' . $finished . ')');
            $database->execute(
                "delete from oxuserbasketitems where oxbasketid in (select oxid from oxuserbaskets where 
                        oxuserid in (select oxid from oxuser where oxshopid= :oxshopid))",
                [
                    ':oxshopid' => $shopId
                ]
            );

            $database->execute('delete from oxuserbaskets where oxid in (' . $finished . ')');
            $database->execute(
                "delete from oxuserbaskets where 
                        oxuserid in (select oxid from oxuser where oxshopid= :oxshopid) and 
                        oxuserbaskets.oxtitle = 'savedbasket' and oxuserbaskets.oxupdate <= :startTime",
                [
                    ':startTime' => $startTime,
                    ':oxshopid'  => $shopId
                ]
            );

            $database->commitTransaction();
        } catch (Exception $exception) {
            $database->rollbackTransaction();

            throw $exception;
        }

        $this->_aCurrentlyReserved = null;
    }

    /**
     * return time left (in seconds) for basket before expiration
     *
     * @return int
     */
    public function getTimeLeft()
    {
        $iTimeout = $this->getConfig()->getConfigParam('iPsBasketReservationTimeout');
        if ($iTimeout > 0) {
            $oRev = $this->getReservations();
            if ($oRev && $oRev->getId()) {
                $iTimeout -= (\OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - (int) $oRev->oxuserbaskets__oxupdate->value);
                \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iBasketReservationTimeout", $oRev->oxuserbaskets__oxupdate->value);
            } elseif (($iSessionTimeout = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iBasketReservationTimeout"))) {
                $iTimeout -= (\OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - (int) $iSessionTimeout);
            }

            return $iTimeout < 0 ? 0 : $iTimeout;
        }

        return 0;
    }

    /**
     * renews expiration timer to maximum value
     */
    public function renewExpiration()
    {
        if ($oReserved = $this->getReservations()) {
            $iTime = \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime();
            $oReserved->oxuserbaskets__oxupdate = new \OxidEsales\Eshop\Core\Field($iTime);
            $oReserved->save();

            \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("iBasketReservationTimeout");
        }
    }

    /**
     * @return \OxidEsales\Eshop\Core\UtilsObject
     */
    protected function getUtilsObjectInstance()
    {
        return Registry::getUtilsObject();
    }
}
