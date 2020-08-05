<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin theme manager.
 * Returns template, that arranges two other templates ("theme_list"
 * and "theme_main") to frame.
 * Admin Menu: Main Menu -> Theme.
 */
class ThemeController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Executes parent method parent::render() and returns name of template
     * file "theme".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return "theme";
    }
}
