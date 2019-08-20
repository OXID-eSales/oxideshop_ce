<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;
use oxAdminDetails;

/**
 * Newsletter sending manager.
 * Performs sending of newsletter to selected user groups.
 */
class NewsletterSend extends \OxidEsales\Eshop\Application\Controller\Admin\NewsletterSelection
{
    /**
     * Mail sending errors array
     *
     * @var array
     */
    protected $_aMailErrors = [];

    /**
     * Executes parent method parent::render(), creates oxnewsletter object,
     * sends newsletter to users of chosen groups and returns name of template
     * file "newsletter_send.tpl"/"newsletter_done.tpl".
     *
     * @return string
     */
    public function render()
    {
        oxAdminDetails::render();

        // calculating
        $iUserCount = $this->getUserCount();

        $iStart = (int) \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("iStart");

        $oNewsletter = oxNew(\OxidEsales\Eshop\Application\Model\Newsletter::class);
        $oNewsletter->load($this->getEditObjectId());
        $oNewsletterGroups = $oNewsletter->getGroups();

        // send emails....
        $oDB = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $sQGroups = " ( oxobject2group.oxgroupsid in ( ";
        $blSep = false;
        foreach ($oNewsletterGroups as $sInGroup) {
            $sSearchKey = $sInGroup->oxgroups__oxid->value;
            if ($blSep) {
                $sQGroups .= ",";
            }
            $sQGroups .= $oDB->quote($sSearchKey);
            $blSep = true;
        }
        $sQGroups .= ") )";

        // no group selected
        if (!$blSep) {
            $sQGroups = " oxobject2group.oxobjectid is null ";
        }

        $myConfig = $this->getConfig();

        $iSendCnt = 0;
        $iMaxCnt = (int) $myConfig->getConfigParam('iCntofMails');
        $sShopId = $myConfig->getShopId();

        $sQ = "select oxnewssubscribed.oxuserid, oxnewssubscribed.oxemail, oxnewssubscribed.oxsal,
           oxnewssubscribed.oxfname, oxnewssubscribed.oxlname, oxnewssubscribed.oxemailfailed
           from oxnewssubscribed left join oxobject2group on
           oxobject2group.oxobjectid = oxnewssubscribed.oxuserid where
           ( oxobject2group.oxshopid = :oxshopid or oxobject2group.oxshopid is null ) and
           $sQGroups and oxnewssubscribed.oxdboptin = 1 and oxnewssubscribed.oxshopid = :oxshopid
           group by oxnewssubscribed.oxemail";

        $oRs = $oDB->selectLimit($sQ, 100, $iStart, [
            ':oxshopid' => $sShopId
        ]);
        $blContinue = ($oRs != false && $oRs->count() > 0);

        if ($blContinue) {
            $blLoadAction = $myConfig->getConfigParam('bl_perfLoadAktion');
            while (!$oRs->EOF && $iSendCnt < $iMaxCnt) {
                if ($oRs->fields['oxemailfailed'] != "1") {
                    $sUserId = $oRs->fields['oxuserid'];
                    $iSendCnt++;

                    // must check if such user is in DB
                    // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
                    if (!\OxidEsales\Eshop\Core\DatabaseProvider::getMaster()->getOne("select oxid from oxuser where oxid = :oxid", [':oxid' => $sUserId])) {
                        $sUserId = null;
                    }

                    // #559
                    if (!isset($sUserId) || !$sUserId) {
                        // there is no user object so we fake one
                        $oUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
                        $oUser->oxuser__oxusername = new \OxidEsales\Eshop\Core\Field($oRs->fields['oxemail']);
                        $oUser->oxuser__oxsal = new \OxidEsales\Eshop\Core\Field($oRs->fields['oxsal']);
                        $oUser->oxuser__oxfname = new \OxidEsales\Eshop\Core\Field($oRs->fields['oxfname']);
                        $oUser->oxuser__oxlname = new \OxidEsales\Eshop\Core\Field($oRs->fields['oxlname']);
                        $oNewsletter->prepare($oUser, $blLoadAction);
                    } else {
                        $oNewsletter->prepare($sUserId, $blLoadAction);
                    }

                    if ($oNewsletter->send($iSendCnt)) {
                        // add user history
                        $oRemark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
                        $oRemark->oxremark__oxtext = new \OxidEsales\Eshop\Core\Field($oNewsletter->getPlainText());
                        $oRemark->oxremark__oxparentid = new \OxidEsales\Eshop\Core\Field($sUserId);
                        $oRemark->oxremark__oxshopid = new \OxidEsales\Eshop\Core\Field($sShopId);
                        $oRemark->oxremark__oxtype = new \OxidEsales\Eshop\Core\Field("n");
                        $oRemark->save();
                    } else {
                        $this->_aMailErrors[] = "problem sending to : " . $oRs->fields['oxemail'];
                    }
                }

                $oRs->fetchRow();
                $iStart++;
            }
        }

        $iSend = $iSendCnt + (ceil($iStart / $iMaxCnt) - 1) * $iMaxCnt;
        $iSend = $iSend > $iUserCount ? $iUserCount : $iSend;

        $this->_aViewData["iStart"] = $iStart;
        $this->_aViewData["iSend"] = $iSend;

        // end ?
        if ($blContinue) {
            return "newsletter_send.tpl";
        } else {
            $this->resetUserCount();

            return "newsletter_done.tpl";
        }
    }

    /**
     * Returns count of users assigned to active newsletter receiver group
     *
     * @return int
     */
    public function getUserCount()
    {
        $iCnt = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("iUserCount");
        if ($iCnt === null) {
            $iCnt = parent::getUserCount();
            \OxidEsales\Eshop\Core\Registry::getSession()->setVariable("iUserCount", $iCnt);
        }

        return $iCnt;
    }

    /**
     * Resets users count
     */
    public function resetUserCount()
    {
        \OxidEsales\Eshop\Core\Registry::getSession()->deleteVariable("iUserCount");
        $this->_iUserCount = null;
    }

    /**
     * Returns newsletter mailing errors
     *
     * @return array
     */
    public function getMailErrors()
    {
        return $this->_aMailErrors;
    }

    /**
     * Overrides parent method to pass referred id
     *
     * @param string $sNode referred id
     */
    protected function _setupNavigation($sNode)
    {
        $sNode = 'newsletter_list';

        $myAdminNavig = $this->getNavigation();

        // active tab
        $iActTab = 3;

        // tabs
        $this->_aViewData['editnavi'] = $myAdminNavig->getTabs($sNode, $iActTab);

        // active tab
        $this->_aViewData['actlocation'] = $myAdminNavig->getActiveTab($sNode, $iActTab);

        // default tab
        $this->_aViewData['default_edit'] = $myAdminNavig->getActiveTab($sNode, $this->_iDefEdit);

        // passign active tab number
        $this->_aViewData['actedit'] = $iActTab;
    }

    /**
     * Does nothing, called in derived template
     */
    public function getListSorting()
    {
    }
}
