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
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 *
 * @ignore   This class will not be included in documentation.
 */
class oxApplicationServer
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
     * @param string $sId
     */
    public function setId($sId)
    {
        $this->_sId = $sId;
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->_sId;
    }

    /**
     * @param string $sIp
     */
    public function setIp($sIp)
    {
        $this->_sIp = $sIp;
    }

    /**
     * @return string
     */
    public function getIp()
    {
        return $this->_sIp;
    }

    /**
     * @param int $iTimestamp
     */
    public function setTimestamp($iTimestamp)
    {
        $this->_iTimestamp = $iTimestamp;
    }

    /**
     * @return int
     */
    public function getTimestamp()
    {
        return $this->_iTimestamp;
    }

    /**
     * @param int|null $iLastAdminUsage
     */
    public function setLastAdminUsage($iLastAdminUsage)
    {
        $this->_iLastAdminUsage = $iLastAdminUsage;
    }

    /**
     * @return int|null
     */
    public function getLastAdminUsage()
    {
        return $this->_iLastAdminUsage;
    }

    /**
     * @param int|null $iLastFrontendUsage Admin server flag which stores timestamp.
     */
    public function setLastFrontendUsage($iLastFrontendUsage)
    {
        $this->_iLastFrontendUsage = $iLastFrontendUsage;
    }

    /**
     * @return int|null Frontend server flag which stores timestamp.
     */
    public function getLastFrontendUsage()
    {
        return $this->_iLastFrontendUsage;
    }
}