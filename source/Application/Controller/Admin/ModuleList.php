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
class ModuleList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * @var array Loaded modules array
     *
     */
    protected $_aModules = [];


    /**
     * Calls parent::render() and returns name of template to render
     *
     * @return string
     */
    public function render()
    {
        $sModulesDir = $this->getConfig()->getModulesDir();

        $oModuleList = oxNew(\OxidEsales\Eshop\Core\Module\ModuleList::class);
        $aModules = $oModuleList->getModulesFromDir($sModulesDir);

        parent::render();

        // assign our list
        $this->_aViewData['mylist'] = $aModules;

        return 'module_list.tpl';
    }
}
