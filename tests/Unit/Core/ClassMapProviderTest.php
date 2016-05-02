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
namespace Unit\Core;

use OxidEsales\Eshop\Core\ClassMapProvider;
use OxidEsales\Eshop\Core\Edition\EditionSelector;
use PHPUnit_Framework_MockObject_MockObject as MockObject;

class ClassMapProviderTest extends \OxidTestCase
{
    /**
     * @return array
     */
    public function providerGetsNotOverridableClassMap()
    {
        $classMapProfessional = array(
            'classprofessional' => '\class\which\is\under\PE'
        );
        $classMapEnterprise = array(
            'classenterprise' => '\class\which\is\under\EE'
        );
        $resultProfessional = $classMapProfessional;
        $resultEnterprise = array(
            'classprofessional' => '\class\which\is\under\PE',
            'classenterprise' => '\class\which\is\under\EE'
        );
        $resultCommunity = array();

        return array(
            array($classMapProfessional, $classMapEnterprise, EditionSelector::PROFESSIONAL, $resultProfessional),
            array($classMapProfessional, $classMapEnterprise, EditionSelector::ENTERPRISE, $resultEnterprise),
            array(null, null, EditionSelector::COMMUNITY, $resultCommunity),
        );
    }

    /**
     * @param array  $mapNotOverridableProfessional
     * @param array  $mapNotOverridableEnterprise
     * @param string $edition
     * @param array  $result
     *
     * @dataProvider providerGetsNotOverridableClassMap
     */
    public function testGetsNotOverridableClassMap(
        $mapNotOverridableProfessional,
        $mapNotOverridableEnterprise,
        $edition,
        $result
    ) {
        /** @var EditionSelector|MockObject $editionSelector */
        $editionSelector = $this->getMockBuilder('\OxidEsales\Eshop\Core\Edition\EditionSelector')->getMock();
        $editionSelector->expects($this->atLeastOnce())->method('getEdition')->will($this->returnValue($edition));

        $classMapProfessional = $this->getMock('ProfessionalClassMap', array('getNotOverridableMap', 'getOverridableMap'));
        $classMapProfessional->expects($this->any())->method('getNotOverridableMap')->will($this->returnValue($mapNotOverridableProfessional));
        $classMapProfessional->expects($this->any())->method('getOverridableMap')->will($this->returnValue(array()));

        $classMapEnterprise = $this->getMock('EnterpriseClassMap', array('getNotOverridableMap', 'getOverridableMap'));
        $classMapEnterprise->expects($this->any())->method('getNotOverridableMap')->will($this->returnValue($mapNotOverridableEnterprise));
        $classMapEnterprise->expects($this->any())->method('getOverridableMap')->will($this->returnValue(array()));

        $classMapSelector = new ClassMapProvider($editionSelector);
        $classMapSelector->setClassMapProfessional($classMapProfessional);
        $classMapSelector->setClassMapEnterprise($classMapEnterprise);

        $this->assertSame($result, $classMapSelector->getNotOverridableClassMap());
    }

    /**
     * @param array  $mapOverridableProfessional
     * @param array  $mapOverridableEnterprise
     * @param string $edition
     * @param array  $result
     *
     * @dataProvider providerGetsNotOverridableClassMap
     */
    public function testGetsOverridableClassMap(
        $mapOverridableProfessional,
        $mapOverridableEnterprise,
        $edition,
        $result
    ) {
        /** @var EditionSelector|MockObject $editionSelector */
        $editionSelector = $this->getMockBuilder('\OxidEsales\Eshop\Core\Edition\EditionSelector')->getMock();
        $editionSelector->expects($this->atLeastOnce())->method('getEdition')->will($this->returnValue($edition));

        $classMapProfessional = $this->getMock('ProfessionalClassMap', array('getNotOverridableMap', 'getOverridableMap'));
        $classMapProfessional->expects($this->any())->method('getOverridableMap')->will($this->returnValue($mapOverridableProfessional));
        $classMapProfessional->expects($this->any())->method('getNotOverridableMap')->will($this->returnValue(array()));

        $classMapEnterprise = $this->getMock('EnterpriseClassMap', array('getNotOverridableMap', 'getOverridableMap'));
        $classMapEnterprise->expects($this->any())->method('getOverridableMap')->will($this->returnValue($mapOverridableEnterprise));
        $classMapEnterprise->expects($this->any())->method('getNotOverridableMap')->will($this->returnValue(array()));

        $classMapSelector = new ClassMapProvider($editionSelector);
        $classMapSelector->setClassMapProfessional($classMapProfessional);
        $classMapSelector->setClassMapEnterprise($classMapEnterprise);

        $this->assertSame($result, $classMapSelector->getOverridableClassMap());
    }
}
