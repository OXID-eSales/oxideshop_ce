<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
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
