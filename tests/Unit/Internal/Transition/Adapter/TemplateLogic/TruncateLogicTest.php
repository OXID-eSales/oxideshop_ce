<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Transition\Adapter\TemplateLogic;

use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TruncateLogic;
use PHPUnit\Framework\TestCase;

/**
 * Class TruncateLogicTest
 */
class TruncateLogicTest extends TestCase
{

    /** @var TruncateLogic */
    private $truncateLogic;

    public function setUp(): void
    {
        $this->truncateLogic = new TruncateLogic();
    }

    /**
     * @param string $string
     * @param string $expected
     * @param array  $parameters
     *
     * @dataProvider truncateProvider
     */
    public function testTruncate(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    /**
     * @return array
     */
    public function truncateProvider(): array
    {
        return [
            [
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse..."
            ],
            [
                "Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. Suspendisse dapibus pulvinar sem vitae.",
                "Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. Suspendisse..."
            ],
            [
                "&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at. Suspendisse dapibus pulvinar sem vitae.",
                "&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit lacinia at...."
            ],
        ];
    }

    /**
     * @param string $string
     * @param string $expected
     * @param array  $parameters
     *
     * @dataProvider truncateProviderWithLength
     */
    public function testTruncateWithLength(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    /**
     * @return array
     */
    public function truncateProviderWithLength(): array
    {
        return [
            [
                "Duis iaculis pellentesque felis, et pulvinar elit.",
                "Duis iaculis...",
                ['length' => 20]
            ],
            [
                "Duis iaculis &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit.",
                "Duis iaculis...",
                ['length' => 20]
            ],
            [
                "&#039;Duis&#039; &#039;iaculis&#039; &#039;pellentesque&#039; felis, et &quot;pulvinar&quot; elit.",
                "&#039;Duis&#039; &#039;iaculis&#039;...",
                ['length' => 20]
            ],
        ];
    }

    /**
     * @param string $string
     * @param string $expected
     * @param array  $parameters
     *
     * @dataProvider truncateProviderWithSuffix
     */
    public function testTruncateWithSuffix(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    /**
     * @return array
     */
    public function truncateProviderWithSuffix(): array
    {
        return [
            [
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse (...)",
                ['suffix' => ' (...)']
            ],
        ];
    }

    /**
     * @param string $string
     * @param string $expected
     * @param array  $parameters
     *
     * @dataProvider truncateProviderWithBreakWords
     */
    public function testTruncateWithBreakWords(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    /**
     * @return array
     */
    public function truncateProviderWithBreakWords(): array
    {
        return [
            [
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dap...",
                ['breakWords' => true]
            ],
        ];
    }
}
