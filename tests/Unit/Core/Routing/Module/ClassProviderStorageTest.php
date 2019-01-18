<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Routing\Module;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Routing\Module\ClassProviderStorage;

/**
 * Test the module controller provider cache.
 *
 * @package Unit\Core\Routing\Module
 */
class ControllerProviderCacheTest extends UnitTestCase
{
    /**
     * Standard setup method, called before every method.
     *
     * Calls parent method first.
     */
    protected function setUp()
    {
        parent::setUp();

        $controllerProviderCache = oxNew(ClassProviderStorage::class);
        $controllerProviderCache->set(null);
    }

    /**
     * Test, that the creation works properly.
     *
     * @return ClassProviderStorage A fresh controller provider cache.
     */
    public function testCreation()
    {
        $controllerProviderCache = oxNew(ClassProviderStorage::class);

        $this->assertTrue(is_a($controllerProviderCache, ClassProviderStorage::class));

        return $controllerProviderCache;
    }

    /**
     * Test, that the cache leads to null, if we don't set anything before.
     */
    public function testGetWithoutSetValueBefore()
    {
        $cache = $this->testCreation();

        $result = $cache->get();

        $this->assertEmpty($result);

        return $cache;
    }

    /**
     * Test, that the cache leads to the before set value.
     */
    public function testSetLowercasesModuleIdAndControllerKey()
    {
        $value = [
            'ModuleA' => [
                'MyControllerKeyOne' => '\MyNamespace\MyClassOne',
                'MyControllerKeyTwo' => '\MyNamespace\MyClassTwo'
            ],
            'ModuleB' => [
                'MyControllerKeyThree' => '\MyNamespace\MyClassThree',
                'MyControllerKeyFour' => '\MyNamespace\MyClassFour'
            ]
        ];
        $expectedValue = [
            'modulea' => [
                'mycontrollerkeyone' => '\MyNamespace\MyClassOne',
                'mycontrollerkeytwo' => '\MyNamespace\MyClassTwo'
            ],
            'moduleb' => [
                'mycontrollerkeythree' => '\MyNamespace\MyClassThree',
                'mycontrollerkeyfour' => '\MyNamespace\MyClassFour'
            ]
        ];

        $cache = $this->testCreation();
        $cache->set($value);

        $this->assertEquals($cache->get(), $expectedValue);

        return $cache;
    }
}
