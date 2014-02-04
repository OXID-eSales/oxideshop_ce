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
 * Admin order remark manager.
 * Collects order remark information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> History.
 * @package admin
 */
class Order_Remark extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxorder and
     * oxlist objects, passes it's data to Smarty engine and returns
     * name of template file "user_remark.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        $sRemoxId = oxConfig::getParameter( "rem_oxid");
        if ( $soxId != "-1" && isset( $soxId)) {
            $oOrder = oxNew( "oxorder" );
            $oOrder->load( $soxId);

            // all remark
            $oRems = oxNew( "oxlist" );
            $oRems->init( "oxremark");
            $sSelect = "select * from oxremark where oxparentid=".oxDb::getDb()->quote( $oOrder->oxorder__oxuserid->value )." order by oxcreate desc";
            $oRems->selectString( $sSelect );
            foreach ($oRems as $key => $val) {
                if ( $val->oxremark__oxid->value == $sRemoxId) {
                    $val->selected = 1;
                    $oRems[$key] = $val;
                    break;
                }
            }

            $this->_aViewData["allremark"] = $oRems;

            if ( isset( $sRemoxId)) {
                $oRemark = oxNew( "oxRemark" );
                $oRemark->load( $sRemoxId);
                $this->_aViewData["remarktext"]      = $oRemark->oxremark__oxtext->value;
                $this->_aViewData["remarkheader"]    = $oRemark->oxremark__oxheader->value;
            }
        }

        return "order_remark.tpl";
    }

    /**
     * Saves order history item text changes.
     *
     * @return string
     */
    public function save()
    {
        parent::save();

        $oOrder = oxNew( "oxorder" );
        if ( $oOrder->load( $this->getEditObjectId() ) ) {
            $oRemark = oxNew( "oxremark" );
            $oRemark->load( oxConfig::getParameter( "rem_oxid" ) );

            $oRemark->oxremark__oxtext     = new oxField( oxConfig::getParameter( "remarktext" ) );
            $oRemark->oxremark__oxheader   = new oxField( oxConfig::getParameter( "remarkheader" ) );
            $oRemark->oxremark__oxtype     = new oxField( "r" );
            $oRemark->oxremark__oxparentid = new oxField( $oOrder->oxorder__oxuserid->value );
            $oRemark->save();
        }
    }

    /**
     * Deletes order history item.
     *
     * @return null
     */
    public function delete()
    {
        $oRemark = oxNew( "oxRemark" );
        $oRemark->delete( oxConfig::getParameter( "rem_oxid" ) );
    }
}
