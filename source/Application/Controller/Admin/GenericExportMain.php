<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin general export manager.
 */
class GenericExportMain extends \OxidEsales\Eshop\Application\Controller\Admin\DynamicExportBaseController
{
    /**
     * Export class name
     *
     * @var string
     */
    public $sClassDo = "genExport_do";

    /**
     * Export ui class name
     *
     * @var string
     */
    public $sClassMain = "genExport_main";

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = "dyn_exportdefault.tpl";

    /**
     * Creates shop object, passes shop data to Smarty engine and returns name of
     * template file
     *
     * @return string
     */
    public function render()
    {
        $this->createMainExportView();

        return parent::render();
    }

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
