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

use oxRegistry;

/**
 * Admin systeminfo manager.
 * Returns template, that arranges two other templates ("delivery_list.tpl"
 * and "delivery_main.tpl") to frame.
 */
class SystemInfoController extends \OxidEsales\Eshop\Application\Controller\Admin\AdminController
{

    /**
     * Executes parent method parent::render(), prints shop and
     * PHP configuration information.
     *
     * @return null
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $oAuthUser = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $oAuthUser->loadAdminUser();
        $blisMallAdmin = $oAuthUser->oxuser__oxrights->value == "malladmin";

        if ($blisMallAdmin && !$myConfig->isDemoShop()) {
            $aClassVars = get_object_vars($myConfig);
            $aSystemInfo = array();
            $aSystemInfo['pkg.info'] = $myConfig->getPackageInfo();
            $oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
            while (list($name, $value) = each($aClassVars)) {
                if (gettype($value) == "object") {
                    continue;
                }

                if (!$this->isClassVariableVisible($name)) {
                    continue;
                }

                $value = var_export($value, true);
                $value = str_replace("\n", "<br>", $value);
                $aSystemInfo[$name] = $value;
                //echo( "$name = $value <br>");
            }
            $oSmarty->assign("oViewConf", $this->_aViewData["oViewConf"]);
            $oSmarty->assign("oView", $this->_aViewData["oView"]);
            $oSmarty->assign("shop", $this->_aViewData["shop"]);
            $oSmarty->assign("isdemo", $myConfig->isDemoShop());
            $oSmarty->assign("aSystemInfo", $aSystemInfo);
            echo $oSmarty->fetch("systeminfo.tpl");
            echo("<br><br>");

            phpinfo();

            \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("");
        } else {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("Access denied !");
        }
    }

    /**
     * Checks if class var can be shown in systeminfo.
     *
     * @param string $varName
     * @return bool
     */
    protected function isClassVariableVisible($varName)
    {
        return !in_array($varName, [
            'oDB',
            'dbUser',
            'dbPwd',
            'oSerial',
            'aSerials',
            'sSerialNr'
        ]);
    }
}
