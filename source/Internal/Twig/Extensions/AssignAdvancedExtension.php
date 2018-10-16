<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\AssignAdvancedLogic;
use OxidEsales\EshopCommunity\Internal\Application\ContainerFactory;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class AssignAdvancedExtension
 *
 * @package OxidEsales\EshopCommunity\Internal\Twig\Extensions
 */
class AssignAdvancedExtension extends AbstractExtension
{

    /**
     * @var AssignAdvancedLogic
     */
    private $assignAdvancedLogic;

    /**
     * AssignAdvancedExtension constructor.
     *
     * @param AssignAdvancedLogic $assignAdvancedLogic
     */
    public function __construct(AssignAdvancedLogic $assignAdvancedLogic)
    {
        $this->assignAdvancedLogic = $assignAdvancedLogic;
    }

    /**
     * @return array|\Twig_Function[]
     */
    public function getFunctions()
    {
        return [new TwigFunction('assignAdvanced', [$this, 'assignAdvanced'])];
    }

    /**
     * Calles formatValue function to format arrays and range()
     *
     * @param string $value
     *
     * @return mixed
     */
    public function assignAdvanced($value)
    {
        /** @var AssignAdvancedLogic $oxgetseourlLogic */
        $assignAdvancedLogic = ContainerFactory::getInstance()->getContainer()->get(AssignAdvancedLogic::class);
        $formattedValue = $assignAdvancedLogic->formatValue($value);

        return $formattedValue;
    }
}
