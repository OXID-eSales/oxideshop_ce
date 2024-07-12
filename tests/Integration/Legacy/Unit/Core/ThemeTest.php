<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \oxTheme;
use \another;

class ThemeTest extends \PHPUnit\Framework\TestCase
{
    protected function setup(): void
    {
        parent::setUp();
    }

    public function testLoadAndGetInfo()
    {
        $oTheme = $this->getProxyClass('oxTheme');
        $this->assertTrue($oTheme->load('azure'));

        foreach (['id', 'title', 'description', 'thumbnail', 'version', 'author', 'active', 'settings'] as $key) {
            $this->assertNotNull($oTheme->getInfo($key));
        }

        $this->assertNull($oTheme->getInfo('asdasdasd'));
        $this->assertSame('azure', $oTheme->getInfo('id'));
    }

    public function testGetList()
    {
        $themeList = $this->getProxyClass('oxTheme')->getList();

        $this->assertGreaterThan(0, count($themeList));

        $this->assertContainsOnlyInstancesOf(\OxidEsales\EshopCommunity\Core\Theme::class, $themeList);
    }

    public function testActivateError()
    {
        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['checkForActivationErrors']);
        $oTheme->expects($this->once())->method('checkForActivationErrors')->willReturn('Error Message');
        $this->expectException(\OxidEsales\Eshop\Core\Exception\StandardException::class);
        $this->expectExceptionMessage('Error Message');
        $oTheme->activate();
    }

    public function testActivateMain()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['saveShopConfVar']);
        $oConfig->method('saveShopConfVar')->willReturn(null);

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['checkForActivationErrors', 'getConfig', 'getInfo']);
        $oTheme->method('checkForActivationErrors')->willReturn(false);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['parentTheme'], ['id'])
            ->willReturnOnConsecutiveCalls(
                '',
                'currentT'
            );

        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);
        $oTheme->activate();
    }

    public function testActivateChild()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['saveShopConfVar']);
        $oConfig->method('saveShopConfVar')->willReturn(null);

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['checkForActivationErrors', 'getConfig', 'getInfo']);
        $oTheme->method('checkForActivationErrors')->willReturn(false);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['parentTheme'], ['id'])
            ->willReturnOnConsecutiveCalls(
                'parentT',
                'currentT'
            );
        $oTheme->activate();
    }

    public function testGetActiveThemeIdCustom()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig->method('getConfigParam')
            ->with(
                'sCustomTheme'
            )
            ->willReturn('custom');
        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame('custom', $oTheme->getActiveThemeId());
    }

    public function testGetActiveThemeIdMain()
    {
        $oConfig = $this->getMock(\OxidEsales\Eshop\Core\Config::class, ['getConfigParam']);
        $oConfig
            ->method('getConfigParam')
            ->withConsecutive(['sCustomTheme'], ['sTheme'])
            ->willReturnOnConsecutiveCalls(
                '',
                'maint'
            );

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getConfig']);
        \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config::class, $oConfig);

        $this->assertSame('maint', $oTheme->getActiveThemeId());
    }


    public function testGetParentNull()
    {
        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oTheme->method('getInfo')->with('parentTheme')->willReturn('');
        $this->assertNull($oTheme->getParent());
    }

    public function testGetParent()
    {
        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oTheme->method('getInfo')->with('parentTheme')->willReturn('azure');
        $oParent = $oTheme->getParent();
        $this->assertInstanceOf(\OxidEsales\EshopCommunity\Core\Theme::class, $oParent);
        $this->assertSame('azure', $oParent->getInfo('id'));
    }

    public function testGetSettingsFromActivatedTheme()
    {
        $this->assertEquals(null, $this->getConfigParam('configParamFromThemeSettings'));

        $theme = $this->getProxyClass('oxTheme');
        $theme->setNonPublicVar("_aTheme", [
            'id'          => 'testTheme',
            'settings'    => [
                [
                    'group' => 'someGroup',
                    'name'  => 'configParamFromThemeSettings',
                    'type'  => 'str',
                    'value' => 'foobar',
                ],
            ],
        ]);

        $theme->activate();

        $this->assertSame('foobar', $this->getConfigParam('configParamFromThemeSettings'));
    }

    public function testOverrideShopSettings()
    {
        $this->setConfigParam('shopSetting', 'startValue');
        $this->assertSame('startValue', $this->getConfigParam('shopSetting'));

        $themeA = $this->getProxyClass('oxTheme');
        $themeA->setNonPublicVar("_aTheme", [
            'id'          => 'themeA',
            'settings'    => [
                [
                    'group' => 'someGroup',
                    'name'  => 'shopSetting',
                    'type'  => 'str',
                    'value' => 'finalValue',
                ],
            ],
        ]);
        $themeA->activate();

        $this->assertSame('finalValue', $this->getConfigParam('shopSetting'));
    }

    public function testDontOverrideAlreadyChangedSettings()
    {
        $this->assertEquals(null, $this->getConfigParam('configParamFromThemeSettings'));

        $themeA = $this->getProxyClass('oxTheme');
        $themeA->setNonPublicVar("_aTheme", [
            'id'          => 'themeA',
            'settings'    => [
                [
                    'group' => 'someGroup',
                    'name'  => 'configParamFromThemeSettings',
                    'type'  => 'str',
                    'value' => 'foobar',
                ],
            ],
        ]);
        $themeA->activate();

        $this->assertSame('themeA', $this->getConfigParam('sTheme'));
        $this->assertSame('foobar', $this->getConfigParam('configParamFromThemeSettings'));

        $themeA->setNonPublicVar("_aTheme", [
            'id'          => 'themeA',
            'settings'    => [
                [
                    'group' => 'someGroup',
                    'name'  => 'configParamFromThemeSettings',
                    'type'  => 'str',
                    'value' => 'otherValue',
                ],
            ],
        ]);
        $themeA->activate();

        $this->assertSame('foobar', $this->getConfigParam('configParamFromThemeSettings'));
    }

    public function testCheckForActivationErrorsNoParent()
    {
        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oTheme->method('getInfo')->with('id')->willReturn('');
        $this->assertSame('EXCEPTION_THEME_NOT_LOADED', $oTheme->checkForActivationErrors());


        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo', 'getParent']);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['id'], ['parentTheme'])
            ->willReturnOnConsecutiveCalls(
                'asd',
                'asd'
            );

        $this->assertSame('EXCEPTION_PARENT_THEME_NOT_FOUND', $oTheme->checkForActivationErrors());


        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo', 'getParent']);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['id'], ['parentTheme'])
            ->willReturnOnConsecutiveCalls(
                'asd',
                ''
            );

        $this->assertFalse($oTheme->checkForActivationErrors());
    }

    public function testCheckForActivationErrorsCheckParent()
    {
        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oParent->method('getInfo')->with('version')->willReturn('');

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo', 'getParent']);
        $oTheme->method('getInfo')->with('id')->willReturn('asd');
        $oTheme->method('getParent')->willReturn($oParent);
        $this->assertSame('EXCEPTION_PARENT_VERSION_UNSPECIFIED', $oTheme->checkForActivationErrors());

        ////
        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oParent->method('getInfo')->with('version')->willReturn('5');

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo', 'getParent']);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['id'], ['parentVersions'])
            ->willReturnOnConsecutiveCalls(
                'asd',
                ''
            );

        $oTheme->method('getParent')->willReturn($oParent);

        $this->assertSame('EXCEPTION_UNSPECIFIED_PARENT_VERSIONS', $oTheme->checkForActivationErrors());

        ////
        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oParent->method('getInfo')->with('version')->willReturn('5');

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo', 'getParent']);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['id'], ['parentVersions'])
            ->willReturnOnConsecutiveCalls(
                'asd',
                [1, 2]
            );

        $oTheme->method('getParent')->willReturn($oParent);

        $this->assertSame('EXCEPTION_PARENT_VERSION_MISMATCH', $oTheme->checkForActivationErrors());

        ////
        $oParent = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo']);
        $oParent->method('getInfo')->with('version')->willReturn('5');

        $oTheme = $this->getMock(\OxidEsales\Eshop\Core\Theme::class, ['getInfo', 'getParent']);
        $oTheme
            ->method('getInfo')
            ->withConsecutive(['id'], ['parentVersions'])
            ->willReturnOnConsecutiveCalls(
                'asd',
                [1, 2, 5]
            );

        $oTheme->method('getParent')->willReturn($oParent);

        $this->assertFalse($oTheme->checkForActivationErrors());
    }

    public function testGetId()
    {
        $oTheme = oxNew('oxTheme');
        $oTheme->load("azure");

        $this->assertSame('azure', $oTheme->getId());
    }

    /**
     * Test if getActiveThemeList gives correct list in simple case - one theme without extending
     */
    public function testGetActiveThemesListSimple()
    {
        $this->setConfigParam('sTheme', 'someTheme');

        $theme = oxNew('oxTheme');
        $this->assertSame(['someTheme'], $theme->getActiveThemesList());
    }

    /**
     * Test if getActiveThemeList gives correct list if there are a theme which extends another theme
     */
    public function testGetActiveThemesListExtended()
    {
        $this->setConfigParam('sTheme', 'someBasicTheme');
        $this->setConfigParam('sCustomTheme', 'someImprovedTheme');

        $theme = oxNew('oxTheme');
        $this->assertSame(['someBasicTheme', 'someImprovedTheme'], $theme->getActiveThemesList());
    }

    /**
     * Test if getActiveThemeList gives correct list if being in admin case
     */
    public function testGetActiveThemesListAdmin()
    {
        $this->setAdminMode(true);
        $this->setConfigParam('sTheme', 'someTheme');
        $this->setConfigParam('sCustomTheme', 'someCustomTheme');

        $theme = oxNew('oxTheme');
        $this->assertSame([], $theme->getActiveThemesList());
    }
}
