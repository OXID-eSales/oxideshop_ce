<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\DIContainer\Dao;

use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Framework\DIContainer\Dao\ParameterDaoInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\FilesystemTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\Exception\ParameterNotFoundException;

final class ParameterDaoTest extends TestCase
{
    use ContainerTrait;
    use FilesystemTrait;

    public function setUp(): void
    {
        parent::setUp();

        $this->backupVarDirectory();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->restoreVarDirectory();
    }

    public function testAddWithMultipleParameters(): void
    {
        $dao = $this->get(ParameterDaoInterface::class);

        $dao->add('param_1', 'som-val-1', 1);
        $dao->add('param_2', 'some-val-2', 1);

        $this->assertTrue($dao->has('param_1', 1));
        $this->assertTrue($dao->has('param_2', 1));
    }

    public function testAddWithExistingParameterWillOverwriteValue(): void
    {
        $dao = ContainerFacade::get(ParameterDaoInterface::class);

        $value1 = uniqid('val-', true);
        $value2 = uniqid('val-', true);

        $dao->add('param_1', $value1, 1);
        $dao->add('param_1', $value2, 1);

        $this->assertEquals($value2, ContainerFacade::getParameter('param_1'));
    }

    public function testAddParameterWithNulValue(): void
    {
        $dao = $this->get(ParameterDaoInterface::class);
        $dao->add('some_null_param', null, 1);

        $this->assertTrue($dao->has('some_null_param', 1));
    }

    public function testRemoveWithMultipleParameters(): void
    {
        $dao = $this->get(ParameterDaoInterface::class);
        $dao->add('param_1', 'som-val-1', 1);
        $dao->add('param_2', 'some-val-2', 1);

        $dao->remove('param_1', 1);

        $this->assertFalse($dao->has('param_1', 1));
        $this->assertTrue($dao->has('param_2', 1));
    }

    public function testParameterAccessFromContainer(): void
    {
        $parameterName = 'test' . time();
        $dao = ContainerFacade::get(ParameterDaoInterface::class);
        $dao->add($parameterName, 'value', 1);

        $this->assertEquals(
            'value',
            ContainerFacade::getParameter($parameterName)
        );

        $dao->remove($parameterName, 1);

        $this->expectException(ParameterNotFoundException::class);

        ContainerFacade::getParameter($parameterName);
    }

    public function testAddParameterAsEnvVar(): void
    {
        $dao = ContainerFacade::get(ParameterDaoInterface::class);
        $dao->add('testEnvVar', '%env(OXID_TEST_ENV_VAR)%', 1);

        putenv('OXID_TEST_ENV_VAR=testValue');

        $this->assertEquals(
            'testValue',
            ContainerFacade::getParameter('testEnvVar')
        );
    }

    public function testAddParameterToMultipleShops(): void
    {
        $dao = $this->get(ParameterDaoInterface::class);
        $dao->add('testShop1', 'value', 1);
        $dao->add('testShop2', 'value', 2);

        $this->assertTrue($dao->has('testShop1', 1));
        $this->assertTrue($dao->has('testShop2', 2));

        $this->assertFalse($dao->has('testShop1', 2));
        $this->assertFalse($dao->has('testShop2', 1));
    }
}
