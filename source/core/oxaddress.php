<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2014
 * @version OXID eShop CE
 * @version   SVN: $Id: oxaddress.php 17768 2009-04-02 10:52:12Z tomas $
 */

/**
 * Address handler
 *
 * @package core
 */
class oxAddress extends oxBase
{
    /**
     * Current class name
     *
     * @var string
     */
    protected $_sClassName = 'oxaddress';

    /**
     * Active address status
     *
     * @var bool
     */
    protected $_blSelected = false;

    /**
     * Class constructor
     *
     * @return null
     */
    public function __construct()
    {
        parent::__construct();
        $this->init( 'oxaddress' );
    }

    /**
     * Magic getter returns address as a single line string
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Formats address as a single line string
     *
     * @return string
     */
    public function toString()
    {
        $sFirstName = $this->oxaddress__oxfname->value;
        $sLastName  = $this->oxaddress__oxlname->value;
        $sStreet    = $this->oxaddress__oxstreet->value;
        $sStreetNr  = $this->oxaddress__oxstreetnr->value;
        $sCity      = $this->oxaddress__oxcity->value;

        //format it
        $sAddress = "";
        if ($sFirstName || $sLastName) {
            $sAddress = $sFirstName. ($sFirstName?" ":"") . "$sLastName, ";
        }
        $sAddress .= "$sStreet $sStreetNr $sCity";

        $sAddress = trim($sAddress);

        return $sAddress;
    }

    /**
     * Returns string representation of address state
     *
     * @return string
     */
    public function getState()
    {
        return $this->oxaddress__oxstateid->value;
    }

    /**
     * Returns TRUE if current address is selected
     *
     * @return bool
     */
    public function isSelected()
    {
        return $this->_blSelected;
    }

    /**
     * Sets address state as selected
     *
     * @return null
     */
    public function setSelected()
    {
        $this->_blSelected = true;
    }
}
