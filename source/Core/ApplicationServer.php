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

/**
 * Class used as entity for server node information.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @ignore   This class will not be included in documentation.
 */
class ApplicationServer
{

    /**
     * @var string
     */
    private $_sId;

    /**
     * @var string
     */
    private $_sIp;

    /**
     * @var int
     */
    private $_iTimestamp;

    /**
     * Flag which stores timestamp.
     *
     * @var int
     */
    private $_iLastFrontendUsage;

    /**
     * Flag which stores timestamp.
     *
     * @var int
     */
    private $_iLastAdminUsage;


    /**
     * Flag - server is used or not
     *
     * @var bool
     */
    private $_blIsValid = false;

    /**
     * Sets id.
     *
     * @param string $sId
     */
    public function setId($sId)
    {
        $this->_sId = $sId;
    }

    /**
     * Gets id
     *
     * @return string
     */
    public function getId()
    {
        return $this->_sId;
    }

    /**
     * Sets ip.
     *
     * @param string $ip
     */
    public function setIp($ip)
    {
        $this->_sIp = $ip;
    }

    /**
     * Gets ip.
     *
     * @return string
     */
    public function getIp()
    {
        return $this->_sIp;
    }

    /**
     * Sets timestamp.
     *
     * @param int $timestamp
     */
    public function setTimestamp($timestamp)
    {
        $this->_iTimestamp = $timestamp;
    }

    /**
     * Gets timestamp.
     *
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_iTimestamp;
    }

    /**
     * Sets last admin usage.
     *
     * @param int|null $lastAdminUsage
     */
    public function setLastAdminUsage($lastAdminUsage)
    {
        $this->_iLastAdminUsage = $lastAdminUsage;
    }

    /**
     * Gets last admin usage.
     *
     * @return int|null
     */
    public function getLastAdminUsage()
    {
        return $this->_iLastAdminUsage;
    }

    /**
     * Sets last frontend usage.
     *
     * @param int|null $lastFrontendUsage Admin server flag which stores timestamp.
     */
    public function setLastFrontendUsage($lastFrontendUsage)
    {
        $this->_iLastFrontendUsage = $lastFrontendUsage;
    }

    /**
     * Gets last frontend usage.
     *
     * @return int|null Frontend server flag which stores timestamp.
     */
    public function getLastFrontendUsage()
    {
        return $this->_iLastFrontendUsage;
    }

    /**
     * Sets whether is valid.
     *
     * @param bool $valid Flag to set if application server is valid
     */
    public function setIsValid($valid = true)
    {
        $this->_blIsValid = $valid;
    }

    /**
     * Checks if valid.
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->_blIsValid;
    }
}
