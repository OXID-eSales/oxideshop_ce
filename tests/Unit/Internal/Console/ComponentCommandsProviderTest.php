<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Console;

use Composer\Package\PackageInterface;
use Composer\Repository\WritableRepositoryInterface;
use OxidEsales\EshopCommunity\Internal\Console\CommandsProvider\ComponentCommandsProvider;
use PHPUnit\Framework\TestCase;

class ComponentCommandsProviderTest extends TestCase
{
    public function testGetClassesWhenProvided()
    {
        $extraForLocalRepository = [
            'oxideshop' => [
                'console-commands' => [\StdClass::class]
            ]
        ];
        $localRepositoryStub = $this->getLocalRepositoryStub($extraForLocalRepository);

        $classesProvider = new ComponentCommandsProvider($localRepositoryStub);

        $this->assertArraySubset([new \StdClass()], $classesProvider->getCommands());
    }

    public function testGetClassesWhenCommandsEmpty()
    {
        $extraForLocalRepository = [
            'oxideshop' => [
                'console-commands' => null
            ]
        ];
        $localRepositoryStub = $this->getLocalRepositoryStub($extraForLocalRepository);

        $classesProvider = new ComponentCommandsProvider($localRepositoryStub);

        $this->assertSame([], $classesProvider->getCommands());
    }

    public function testGetClassesWhenConsoleCommandsNotProvided()
    {
        $extraForLocalRepository = [
            'oxideshop' => []
        ];
        $localRepositoryStub = $this->getLocalRepositoryStub($extraForLocalRepository);

        $classesProvider = new ComponentCommandsProvider($localRepositoryStub);

        $this->assertSame([], $classesProvider->getCommands());
    }

    public function testGetClassesWhenExtrasNotProvided()
    {
        $extraForLocalRepository = [];
        $localRepositoryStub = $this->getLocalRepositoryStub($extraForLocalRepository);

        $classesProvider = new ComponentCommandsProvider($localRepositoryStub);

        $this->assertSame([], $classesProvider->getCommands());
    }

    public function testGetClassesWhenCommandsNotArray()
    {
        $extraForLocalRepository = [
            'oxideshop' => [
                'console-commands' => 'any_class'
            ]
        ];
        $localRepositoryStub = $this->getLocalRepositoryStub($extraForLocalRepository);

        $classesProvider = new ComponentCommandsProvider($localRepositoryStub);

        $this->assertArraySubset([], $classesProvider->getCommands());
    }

    /**
     * @param array $extra
     *
     * @return WritableRepositoryInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private function getLocalRepositoryStub(array $extra)
    {
        $packageStub = $this->getMockBuilder(PackageInterface::class)->getMock();
        $packageStub->method('getExtra')->willReturn($extra);
        $localRepositoryStub = $this->getMockBuilder(WritableRepositoryInterface::class)->getMock();
        $packages = [
            $packageStub
        ];
        $localRepositoryStub->method('getPackages')->willReturn($packages);

        return $localRepositoryStub;
    }
}
