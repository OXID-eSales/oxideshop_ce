<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Iterator;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\DataObject\ParameterInterface;

/**
 * Class EventModuleParameters
 * @package OxidEsales\EshopCommunity\Internal\Framework\Module\Setup\Iterator
 */
class EventModuleParameters implements EventModuleParametersInterface
{
    /**
     * @var ParameterInterface[]
     */
    private $parameterInterfaces = [];

    /**
     * @return array
     */
    public function getParameters()
    {
        $parameters = array_map(
            function ($parameterFunctionInterface) {
                return $parameterFunctionInterface->getParameter();
            },
            $this->parameterInterfaces,
        );

        return $parameters;
    }

    /**
     * @param ParameterInterface $parameter
     */
    public function addParameter(ParameterInterface $parameter)
    {
        $this->parameterInterfaces[] = $parameter;
    }
}
