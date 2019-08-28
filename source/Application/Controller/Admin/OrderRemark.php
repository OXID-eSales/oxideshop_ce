<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
class OrderRemark extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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
        $sRemoxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rem_oxid");
        if (isset($soxId) && $soxId != "-1") {
            $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
            $oOrder->load($soxId);

            // all remark
            $oRems = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oRems->init("oxremark");
            $sUserIdField = 'oxorder__oxuserid';
            $sSelect = "select * from oxremark where oxparentid = :oxparentid order by oxcreate desc";
            $oRems->selectString($sSelect, [
                ':oxparentid' => $oOrder->$sUserIdField->value
            ]);
            foreach ($oRems as $key => $val) {
                if ($val->oxremark__oxid->value == $sRemoxId) {
                    $val->selected = 1;
                    $oRems[$key] = $val;
                    break;
                }
            }

            $this->_aViewData["allremark"] = $oRems;

            if (isset($sRemoxId)) {
                $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
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

        $oOrder = oxNew(\OxidEsales\Eshop\Application\Model\Order::class);
        if ($oOrder->load($this->getEditObjectId())) {
            $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
            $oRemark->load(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rem_oxid"));

            $oRemark->oxremark__oxtext = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("remarktext"));
            $oRemark->oxremark__oxheader = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("remarkheader"));
            $oRemark->oxremark__oxtype = new \OxidEsales\Eshop\Core\Field("r");
            $oRemark->oxremark__oxparentid = new \OxidEsales\Eshop\Core\Field($oOrder->oxorder__oxuserid->value);
            $oRemark->save();
        }
    }

    /**
     * Deletes order history item.
     */
    public function delete()
    {
        $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
        $oRemark->delete(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rem_oxid"));
    }
}
