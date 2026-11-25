<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\WidgetControl;

class IncludeWidgetLogic
{
    /**
     * @param array $params
     *
     * @return mixed
     */
    public function renderWidget(array $params)
    {
        $class = '';
        if (isset($params['cl'])) {
            $class = strtolower($params['cl']);
            unset($params['cl']);
        }

        $parentViews = [];
        if (!empty($params["_parent"])) {
            $parentViews = explode("|", $params["_parent"]);
            unset($params["_parent"]);
        }

        return Registry::get(WidgetControl::class)
            ->buildIncludedWidgetResponse($class, $params, $parentViews)
            ->getContent();
    }
}
