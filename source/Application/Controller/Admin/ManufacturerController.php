<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Returns template, that arranges two other templates ("manufacturer_list.tpl"
 * and "manufacturer_main.tpl") to frame.
 * Admin Menu: Settings -> Manufacturers
 */
class ManufacturerController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'manufacturer.tpl';
}
