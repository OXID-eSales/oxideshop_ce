<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
