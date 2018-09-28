<?php

namespace OxidEsales\EshopCommunity\Internal\Twig\Extensions;

use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Class MathExtension
 */
class MathExtension extends AbstractExtension
{

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return TwigFunction[]
     */
    public function getFunctions()
    {
        return [
            new TwigFunction('cos', 'cos'),
            new TwigFunction('sin', 'sin'),
            new TwigFunction('tan', 'tan'),
            new TwigFunction('exp', 'exp'),
            new TwigFunction('log', 'log'),
            new TwigFunction('log10', 'log10'),
            new TwigFunction('pi', 'pi'),
            new TwigFunction('sqrt', 'sqrt'),
        ];
    }
}
