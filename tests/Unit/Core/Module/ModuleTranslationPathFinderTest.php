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
namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Module;

/**
 * Test the translation path finder.
 *
 * @group module
 * @package Unit\Core
 */
class ModuleTranslationPathFinderTest extends \OxidTestCase
{

    /**
     * Data provider for the test of the method findTranslationPath.
     *
     * @return array The test cases.
     */
    public function dataProvider_testFindTranslationPath()
    {
        return array(
            array(
                'language' => 'de',
                'admin' => false,
                'expectedFullPath' => 'MODULES/welcome_home/translations/de'
            ),
            array(
                'language' => 'en',
                'admin' => false,
                'expectedFullPath' => 'MODULES/welcome_home/translations/en'
            ),
            array(
                'language' => 'de',
                'admin' => true,
                'expectedFullPath' => 'MODULES/welcome_home/views/admin/de'
            )
        );
    }

    /**
     * Test, that the method findTranslationPath works as expected.
     *
     * @dataProvider dataProvider_testFindTranslationPath
     */
    public function testFindTranslationPath($language, $admin, $expectedFullPath)
    {
        $mockedClassName = \OxidEsales\Eshop\Core\Module\ModuleTranslationPathFinder::class;
        $pathFinderMock = $this->getMock($mockedClassName, array('getModulesDirectory'));

        $pathFinderMock->expects($this->once())->method('getModulesDirectory')->willReturn('MODULES/');

        $fullPath = $pathFinderMock->findTranslationPath($language, $admin, 'welcome_home');

        $this->assertEquals($expectedFullPath, $fullPath);
    }

    /**
     * Data provider for the test of the method findTranslationPath.
     *
     * @return array The test cases.
     */
    public function dataProvider_testBothCaseApplicationFolders()
    {
        return array(
            array(
                'hasUpper' => true,
                'hasLower' => false,
                'expectedFullPath' => 'MODULES/welcome_home/Application/translations/de'
            ),
            array(
                'hasUpper' => false,
                'hasLower' => true,
                'expectedFullPath' => 'MODULES/welcome_home/application/translations/de'
            )
        );
    }

    /**
     * Test, that the application/Application folder with the translation work.
     *
     * @dataProvider dataProvider_testBothCaseApplicationFolders
     *
     * @param bool   $hasUpper         Exists an 'Application' folder?
     * @param bool   $hasLower         Exists an 'application' folder?
     * @param string $expectedFullPath The path we expect to get with the given preconditions.
     */
    public function testBothCaseApplicationFolders($hasUpper, $hasLower, $expectedFullPath)
    {
        $mockedClassName = \OxidEsales\Eshop\Core\Module\ModuleTranslationPathFinder::class;
        $pathFinderMock = $this->getMock($mockedClassName, array('getModulesDirectory', 'hasUppercaseApplicationDirectory', 'hasLowercaseApplicationDirectory'));

        $pathFinderMock->expects($this->once())->method('getModulesDirectory')->willReturn('MODULES/');
        $pathFinderMock->expects($this->any())->method('hasUppercaseApplicationDirectory')->willReturn($hasUpper);
        $pathFinderMock->expects($this->any())->method('hasLowercaseApplicationDirectory')->willReturn($hasLower);

        $fullPath = $pathFinderMock->findTranslationPath('de', false, 'welcome_home');

        $this->assertEquals($expectedFullPath, $fullPath);
    }
}
