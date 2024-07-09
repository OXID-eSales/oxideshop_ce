<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;

/**
 * CVS export manager.
 * Performs export function according to user chosen categories.
 * Admin Menu: Maine Menu -> Im/Export -> Export.
 */
class ToolsMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        if (Registry::getConfig()->isDemoShop()) {
            Registry::getUtils()->showMessageAndExit("Access denied !");
        }

        parent::render();

        $oAuthUser = oxNew(User::class);
        $oAuthUser->loadAdminUser();
        $this->_aViewData["blIsMallAdmin"] = $oAuthUser->oxuser__oxrights->value == "malladmin";

        $this->_aViewData['showViewUpdate'] = ContainerFacade::getParameter('oxid_show_update_views_button');

        return "tools_main";
    }
}
