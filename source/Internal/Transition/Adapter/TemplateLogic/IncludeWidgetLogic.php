<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Core\WidgetControl;

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

        $parentViews = null;
        if (!empty($params["_parent"])) {
            $parentViews = explode("|", $params["_parent"]);
            unset($params["_parent"]);
        }

        /**
         * @var WidgetControl $widgetControl
         */
        $widgetControl = \OxidEsales\Eshop\Core\Registry::get(\OxidEsales\Eshop\Core\WidgetControl::class);

        return $widgetControl->start($class, null, $params, $parentViews);
    }
}
