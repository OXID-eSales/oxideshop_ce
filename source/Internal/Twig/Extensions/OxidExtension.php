<?php

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\OxgetseourlLogic;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class OxidExtension
 */
class OxidExtension extends AbstractExtension
{
    /** @var OxgetseourlLogic */
    private $oxgetseourlLogic;

    /**
     * OxidExtension constructor.
     *
     * @param OxgetseourlLogic $oxgetseourlLogic
     */
    public function __construct(OxgetseourlLogic $oxgetseourlLogic)
    {
        $this->oxgetseourlLogic = $oxgetseourlLogic;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('oxgetseourl', [$this->oxgetseourlLogic, 'oxgetseourl'])
        ];
    }
}