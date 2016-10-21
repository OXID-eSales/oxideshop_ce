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

/**
 * Order delivery set manager.
 *
 */
class DeliverySet extends \oxI18n
{

    /**
     * Current object class name
     *
     * @var string
     */
    protected $_sClassName = 'oxdeliveryset';

    /**
     * Class constructor, initiates parent constructor (parent::oxBase()).
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxdeliveryset');
    }

    /**
     * Delete this object from the database, returns true on success.
     *
     * @param string $sOxId Object ID(default null)
     *
     * @return bool
     */
    public function delete($sOxId = null)
    {
        if (!$sOxId) {
            $sOxId = $this->getId();
        }
        if (!$sOxId) {
            return false;
        }

        $oDb = oxDb::getDb();

        $sOxIdQuoted = $oDb->quote($sOxId);
        $oDb->execute('delete from oxobject2payment where oxobjectid = ' . $sOxIdQuoted);
        $oDb->execute('delete from oxobject2delivery where oxdeliveryid = ' . $sOxIdQuoted);
        $oDb->execute('delete from oxdel2delset where oxdelsetid = ' . $sOxIdQuoted);

        return parent::delete($sOxId);
    }

    /**
     * returns delivery set id
     *
     * @param string $sTitle delivery name
     *
     * @return string
     */
    public function getIdByName($sTitle)
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT `oxid` FROM `" . getViewName('oxdeliveryset') . "` WHERE  `oxtitle` = " . $oDb->quote($sTitle);
        $sId = $oDb->getOne($sQ);

        return $sId;
    }
}
