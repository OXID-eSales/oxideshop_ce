<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;

/**
 * Newsletter preview manager.
 * Creates plaintext and HTML format newsletter preview.
 * Admin Menu: Customer Info -> Newsletter -> Preview.
 */
class NewsletterPreview extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxnewsletter object
     * and passes it's data to Smarty engine, returns name of template file
     * "newsletter_preview.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if ($soxId != "-1" && isset($soxId)) {
            // load object
            $oNewsletter = oxNew(\OxidEsales\Eshop\Application\Model\Newsletter::class);
            $oNewsletter->load($soxId);
            $this->_aViewData["edit"] = $oNewsletter;

            // user
            $sUserID = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable("auth");

            // assign values to the newsletter and show it
            $oNewsletter->prepare($sUserID, $this->getConfig()->getConfigParam('bl_perfLoadAktion'));

            $this->_aViewData["previewhtml"] = $oNewsletter->getHtmlText();
            $this->_aViewData["previewtext"] = $oNewsletter->getPlainText();
        }

        return "newsletter_preview.tpl";
    }
}
