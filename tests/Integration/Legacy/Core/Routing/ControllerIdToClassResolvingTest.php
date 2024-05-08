<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Routing;

use OxidEsales\Eshop\Application\Controller\StartController;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class ControllerIdToClassResolvingTest extends IntegrationTestCase
{
    /**
     * Test controller classid to class mapping.
     */
    public function testIdToClassMapping(): void
    {
        $classId = 'start';
        $resolvedClass = Registry::getControllerClassNameResolver()->getClassNameById($classId);
        $this->assertEquals(StartController::class, $resolvedClass);
    }

    /**
     * Test controller class to classId mapping.
     */
    public function testClassToIdMapping(): void
    {
        $class = StartController::class;
        $this->assertEquals('start', Registry::getControllerClassNameResolver()->getIdByClassName($class));
    }

    /**
     * Test controller class to classId mapping.
     */
    public function testClassToIdMappingNotExistingClass(): void
    {
        $class = 'classDoesNotExist';
        $this->assertEquals(null, Registry::getControllerClassNameResolver()->getClassNameById($class));
    }
}
