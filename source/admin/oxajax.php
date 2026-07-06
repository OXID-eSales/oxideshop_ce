<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\Http\LegacyAjaxRunnerInterface;
use Symfony\Component\HttpFoundation\Response;

if (!defined('OX_IS_ADMIN')) {
    define('OX_IS_ADMIN', true);
}

if (!defined('OX_ADMIN_DIR')) {
    define('OX_ADMIN_DIR', __DIR__);
}

require_once __DIR__ . '/../bootstrap.php';

$blAjaxCall = (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest');
if (!$blAjaxCall) {
    return;
}

ContainerFacade::get(LegacyAjaxRunnerInterface::class)->runController(static function (): Response {
    $myConfig = Registry::getConfig();
    $myConfig->init();

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
        Registry::getUtils()->redirect('index.php', false);
    }

    $content = '';
    $status = Response::HTTP_OK;
    if ($sContainer = Registry::getRequest()->getRequestParameter('container')) {
        $sContainer = strtolower(trim(basename($sContainer)));

        // Ajax class is derived from the container name plus the "_ajax" suffix.
        $ajaxContainerClassName = $sContainer . '_ajax';
        $containerClass = Registry::getControllerClassNameResolver()->getClassNameById($ajaxContainerClassName);

        if ($containerClass !== null) {
            $oAjaxComponent = oxNew($containerClass);
            $oAjaxComponent->setName($sContainer);
            $content = (string) $oAjaxComponent->processRequest(Registry::getRequest()->getRequestParameter('fnc'));
        } else {
            $status = Response::HTTP_NOT_FOUND;
        }
    }

    $myConfig->pageClose();

    return new Response($content, $status);
});
