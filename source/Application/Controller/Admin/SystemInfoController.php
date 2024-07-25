<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;

/**
 * Admin systeminfo manager.
 * Returns template "systeminfo" and phphinfo() result to frame.
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
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

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
            }
            $context = [
                "oViewConf" => $this->_aViewData["oViewConf"],
                "oView" => $this->_aViewData["oView"],
                "shop" => $this->_aViewData["shop"] ?? 1,
                "isdemo" => $myConfig->isDemoShop(),
                "aSystemInfo" => $aSystemInfo
            ];

            ob_start();
            echo $this->getRenderer()->renderTemplate("systeminfo", $context);
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
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)
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
