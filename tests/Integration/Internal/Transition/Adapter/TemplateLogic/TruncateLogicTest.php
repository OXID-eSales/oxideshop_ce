<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Transition\Adapter\TemplateLogic;

use PHPUnit\Framework\Attributes\DataProvider;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\TemplateLogic\TruncateLogic;
use PHPUnit\Framework\TestCase;

final class TruncateLogicTest extends TestCase
{
    private TruncateLogic $truncateLogic;

    public function setup(): void
    {
        $this->truncateLogic = new TruncateLogic();
    }

    #[DataProvider('truncateProvider')]
    public function testTruncate(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    public static function truncateProvider(): array
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

    #[DataProvider('truncateProviderWithLength')]
    public function testTruncateWithLength(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    public static function truncateProviderWithLength(): array
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

    #[DataProvider('truncateProviderWithSuffix')]
    public function testTruncateWithSuffix(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    public static function truncateProviderWithSuffix(): array
    {
        return [
            [
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse dapibus pulvinar sem vitae.",
                "Duis iaculis pellentesque felis, et pulvinar elit lacinia at. Suspendisse (...)",
                ['suffix' => ' (...)']
            ],
        ];
    }

    #[DataProvider('truncateProviderWithBreakWords')]
    public function testTruncateWithBreakWords(string $string, string $expected, array $parameters = []): void
    {
        $length = isset($parameters['length']) ? $parameters['length'] : 80;
        $suffix = isset($parameters['suffix']) ? $parameters['suffix'] : '...';
        $breakWords = isset($parameters['breakWords']) ? $parameters['breakWords'] : false;

        $this->assertEquals($expected, $this->truncateLogic->truncate($string, $length, $suffix, $breakWords));
    }

    public static function truncateProviderWithBreakWords(): array
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
