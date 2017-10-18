<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin dyn manager.
 *
 * @subpackage dyn
 *
 * @deprecated since v5.3 (2016-05-20); Dynpages will be removed.
 */
class DynamicInterface extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Returns view id
     *
     * @return string
     */
    public function getViewId()
    {
        return 'dyn_interface';
    }
}
