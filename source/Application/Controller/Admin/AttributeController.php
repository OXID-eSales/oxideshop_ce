<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Returns template, that arranges two other templates ("attribute_list.tpl"
 * and "attribute_main.tpl") to frame.
 * Admin Menu: Manage Products -> Attributes.
 */
class AttributeController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'attribute.tpl';
}
