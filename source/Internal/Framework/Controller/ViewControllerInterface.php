<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Controller;

interface ViewControllerInterface
{
    public function init();

    public function render();

    public function executeFunction($function);

    public function setClassKey($classKey);

    public function getClassKey();

    public function setFncName($fncName);

    public function getFncName();

    public function setViewParameters($params = null);

    public function getViewParameter($key);

    public function setViewData($viewData = null);

    public function getViewData();

    public function getViewId();

    public function getIsCallForCache();

    /*
     * @deprecated
     *
     * Added only for BC and will be removed in the next major with 'charset' language string.
     */
    public function getCharSet();
}
