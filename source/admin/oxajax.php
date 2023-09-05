<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Core\Exception\FileException;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Registry;

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
    $myConfig = Registry::getConfig();

    // Includes Utility module.
    $sUtilModule = $myConfig->getConfigParam('sUtilModule');
    if ($sUtilModule && file_exists(getShopBasePath() . "modules/" . $sUtilModule)) {
        include_once getShopBasePath() . "modules/" . $sUtilModule;
    }

    $myConfig->setConfigParam('blAdmin', true);

    // authorization
    if (
        !(
        Registry::getSession()->checkSessionChallenge()
        && count(Registry::getUtilsServer()->getOxCookie())
        && Registry::getUtils()->checkAccessRights()
        )
    ) {
        header("location:index.php");
        Registry::getUtils()->showMessageAndExit("");
    }

    if ($sContainer = Registry::getRequest()->getRequestParameter('container')) {
        $sContainer = strtolower(trim(basename($sContainer)));

        try {
            // Controller name for ajax class is automatically done from the request.
            // Request comes from the same named class without _ajax.
            $ajaxContainerClassName = $sContainer . '_ajax';
            // Ensures that the right name is returned when a module introduce an ajax class.
            $containerClass = Registry::getControllerClassNameResolver()->getClassNameById($ajaxContainerClassName);
            $oAjaxComponent = oxNew($containerClass);
        } catch (SystemComponentException $oCe) {
            $oEx = new FileException();
            $oEx->setMessage('EXCEPTION_FILENOTFOUND' . ' ' . $ajaxContainerClassName);
            throw $oEx;
        }

        $oAjaxComponent->setName($sContainer);
        $oAjaxComponent->processRequest(Registry::getRequest()->getRequestParameter('fnc'));
    }

    $myConfig->pageClose();
}
