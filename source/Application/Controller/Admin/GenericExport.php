<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin dyn General export manager.
 */
class GenericExport extends \OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = 'genexport_do';

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = 'genexport_main';

    /**
     * Current view ID getter helps to identify navigation position.
     * Bypassing dynexportbase::getViewId
     *
     * @return string
     */
    public function getViewId()
    {
        return \OxidEsales\Eshop\Application\Controller\Admin\AdminController::getViewId();
    }
}
