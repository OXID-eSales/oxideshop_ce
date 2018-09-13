<?php

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Extension;

use PHPUnit\Framework\TestCase;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Loader\ArrayLoader;

abstract class AbstractExtensionTest extends TestCase
{
    /** @var AbstractExtension */
    protected $extension;

    /**
     * @param string $template
     *
     * @return \Twig_Template
     */
    protected function getTemplate($template)
    {
        $loader = new ArrayLoader(['index' => $template]);

        $twig = new Environment($loader, ['debug' => true, 'cache' => false]);
        $twig->addExtension($this->extension);

        return $twig->loadTemplate('index');
    }
}
