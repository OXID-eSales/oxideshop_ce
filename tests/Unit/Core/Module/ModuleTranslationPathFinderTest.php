<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
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
