<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxSysRequirements;

/**
 * Collects System information.
 * Admin Menu: Service -> System Requirements -> Main.
 */
class SystemRequirementsMain extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Loads article Mercators info, passes it to Smarty engine and
     * returns name of template file "Mercator_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $oSysReq = new \OxidEsales\Eshop\Core\SystemRequirements();

        $this->_aViewData['aInfo'] = $oSysReq->getSystemInfo();
        $this->_aViewData['aCollations'] = $oSysReq->checkCollation();

        return "sysreq_main.tpl";
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
        $oSysReq = new \OxidEsales\Eshop\Core\SystemRequirements();

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
