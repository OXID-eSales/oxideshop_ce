<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Theme\Bridge\ThemeViewItemFactoryInterface;

/**
 * Admin theme list.
 * Lists installed themes.
 * Admin Menu: Extensions -> Themes.
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
        parent::render();

        $this->_aViewData['mylist'] = ContainerFacade::get(ThemeViewItemFactoryInterface::class)
            ->getAll((int) Registry::getConfig()->getShopId());

        return 'theme_list';
    }
}
