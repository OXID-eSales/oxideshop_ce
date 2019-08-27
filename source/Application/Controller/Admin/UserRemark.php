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
 * Admin user history settings manager.
 * Collects user history settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> History.
 */
class UserRemark extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxuser, oxlist and
     * oxRemark objects, passes data to Smarty engine and returns name of
     * template file "user_remark.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        $sRemoxId = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rem_oxid");
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $oUser->load($soxId);
            $this->_aViewData["edit"] = $oUser;

            // all remark
            $oRems = oxNew(\OxidEsales\Eshop\Core\Model\ListModel::class);
            $oRems->init("oxremark");
            $sSelect = "select * from oxremark where oxparentid = :oxparentid order by oxcreate desc";
            $oRems->selectString($sSelect, [
                ':oxparentid' => $oUser->getId()
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

        return "user_remark.tpl";
    }

    /**
     * Saves user history text changes.
     */
    public function save()
    {
        parent::save();

        $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);

        // try to load if exists
        $oRemark->load(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rem_oxid"));

        $oRemark->oxremark__oxtext = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("remarktext"));
        $oRemark->oxremark__oxheader = new \OxidEsales\Eshop\Core\Field(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("remarkheader"));
        $oRemark->oxremark__oxparentid = new \OxidEsales\Eshop\Core\Field($this->getEditObjectId());
        $oRemark->oxremark__oxtype = new \OxidEsales\Eshop\Core\Field("r");
        $oRemark->save();
    }

    /**
     * Deletes user actions history record.
     */
    public function delete()
    {
        $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
        $oRemark->delete(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("rem_oxid"));
    }
}
