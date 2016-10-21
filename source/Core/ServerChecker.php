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

namespace OxidEsales\EshopCommunity\Core;

use oxRegistry;
use oxApplicationServer;

/**
 * Checks if server node is valid, information is not outdated.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class ServerChecker
{

    /**
     * Time in seconds, server node information life time.
     */
    const NODE_VALIDITY_TIME = 43200;

    /**
     * Current checking time - timestamp.
     *
     * @var int
     */
    private $_iCurrentTime = 0;

    /**
     * Class constructor. Sets current time to $_iCurrentTime parameter.
     */
    public function __construct()
    {
        $this->_iCurrentTime = oxRegistry::get("oxUtilsDate")->getTime();
    }

    /**
     * Checks if server node is valid.
     *
     * @param oxApplicationServer $oServer
     *
     * @return bool
     */
    public function check(oxApplicationServer $oServer)
    {
        return $this->_isValid($oServer) && $this->_isServerTimeValid($oServer->getTimestamp());
    }

    /**
     * Check is server information out dated.
     *
     * @param oxApplicationServer $oServer
     *
     * @return bool
     */
    private function _isValid($oServer)
    {
        return ($oServer->getTimestamp() - $this->_getCurrentTime() + self::NODE_VALIDITY_TIME) > 0;
    }

    /**
     * Method checks if server time was not rolled back.
     *
     * @param int $iServerTimeInPast timestamp of time in past
     *
     * @return bool
     */
    private function _isServerTimeValid($iServerTimeInPast)
    {
        return ($this->_getCurrentTime() - $iServerTimeInPast) >= 0;
    }

    /**
     * Returns current time - timestamp.
     *
     * @return int
     */
    private function _getCurrentTime()
    {
        return $this->_iCurrentTime;
    }
}
