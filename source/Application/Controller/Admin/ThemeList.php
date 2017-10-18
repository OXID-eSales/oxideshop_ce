<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin actionss manager.
 * Sets list template, list object class ('oxactions') and default sorting
 * field ('oxactions.oxtitle').
 * Admin Menu: Manage Products -> Actions.
 */
class ThemeList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Calls parent::render() and returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        $oTheme = oxNew(\OxidEsales\Eshop\Core\Theme::class);

        parent::render();

        // assign our list
        $this->_aViewData['mylist'] = $oTheme->getList();

        return 'theme_list.tpl';
    }
}
