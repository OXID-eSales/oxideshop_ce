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

if (!defined('OX_IS_ADMIN')) {
    define('OX_IS_ADMIN', true);
}

if (!defined('OX_ADMIN_DIR')) {
    define('OX_ADMIN_DIR', dirname(__FILE__));
}

require_once dirname(__FILE__) . "/../bootstrap.php";

// processing ..
$blAjaxCall = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
if ($blAjaxCall) {
    // Setting error reporting mode
    error_reporting(E_ALL ^ E_NOTICE);

    $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();

    // Includes Utility module.
    $sUtilModule = $myConfig->getConfigParam('sUtilModule');
    if ($sUtilModule && file_exists(getShopBasePath() . "modules/" . $sUtilModule)) {
        include_once getShopBasePath() . "modules/" . $sUtilModule;
    }

    $myConfig->setConfigParam('blAdmin', true);

    // authorization
    if (!(\OxidEsales\Eshop\Core\Registry::getSession()->checkSessionChallenge() && count(\OxidEsales\Eshop\Core\Registry::getUtilsServer()->getOxCookie()) && \OxidEsales\Eshop\Core\Registry::getUtils()->checkAccessRights())) {
        header("location:index.php");
        \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("");
    }

    if ($sContainer = \OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('container')) {
        $sContainer = trim(strtolower(basename($sContainer)));

        try {
            $oAjaxComponent = oxNew($sContainer . '_ajax');
        } catch (\OxidEsales\Eshop\Core\Exception\SystemComponentException $oCe) {
            $oEx = new FileException();
            $oEx->setMessage('EXCEPTION_FILENOTFOUND' . ' ' . $sContainer . '_ajax.php');
            $oEx->debugOut();
            throw $oEx;
        }

        $oAjaxComponent->setName($sContainer);
        $oAjaxComponent->processRequest(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter('fnc'));
    }

    $myConfig->pageClose();

    // closing session handlers
    // session_write_close();
    return;
}
