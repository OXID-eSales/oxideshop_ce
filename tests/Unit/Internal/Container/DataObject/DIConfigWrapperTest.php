<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Container\DataObject;

use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\DataObject\DIConfigWrapper;
use PHPUnit\Framework\TestCase;

final class DIConfigWrapperTest extends TestCase
{
    private string $servicePath1;
    private string $servicePath2;

    public function setup(): void
    {
        $this->servicePath1 = __DIR__ . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'TestModule1' . DIRECTORY_SEPARATOR .
            'services.yaml';
        $this->servicePath2 = __DIR__ . DIRECTORY_SEPARATOR .
            '..' . DIRECTORY_SEPARATOR .
            'TestModule2' . DIRECTORY_SEPARATOR .
            'services.yaml';
    }

    public function testCleaningSections(): void
    {
        $projectYaml = new DIConfigWrapper(['imports' => []]);
        // These empty sections should be cleaned away
        $this->assertCount(0, $projectYaml->getConfigAsArray());
    }

    public function testGetAllImportFileNames(): void
    {
        $configArray = ['imports' => [
            ['resource' => $this->servicePath1],
            ['resource' => $this->servicePath2]
        ]];

        $wrapper = new DIConfigWrapper($configArray);
        $names = $wrapper->getImportFileNames();

        $this->assertEquals($this->servicePath1, $names[0]);
        $this->assertEquals($this->servicePath2, $names[1]);
    }

    public function testAddImport(): void
    {
        $configArray = ['imports' => [['resource' => $this->servicePath1]]];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->addImport($this->servicePath2);

        $this->assertCount(2, $wrapper->getConfigAsArray()['imports']);
    }

    public function testAddFirstImport(): void
    {
        $configArray = [];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->addImport($this->servicePath1);

        $expected = ['imports' => [['resource' => $this->servicePath1]]];
        $this->assertEquals($expected, $wrapper->getConfigAsArray());
    }

    public function testRemoveImport(): void
    {
        $configArray = ['imports' => [
            ['resource' => $this->servicePath1],
            ['resource' => $this->servicePath2]
        ]];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->removeImport($this->servicePath1);

        $this->assertCount(1, $wrapper->getConfigAsArray()['imports']);
    }

    public function testRemoveLastImport(): void
    {
        $configArray = ['imports' => [['resource' => $this->servicePath1]]];

        $wrapper = new DIConfigWrapper($configArray);
        $wrapper->removeImport($this->servicePath1);

        $this->assertEquals([], $wrapper->getConfigAsArray());
    }
}
