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

use OxidEsales\EshopCommunity\Core\ClassMap;
use OxidEsales\EshopCommunity\Core\ClassMapProvider;
use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;

class ClassMapProviderTest extends \OxidTestCase
{
    /**
     * @return array
     */
    public function providerGetsNotOverridableClassMap()
    {
        $classMapCommunity = array(
            'classcommunity' => '\class\which\is\under\CE'
        );
        $classMapProfessional = array(
            'classprofessional' => '\class\which\is\under\PE'
        );
        $classMapEnterprise = array(
            'classenterprise' => '\class\which\is\under\EE'
        );
        $resultCommunity = $classMapCommunity;
        $resultProfessional = array(
            'classcommunity' => '\class\which\is\under\CE',
            'classprofessional' => '\class\which\is\under\PE'
        );
        $resultEnterprise = array(
            'classcommunity' => '\class\which\is\under\CE',
            'classprofessional' => '\class\which\is\under\PE',
            'classenterprise' => '\class\which\is\under\EE'
        );

        return array(
            array($classMapCommunity, $classMapProfessional, $classMapEnterprise, EditionSelector::COMMUNITY, $resultCommunity),
            array($classMapCommunity, $classMapProfessional, $classMapEnterprise, EditionSelector::PROFESSIONAL, $resultProfessional),
            array($classMapCommunity, $classMapProfessional, $classMapEnterprise, EditionSelector::ENTERPRISE, $resultEnterprise),
            array($classMapCommunity, null, null, EditionSelector::COMMUNITY, $resultCommunity),
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
        $mapNotOverridableCommunity,
        $mapNotOverridableProfessional,
        $mapNotOverridableEnterprise,
        $edition,
        $result
    ) {
        $editionSelector = $this->getEditionSelectorStub($edition);

        $classMapCommunity = $this->getClassMapStub([], $mapNotOverridableCommunity);
        $classMapProfessional = $this->getClassMapStub([], $mapNotOverridableProfessional);
        $classMapEnterprise = $this->getClassMapStub([], $mapNotOverridableEnterprise);

        $classMapSelector = new ClassMapProvider($editionSelector);
        $classMapSelector->setClassMapCommunity($classMapCommunity);
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
        $mapOverridableCommunity,
        $mapOverridableProfessional,
        $mapOverridableEnterprise,
        $edition,
        $result
    ) {
        $editionSelector = $this->getEditionSelectorStub($edition);

        $classMapCommunity = $this->getClassMapStub($mapOverridableCommunity, []);
        $classMapProfessional = $this->getClassMapStub($mapOverridableProfessional, []);
        $classMapEnterprise = $this->getClassMapStub($mapOverridableEnterprise, []);

        $classMapSelector = new ClassMapProvider($editionSelector);
        $classMapSelector->setClassMapCommunity($classMapCommunity);
        $classMapSelector->setClassMapProfessional($classMapProfessional);
        $classMapSelector->setClassMapEnterprise($classMapEnterprise);

        $this->assertSame($result, $classMapSelector->getOverridableClassMap());
    }

    protected function getClassMapStub($mapOverridable, $mapNotOverridable)
    {
        $classMapStub = $this->getMock(ClassMap::class);
        $classMapStub->method('getOverridableMap')->willReturn($mapOverridable);
        $classMapStub->method('getNotOverridableMap')->willReturn($mapNotOverridable);

        return $classMapStub;
    }

    protected function getEditionSelectorStub($edition)
    {
        $editionSelector = $this->getMock(EditionSelector::class);
        $editionSelector->method('getEdition')->willreturn($edition);

        return $editionSelector;
    }
}
