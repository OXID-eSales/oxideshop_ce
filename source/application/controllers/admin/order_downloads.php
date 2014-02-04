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
 * Admin order article manager.
 * Collects order articles information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> Articles.
 * @package admin
 */
class Order_Downloads extends oxAdminDetails
{

    /**
     * Active order object
     *
     * @var oxorder
     */
    protected $_oEditObject = null;

    /**
     * Executes parent method parent::render(), passes data
     * to Smarty engine, returns name of template file "order_downloads.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ( $oOrder = $this->getEditObject() ) {
            $this->_aViewData["edit"] = $oOrder;
        }

        return "order_downloads.tpl";
    }

    /**
     * Returns editable order object
     *
     * @return oxorder
     */
    public function getEditObject()
    {
        $soxId = $this->getEditObjectId();
        if ( $this->_oEditObject === null && isset( $soxId ) && $soxId != "-1" ) {
            $this->_oEditObject = oxNew( "oxOrderFileList" );
            $this->_oEditObject->loadOrderFiles( $soxId );
        }
        return $this->_oEditObject;
    }

    /**
     * Returns editable order object
     *
     * @return oxorder
     */
    public function resetDownloadLink()
    {
        $sOrderFileId = oxConfig::getParameter( 'oxorderfileid' );
        $oOrderFile = oxNew("oxorderfile");
        if ( $oOrderFile->load($sOrderFileId) ) {
            $oOrderFile->reset();
            $oOrderFile->save();
        }
    }
}
