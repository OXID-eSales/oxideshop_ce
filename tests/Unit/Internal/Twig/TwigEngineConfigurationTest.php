<?php
/**
 * Created by PhpStorm.
 * User: jskoczek
 * Date: 22/08/18
 * Time: 15:25
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig;

use OxidEsales\EshopCommunity\Internal\Twig\TwigEngineConfiguration;
use OxidEsales\EshopCommunity\Internal\Twig\TwigContextInterface;

class TwigEngineConfigurationTest extends \PHPUnit_Framework_TestCase
{

    public function testGetParameters()
    {
        $engineConfiguration = $this->getEngineConfiguration();
        $this->assertEquals(['debug' => 'dummy_is_debug', 'cache' => 'dummy_cache_dir'], $engineConfiguration->getParameters());
        $this->assertNotEquals(['debug' => 'foo', 'cache' => 'foo'], $engineConfiguration->getParameters());
    }

    private function getEngineConfiguration()
    {
        /** @var TwigContextInterface $context */
        $context = $this->getMockBuilder('OxidEsales\EshopCommunity\Internal\Twig\TwigContextInterface')->getMock();
        $context->method('getIsDebug')->willReturn('dummy_is_debug');
        $context->method('getCacheDir')->willReturn('dummy_cache_dir');
        return new TwigEngineConfiguration($context);
    }
}
