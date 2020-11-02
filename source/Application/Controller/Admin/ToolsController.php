<?php

declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin systeminfo manager.
 * Returns template, that arranges two other templates ("delivery_list.tpl"
 * and "delivery_main.tpl") to frame.
 */
class ToolsController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Executes parent method parent::render(), prints shop and
     * PHP configuration information.
     *
     * @return string
     */
    public function render()
    {
        if (\OxidEsales\Eshop\Core\Registry::getConfig()->isDemoShop()) {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit('Access denied !');
        }

        parent::render();

        return 'tools.tpl';
    }
}
