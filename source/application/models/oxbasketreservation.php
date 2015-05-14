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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Basket reservations handler class
 *
 */
class oxBasketReservation extends oxSuperCfg
{

    /**
     * Reservations list
     *
     * @var oxuserbasket
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
     */
    protected function _getReservationsId()
    {
        $sId = oxRegistry::getSession()->getVariable('basketReservationToken');
        if (!$sId) {
            $sId = oxUtilsObject::getInstance()->generateUId();
            oxRegistry::getSession()->setVariable('basketReservationToken', $sId);
        }

        return $sId;
    }

    /**
     * load reservation or create new reservation oxuserbasket
     *
     * @param string $sBasketId basket id for this userbasket
     *
     * @return oxuserbasket
     */
    protected function _loadReservations($sBasketId)
    {
        $oReservations = oxNew('oxuserbasket');
        $aWhere = array('oxuserbaskets.oxuserid' => $sBasketId, 'oxuserbaskets.oxtitle' => 'reservations');

        // creating if it does not exist
        if (!$oReservations->assignRecord($oReservations->buildSelectString($aWhere))) {
            $oReservations->oxuserbaskets__oxtitle = new oxField('reservations');
            $oReservations->oxuserbaskets__oxuserid = new oxField($sBasketId);
            // marking basket as new (it will not be saved in DB yet)
            $oReservations->setIsNewBasket();
        }

        return $oReservations;
    }

    /**
     * get reservations collection
     *
     * @return oxUserBasket
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
     */
    protected function _getReservedItems()
    {
        if (isset($this->_aCurrentlyReserved)) {
            return $this->_aCurrentlyReserved;
        }

        $oReserved = $this->getReservations();
        if (!$oReserved) {
            return array();
        }

        $this->_aCurrentlyReserved = array();
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
     * @param oxBasket $oBasket basket object
     *
     * @return array
     */
    protected function _basketDifference(oxBasket $oBasket)
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
     */
    protected function _reserveArticles($aBasketDiff)
    {
        $blAllowNegativeStock = $this->getConfig()->getConfigParam('blAllowNegativeStock');

        $oReserved = $this->getReservations();
        foreach ($aBasketDiff as $sId => $dAmount) {
            if ($dAmount != 0) {
                $oArticle = oxNew('oxarticle');
                if ($oArticle->load($sId)) {
                    $oArticle->reduceStock(-$dAmount, $blAllowNegativeStock);
                    $oReserved->addItemToBasket($sId, -$dAmount);
                }
            }
        }
        $this->_aCurrentlyReserved = null;
    }

    /**
     * reserve given basket items
     *
     * @param oxBasket $oBasket basket object
     */
    public function reserveBasket(oxBasket $oBasket)
    {
        $this->_reserveArticles($this->_basketDifference($oBasket));
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

        $oArticle = oxNew('oxarticle');
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
            $oArticle = oxNew('oxarticle');
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
     * @return null
     */
    public function discardUnusedReservations($iLimit)
    {
        $oDb = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);
        $iStartTime = oxRegistry::get("oxUtilsDate")->getTime() - (int) $this->getConfig()->getConfigParam('iPsBasketReservationTimeout');
        $oRs = $oDb->select("select oxid from oxuserbaskets where oxtitle = 'reservations' and oxupdate <= $iStartTime limit $iLimit", false, false);
        if ($oRs->EOF) {
            return;
        }
        $aFinished = array();
        while (!$oRs->EOF) {
            $aFinished[] = $oDb->quote($oRs->fields['oxid']);
            $oRs->MoveNext();
        }
        $oRs = $oDb->select("select oxartid, oxamount from oxuserbasketitems where oxbasketid in (" . implode(",", $aFinished) . ")", false, false);
        while (!$oRs->EOF) {
            $oArticle = oxNew('oxarticle');
            if ($oArticle->load($oRs->fields['oxartid'])) {
                $oArticle->reduceStock(-$oRs->fields['oxamount'], true);
            }
            $oRs->MoveNext();
        }
        $oDb->execute("delete from oxuserbasketitems where oxbasketid in (" . implode(",", $aFinished) . ")");
        $oDb->execute("delete from oxuserbaskets where oxid in (" . implode(",", $aFinished) . ")");

        // cleanup basket history also..
        $oDb->execute("delete from oxuserbaskets where oxtitle = 'savedbasket' and oxupdate <= $iStartTime");

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
                $iTimeout -= (oxRegistry::get("oxUtilsDate")->getTime() - (int) $oRev->oxuserbaskets__oxupdate->value);
                oxRegistry::getSession()->setVariable("iBasketReservationTimeout", $oRev->oxuserbaskets__oxupdate->value);
            } elseif (($iSessionTimeout = oxRegistry::getSession()->getVariable("iBasketReservationTimeout"))) {
                $iTimeout -= (oxRegistry::get("oxUtilsDate")->getTime() - (int) $iSessionTimeout);
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
            $iTime = oxRegistry::get("oxUtilsDate")->getTime();
            $oReserved->oxuserbaskets__oxupdate = new oxField($iTime);
            $oReserved->save();

            oxRegistry::getSession()->deleteVariable("iBasketReservationTimeout");
        }
    }
}
