<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic;

class StyleLogic
{
    /**
     * @param array $params
     * @param bool  $isDynamic
     *
     * @return string
     */
    public function collectStyleSheets($params, $isDynamic)
    {
        $params = $this->fillDefaultParams($params);
        $output = $this->getOutput($params, $isDynamic);

        return $output;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    private function fillDefaultParams($params)
    {
        $defaults = [
            'widget'   => '',
            'inWidget' => false,
            'if'       => null,
            'include'  => null,
        ];
        $params = array_merge($defaults, $params);

        return $params;
    }

    /**
     * @param array $params
     * @param bool  $isDynamic
     *
     * @return string
     */
    private function getOutput($params, $isDynamic)
    {
        $output = '';
        $widget = $params['widget'];
        $forceRender = $params['inWidget'];
        if (!empty($params['include'])) {
            $registrator = oxNew(\OxidEsales\Eshop\Core\ViewHelper\StyleRegistrator::class);
            $registrator->addFile($params['include'], $params['if'], $isDynamic);
        } else {
            $renderer = oxNew(\OxidEsales\Eshop\Core\ViewHelper\StyleRenderer::class);
            $output = $renderer->render($widget, $forceRender, $isDynamic);
        }

        return $output;
    }
}
