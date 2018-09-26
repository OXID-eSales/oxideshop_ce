<?php

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\OxaddparamsLogic;
use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\OxgetseourlLogic;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class OxidExtension
 */
class OxidExtension extends AbstractExtension
{
    /** @var OxgetseourlLogic */
    private $oxgetseourlLogic;

    /** @var OxaddparamsLogic */
    private $oxaddparamsLogic;

    /**
     * OxidExtension constructor.
     *
     * @param OxgetseourlLogic $oxgetseourlLogic
     * @param OxaddparamsLogic $oxaddparamsLogic
     */
    public function __construct(OxgetseourlLogic $oxgetseourlLogic, OxaddparamsLogic $oxaddparamsLogic)
    {
        $this->oxgetseourlLogic = $oxgetseourlLogic;
        $this->oxaddparamsLogic = $oxaddparamsLogic;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('oxgetseourl', [$this, 'oxgetseourl'], ['is_safe' => ['html']])
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters()
    {
        return [
            new TwigFilter('oxaddparams', [$this, 'oxaddparams'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Output SEO style url
     *
     * @param array $parameters
     *
     * @return null|string
     */
    public function oxgetseourl(array $parameters)
    {
        $url = $this->oxgetseourlLogic->oxgetseourl($parameters);

        $dynamicParameters = isset($parameters['params']) ? $parameters['params'] : false;
        if ($dynamicParameters) {
            $url = $this->oxaddparams($url, $dynamicParameters);
        }

        return $url;
    }

    /**
     * Add additional parameters to SEO url
     *
     * @param string $url               Url
     * @param string $dynamicParameters Dynamic URL parameters
     *
     * @return string
     */
    public function oxaddparams($url, $dynamicParameters)
    {
        return $this->oxaddparamsLogic->oxaddparams($url, $dynamicParameters);
    }
}