<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Routing;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\EshopCommunity\Core\Registry;

class ControllerIdToClassResolvingTest extends UnitTestCase
{
    /**
     * Test controller classid to class mapping.
     */
    public function testIdToClassMapping()
    {
        $classId = 'start';
        $resolvedClass = Registry::getControllerClassNameResolver()->getClassNameById($classId);
        $this->assertEquals(\OxidEsales\Eshop\Application\Controller\StartController::class, $resolvedClass);
    }

    /**
     * Test controller class to classId mapping.
     */
    public function testClassToIdMapping()
    {
        $class = \OxidEsales\Eshop\Application\Controller\StartController::class;
        $this->assertEquals('start', Registry::getControllerClassNameResolver()->getIdByClassName($class));
    }

    /**
     * Test controller class to classId mapping.
     */
    public function testClassToIdMappingNotExistingClass()
    {
        $class = 'classDoesNotExist';
        $this->assertEquals(null, Registry::getControllerClassNameResolver()->getClassNameById($class));
    }
}
