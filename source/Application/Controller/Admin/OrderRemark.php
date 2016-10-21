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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;

/**
 * Admin order remark manager.
 * Collects order remark information, updates it on user submit, etc.
 * Admin Menu: Orders -> Display Orders -> History.
 */
class OrderRemark extends \oxAdminDetails
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
        $sRemoxId = oxRegistry::getConfig()->getRequestParameter("rem_oxid");
        if (isset($soxId) && $soxId != "-1") {
            $oOrder = oxNew("oxorder");
            $oOrder->load($soxId);

            // all remark
            $oRems = oxNew("oxlist");
            $oRems->init("oxremark");
            $sUserIdField = 'oxorder__oxuserid';
            $sQuotedUserId = oxDb::getDb()->quote($oOrder->$sUserIdField->value);
            $sSelect = "select * from oxremark where oxparentid=" . $sQuotedUserId . " order by oxcreate desc";
            $oRems->selectString($sSelect);
            foreach ($oRems as $key => $val) {
                if ($val->oxremark__oxid->value == $sRemoxId) {
                    $val->selected = 1;
                    $oRems[$key] = $val;
                    break;
                }
            }

            $this->_aViewData["allremark"] = $oRems;

            if (isset($sRemoxId)) {
                $oRemark = oxNew("oxRemark");
                $oRemark->load($sRemoxId);
                $this->_aViewData["remarktext"] = $oRemark->oxremark__oxtext->value;
                $this->_aViewData["remarkheader"] = $oRemark->oxremark__oxheader->value;
            }
        }

        return "order_remark.tpl";
    }

    /**
     * Saves order history item text changes.
     */
    public function save()
    {
        parent::save();

        $oOrder = oxNew("oxorder");
        if ($oOrder->load($this->getEditObjectId())) {
            $oRemark = oxNew("oxremark");
            $oRemark->load(oxRegistry::getConfig()->getRequestParameter("rem_oxid"));

            $oRemark->oxremark__oxtext = new oxField(oxRegistry::getConfig()->getRequestParameter("remarktext"));
            $oRemark->oxremark__oxheader = new oxField(oxRegistry::getConfig()->getRequestParameter("remarkheader"));
            $oRemark->oxremark__oxtype = new oxField("r");
            $oRemark->oxremark__oxparentid = new oxField($oOrder->oxorder__oxuserid->value);
            $oRemark->save();
        }
    }

    /**
     * Deletes order history item.
     */
    public function delete()
    {
        $oRemark = oxNew("oxRemark");
        $oRemark->delete(oxRegistry::getConfig()->getRequestParameter("rem_oxid"));
    }
}
