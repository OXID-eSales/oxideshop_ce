<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Newsletter plain manager.
 * Performs newsletter creation (plain text format, collects neccessary information).
 * Admin Menu: Customer Info -> Newsletter -> Text.
 */
class NewsletterPlain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes prent method parent::render(), creates oxnewsletter object
     * and passes it's data to smarty. Returns name of template file
     * "newsletter_plain.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData["oxid"] = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            // load object
            $oNewsletter = oxNew(\OxidEsales\Eshop\Application\Model\Newsletter::class);
            $oNewsletter->load($soxId);
            $this->_aViewData["edit"] = $oNewsletter;
        }

        return "newsletter_plain.tpl";
    }

    /**
     * Saves newsletter text in plain text format.
     */
    public function save()
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editval");

        // shopid
        $sShopID = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("actshop");
        $aParams['oxnewsletter__oxshopid'] = $sShopID;

        $oNewsletter = oxNew(\OxidEsales\Eshop\Application\Model\Newsletter::class);
        if ($soxId != "-1") {
            $oNewsletter->load($soxId);
        } else {
            $aParams['oxnewsletter__oxid'] = null;
        }
        //$aParams = $oNewsletter->ConvertNameArray2Idx( $aParams);
        $oNewsletter->assign($aParams);
        $oNewsletter->save();

        // set oxid if inserted
        $this->setEditObjectId($oNewsletter->getId());
    }
}
