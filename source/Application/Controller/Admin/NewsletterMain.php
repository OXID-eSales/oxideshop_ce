<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article main newsletter manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer Info -> Newsletter -> Main.
 */
class NewsletterMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Executes parent method parent::render(), creates oxnewsletter object
     * and passes it's data to Smarty engine. Returns name of template file
     * "newsletter_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->_aViewData['oxid'] = $this->getEditObjectId();
        $oNewsletter = oxNew(\OxidEsales\Eshop\Application\Model\Newsletter::class);

        if (isset($soxId) && '-1' !== $soxId) {
            $oNewsletter->load($soxId);
            $this->_aViewData['edit'] = $oNewsletter;
        }

        // generate editor
        $this->_aViewData['editor'] = $this->_generateTextEditor(
            '100%',
            255,
            $oNewsletter,
            'oxnewsletter__oxtemplate'
        );

        return 'newsletter_main.tpl';
    }

    /**
     * Saves newsletter HTML format text.
     */
    public function save(): void
    {
        $soxId = $this->getEditObjectId();
        $aParams = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('editval');

        // shopid
        $sShopID = \OxidEsales\Eshop\Core\Registry::getSession()->getVariable('actshop');
        $aParams['oxnewsletter__oxshopid'] = $sShopID;

        $oNewsletter = oxNew(\OxidEsales\Eshop\Application\Model\Newsletter::class);
        if ('-1' !== $soxId) {
            $oNewsletter->load($soxId);
        } else {
            $aParams['oxnewsletter__oxid'] = null;
        }

        $oNewsletter->assign($aParams);
        $oNewsletter->save();

        // set oxid if inserted
        $this->setEditObjectId($oNewsletter->getId());
    }
}
