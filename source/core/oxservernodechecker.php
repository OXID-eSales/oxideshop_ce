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
 * Class oxServerNodeChecker Checks if server node is valid.
 *
 * @internal Do not make a module extension for this class.
 * @see http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore This class will not be included in documentation.
 */
class oxServerNodeChecker
{
    /**
     * Time in seconds which shows how long server node is valid.
     */
    CONST NODE_VALIDITY_TIME = 82800;

    /**
     * Checks if server node is valid.
     *
     * @param oxServerNode $oServerNode
     *
     * @return bool
     */
    public function check(oxServerNode $oServerNode)
    {
        $iServerNodeTime = $oServerNode->getTimestamp();
        $iCurrentTime = oxRegistry::get("oxUtilsDate")->getTime();

        $iTimeToLive = $this->_getLeftTimeTillNodeIsValid($iServerNodeTime, $iCurrentTime);

        $blResult = false;
        if ($iTimeToLive > 0 && $this->_isServerTimeValid($iCurrentTime, $iServerNodeTime)) {
            $blResult = true;
        }

        return $blResult;
    }

    /**
     * Returns left time till server node is valid.
     *
     * @param $iServerNodeTime
     * @param $iCurrentTime
     * @return int
     */
    private function _getLeftTimeTillNodeIsValid($iServerNodeTime, $iCurrentTime)
    {
        return $iServerNodeTime - $iCurrentTime + self::NODE_VALIDITY_TIME;
    }

    /**
     * Method checks if server time was not changed till last node update.
     *
     * @param $iCurrentTime
     * @param $iServerNodeTime
     *
     * @return bool
     */
    private function _isServerTimeValid($iCurrentTime, $iServerNodeTime)
    {
        return ($iCurrentTime - $iServerNodeTime) >= 0;
    }
}