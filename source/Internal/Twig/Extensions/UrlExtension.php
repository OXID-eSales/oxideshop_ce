<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\AddUrlParametersLogic;
use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\SeoUrlLogic;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

/**
 * Class UrlExtension
 */
class UrlExtension extends AbstractExtension
{

    /** @var SeoUrlLogic */
    private $seoUrlLogic;

    /** @var AddUrlParametersLogic */
    private $addUrlParametersLogic;

    /**
     * OxidExtension constructor.
     *
     * @param SeoUrlLogic           $seoUrlLogic
     * @param AddUrlParametersLogic $addUrlParametersLogic
     */
    public function __construct(SeoUrlLogic $seoUrlLogic, AddUrlParametersLogic $addUrlParametersLogic)
    {
        $this->seoUrlLogic = $seoUrlLogic;
        $this->addUrlParametersLogic = $addUrlParametersLogic;
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('seo_url', [$this, 'getSeoUrl'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Returns a list of filters to add to the existing list.
     *
     * @return TwigFilter[]
     */
    public function getFilters()
    {
        return [
            new TwigFilter('add_url_parameters', [$this, 'addUrlParameters'], ['is_safe' => ['html']])
        ];
    }

    /**
     * Output SEO style url
     *
     * @param array $parameters
     *
     * @return null|string
     */
    public function getSeoUrl(array $parameters)
    {
        $url = $this->seoUrlLogic->seoUrl($parameters);

        $dynamicParameters = isset($parameters['params']) ? $parameters['params'] : false;
        if ($dynamicParameters) {
            $url = $this->addUrlParameters($url, $dynamicParameters);
        }

        return $url;
    }

    /**
     * Add additional parameters to url
     *
     * @param string $url               Url
     * @param string $dynamicParameters Dynamic URL parameters
     *
     * @return string
     */
    public function addUrlParameters($url, $dynamicParameters)
    {
        return $this->addUrlParametersLogic->addUrlParameters($url, $dynamicParameters);
    }
}
