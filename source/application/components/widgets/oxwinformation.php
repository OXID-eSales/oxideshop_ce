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
 * @package   views
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 */

/**
 * List of additional shop information links widget.
 * Forms info link list.
 */
class oxwInformation extends oxWidget
{
    /**
     * Current class template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/footer/info.tpl';

    /**
     * Default service keys
     *
     * @var array
     */
    protected $_aServiceKeys = array( 'oximpressum', 'oxagb', 'oxsecurityinfo', 'oxdeliveryinfo', 'oxrightofwithdrawal', 'oxorderinfo', 'oxcredits' );

    /**
     * @param string $sTemplate
     */
    public function setTemplate( $sTemplate )
    {
        $this->_sThisTemplate = $sTemplate;
    }

    /**
     * @return string
     */
    public function getTemplate()
    {
        return $this->_sThisTemplate;
    }

    /**
     * @param $aKeys
     */
    public function setServicesKeys( $aKeys )
    {
        $this->_aServiceKeys = $aKeys;
    }

    /**
     * @return array
     */
    public function getServicesKeys()
    {
        return $this->_aServiceKeys;
    }

    /**
     * Get services content list
     *
     * @return array
     */
    public function getServicesList()
    {
        /** * @var oxContentList $oContentList */
        $oContentList = oxNew( "oxContentList" );

        $oContentList->loadServicesFromDbByKeys( $this->getServicesKeys() );

        $aServices = $this->_extractListToArray( $oContentList );

        return $aServices;
    }

    /**
     * Extract oxContentList object to associative array with oxloadid as keys
     *
     * @param oxContentList $oContentList
     *
     * @return array
     */
    protected function _extractListToArray( oxContentList $oContentList )
    {
        $aContents = $oContentList->getArray();

        $aExtractedContents = array();

        foreach ( $aContents as $oContent ) {
            $aExtractedContents[$oContent->oxcontents__oxloadid->value] = $oContent;
        }

        return $aExtractedContents;
    }
}
