<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Collects System information.
 * Admin Menu: Service -> System Requirements -> Main.
 */
class SystemRequirementsMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        parent::render();

        $oSysReq = oxNew(\OxidEsales\Eshop\Core\SystemRequirements::class);

        $this->_aViewData['aInfo'] = $oSysReq->getSystemInfo();
        $this->_aViewData['aCollations'] = $oSysReq->checkCollation();

        return "sysreq_main";
    }

    /**
     * Returns module state
     *
     * @param int $iModuleState state integer value
     *
     * @return string
     */
    public function getModuleClass($iModuleState)
    {
        switch ($iModuleState) {
            case 2:
                $sClass = 'pass';
                break;
            case 1:
                $sClass = 'pmin';
                break;
            case -1:
                $sClass = 'null';
                break;
            default:
                $sClass = 'fail';
                break;
        }
        return $sClass;
    }

    /**
     * Returns hint URL
     *
     * @param string $sIdent Module ident
     *
     * @return string
     */
    public function getReqInfoUrl($sIdent)
    {
        $oSysReq = oxNew(\OxidEsales\Eshop\Core\SystemRequirements::class);

        return $oSysReq->getReqInfoUrl($sIdent);
    }

    /**
     * return missing template blocks
     *
     * @see \OxidEsales\Eshop\Core\SystemRequirements::getMissingTemplateBlocks
     *
     * @return array
     */
    public function getMissingTemplateBlocks()
    {
        $oSysReq = oxNew(\OxidEsales\Eshop\Core\SystemRequirements::class);

        return $oSysReq->getMissingTemplateBlocks();
    }
}
