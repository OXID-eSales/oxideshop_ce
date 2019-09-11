<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

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
            $aSystemInfo = [];
            $aSystemInfo['pkg.info'] = $myConfig->getPackageInfo();
            foreach ($aClassVars as $name => $value) {
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
            $context = [
                "oViewConf" => $this->_aViewData["oViewConf"],
                "oView" => $this->_aViewData["oView"],
                "shop" => $this->_aViewData["shop"],
                "isdemo" => $myConfig->isDemoShop(),
                "aSystemInfo" => $aSystemInfo
            ];

            ob_start();
            echo $this->getRenderer()->renderTemplate("systeminfo.tpl", $context);
            echo("<br><br>");

            phpinfo();
            $sMessage = ob_get_clean();

            \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit($sMessage);
        } else {
            return \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("Access denied !");
        }
    }

    /**
     * @internal
     *
     * @return TemplateRendererInterface
     */
    private function getRenderer()
    {
        return $this->getContainer()
            ->get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
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
