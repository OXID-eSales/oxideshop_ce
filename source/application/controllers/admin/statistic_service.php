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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Admin statistics service setting manager.
 * Collects statistics service settings, updates it on user submit, etc.
 * Admin Menu: Statistics -> Show -> Clear Log.
 * @package admin
 */
class Statistic_Service extends oxAdminDetails
{
    /**
     * Executes parent method parent::render() and returns name of template
     * file "statistic_service.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();
        $this->_aViewData['iLogCount'] = oxDb::getDb()->getOne( "select count(*) from oxlogs where oxshopid = '".$this->getConfig()->getShopId()."'", false, false);

        return "statistic_service.tpl";
    }

    /**
     * Performs cleanup of statistic data for selected period.
     *
     * @return null
     */
    public function cleanup()
    {
        $iTimeFrame = oxConfig::getParameter( "timeframe");
        $dNow = time();
        $sDeleteFrom = date( "Y-m-d H:i:s", mktime( date( "H", $dNow), date( "i", $dNow), date( "s", $dNow), date( "m", $dNow), date( "d", $dNow) - $iTimeFrame, date( "Y", $dNow)));

        $oDb = oxDb::getDb();
        $oDb->Execute( "delete from oxlogs where oxtime < ".$oDb->quote( $sDeleteFrom ) );
    }
}
