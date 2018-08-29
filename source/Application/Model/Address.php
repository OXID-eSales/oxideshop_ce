<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Model;

/**
 * Address handler
 */
class Address extends \OxidEsales\Eshop\Core\Model\BaseModel
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
     * @var oxState
     */
    protected $_oStateObject = null;

    /**
     * Returns oxState object
     *
     * @return oxState
     */
    protected function _getStateObject()
    {
        if (is_null($this->_oStateObject)) {
            $this->_oStateObject = oxNew(\OxidEsales\Eshop\Application\Model\State::class);
        }

        return $this->_oStateObject;
    }

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        $this->init('oxaddress');
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
        $sLastName = $this->oxaddress__oxlname->value;
        $sStreet = $this->oxaddress__oxstreet->value;
        $sStreetNr = $this->oxaddress__oxstreetnr->value;
        $sCity = $this->oxaddress__oxcity->value;

        //format it
        $sAddress = "";
        if ($sFirstName || $sLastName) {
            $sAddress = $sFirstName . ($sFirstName ? " " : "") . "$sLastName, ";
        }
        $sAddress .= "$sStreet $sStreetNr, $sCity";

        return trim($sAddress);
    }

    /**
     * Returns encoded address.
     *
     * @return string
     */
    public function getEncodedDeliveryAddress()
    {
        return md5($this->_getMergedAddressFields());
    }

    /**
     * Get state id for current address
     *
     * @return mixed
     */
    public function getStateId()
    {
        return $this->oxaddress__oxstateid->value;
    }


    /**
     * Get state title
     *
     * @param string $sId state ID
     *
     * @return string
     */
    public function getStateTitle($sId = null)
    {
        $oState = $this->_getStateObject();

        if (is_null($sId)) {
            $sId = $this->getStateId();
        }

        return $oState->getTitleById($sId);
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
     */
    public function setSelected()
    {
        $this->_blSelected = true;
    }

    /**
     * Returns merged address fields.
     *
     * @return string
     */
    protected function _getMergedAddressFields()
    {
        $sDelAddress = '';
        $sDelAddress .= $this->oxaddress__oxcompany;
        $sDelAddress .= $this->oxaddress__oxfname;
        $sDelAddress .= $this->oxaddress__oxlname;
        $sDelAddress .= $this->oxaddress__oxstreet;
        $sDelAddress .= $this->oxaddress__oxstreetnr;
        $sDelAddress .= $this->oxaddress__oxaddinfo;
        $sDelAddress .= $this->oxaddress__oxcity;
        $sDelAddress .= $this->oxaddress__oxcountryid;
        $sDelAddress .= $this->oxaddress__oxstateid;
        $sDelAddress .= $this->oxaddress__oxzip;
        $sDelAddress .= $this->oxaddress__oxfon;
        $sDelAddress .= $this->oxaddress__oxfax;
        $sDelAddress .= $this->oxaddress__oxsal;

        return $sDelAddress;
    }
}
