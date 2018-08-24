<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 *
 * @author Jędrzej Skoczek & Tomasz Kowalewski
 */

namespace OxidEsales\EshopCommunity\Internal\Twig;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\UtilsView;

/**
 * Class TwigContext
 * @package OxidEsales\EshopCommunity\Internal\Twig
 */
class TwigContext implements TwigContextInterface
{
    /**
     * @var Config
     */
    private $config;

    /**
     * @var UtilsView
     */
    private $utilsView;

    /**
     * Context constructor.
     *
     * @param Config    $config
     * @param UtilsView $utilsView
     */
    public function __construct(Config $config, UtilsView $utilsView)
    {
        $this->config = $config;
        $this->utilsView = $utilsView;
    }

    /**
     * @return array
     */
    public function getTemplateDirectories()
    {
        $templateDirectory = $this->utilsView->getTemplateDirs();
        $directories = array_filter($templateDirectory, function ($directory) {
            return is_dir($directory);
        });
        return $directories;
    }

    /**
     * @return bool
     */
    public function getIsDebug()
    {
        return $this->config->getConfigParam('iDebug', false);
    }

    /**
     * @return string
     */
    public function getCacheDir()
    {
        return $this->config->getConfigParam('sCompileDir') . '/twig';
    }
}