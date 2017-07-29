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

namespace OxidEsales\EshopCommunity\Application\Model;

use oxDb;
use oxRegistry;

/**
 * Article rate manager.
 * Performs loading, updating, inserting of article rates.
 *
 */
class Rating extends \OxidEsales\Eshop\Core\Model\BaseModel
{

    /**
     * Shop control variable
     *
     * @var string
     */
    protected $_blDisableShopCheck = true;

    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxrating';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxratings');
    }

    /**
     * Checks if user can rate product.
     *
     * @param string $sUserId   user id
     * @param string $sType     object type
     * @param string $sObjectId object id
     *
     * @return bool
     */
    public function allowRating($sUserId, $sType, $sObjectId)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
        $myConfig = $this->getConfig();

        if ($iRatingLogsTimeout = $myConfig->getConfigParam('iRatingLogsTimeout')) {
            $sExpDate = date('Y-m-d H:i:s', \OxidEsales\Eshop\Core\Registry::getUtilsDate()->getTime() - $iRatingLogsTimeout * 24 * 60 * 60);
            $oDb->execute("delete from oxratings where oxtimestamp < '$sExpDate'");
        }
        $sSelect = "select oxid from oxratings where oxuserid = " . $oDb->quote($sUserId) . " and oxtype=" . $oDb->quote($sType) . " and oxobjectid = " . $oDb->quote($sObjectId);
        if ($oDb->getOne($sSelect)) {
            return false;
        }

        return true;
    }


    /**
     * calculates and return objects rating
     *
     * @param string $sObjectId           object id
     * @param string $sType               object type
     * @param array  $aIncludedObjectsIds array of ids
     *
     * @return float
     */
    public function getRatingAverage($sObjectId, $sType, $aIncludedObjectsIds = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQuerySnipet = '';
        if (is_array($aIncludedObjectsIds) && count($aIncludedObjectsIds) > 0) {
            $sQuerySnipet = " AND ( `oxobjectid` = " . $oDb->quote($sObjectId) . " OR `oxobjectid` in ('" . implode("', '", $aIncludedObjectsIds) . "') )";
        } else {
            $sQuerySnipet = " AND `oxobjectid` = " . $oDb->quote($sObjectId);
        }

        $sSelect = "
            SELECT
                AVG(`oxrating`)
            FROM `oxreviews`
            WHERE `oxrating` > 0
                 AND `oxtype` = " . $oDb->quote($sType)
                   . $sQuerySnipet . "
            LIMIT 1";

        $fRating = 0;
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        if ($fRating = $database->getOne($sSelect)) {
            $fRating = round($fRating, 1);
        }

        return $fRating;
    }

    /**
     * calculates and return objects rating count
     *
     * @param string $sObjectId           object id
     * @param string $sType               object type
     * @param array  $aIncludedObjectsIds array of ids
     *
     * @return integer
     */
    public function getRatingCount($sObjectId, $sType, $aIncludedObjectsIds = null)
    {
        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb();

        $sQuerySnipet = '';
        if (is_array($aIncludedObjectsIds) && count($aIncludedObjectsIds) > 0) {
            $sQuerySnipet = " AND ( `oxobjectid` = " . $oDb->quote($sObjectId) . " OR `oxobjectid` in ('" . implode("', '", $aIncludedObjectsIds) . "') )";
        } else {
            $sQuerySnipet = " AND `oxobjectid` = " . $oDb->quote($sObjectId);
        }

        $sSelect = "
            SELECT
                COUNT(*)
            FROM `oxreviews`
            WHERE `oxrating` > 0
                AND `oxtype` = " . $oDb->quote($sType)
                   . $sQuerySnipet . "
            LIMIT 1";

        $iCount = 0;
        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        $masterDb = \OxidEsales\Eshop\Core\DatabaseProvider::getMaster();
        $iCount = $masterDb->getOne($sSelect);

        return $iCount;
    }

    /**
     * Retuns review object type
     *
     * @return string
     */
    public function getObjectType()
    {
        return $this->oxratings__oxtype->value;
    }

    /**
     * Retuns review object id
     *
     * @return string
     */
    public function getObjectId()
    {
        return $this->oxratings__oxobjectid->value;
    }
}
