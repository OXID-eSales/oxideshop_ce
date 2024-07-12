<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\SystemRequirements;
use OxidEsales\TestingLibrary\UnitTestCase;
use PHPUnit\Framework\MockObject\MockObject as Mock;

final class SystemRequirementsTest extends UnitTestCase
{
    public function testGetBytes()
    {
        $systemRequirements = new SystemRequirements();

        $this->assertEquals(33554432, $systemRequirements->getBytes('32M'));
        $this->assertEquals(32768, $systemRequirements->getBytes('32K'));
        $this->assertEquals(34359738368, $systemRequirements->getBytes('32G'));
    }

    public function testGetRequiredModules()
    {
        $systemRequirements = new SystemRequirements();

        $requiredModules = $systemRequirements->getRequiredModules();
        $this->assertTrue(is_array($requiredModules));
        $requirementGroups = array_unique(array_values($requiredModules));

        $this->assertCount(3, $requirementGroups);
    }

    public function testGetModuleInfo()
    {
        /** @var SystemRequirements|Mock $systemRequirementsMock */
        $systemRequirementsMock = $this->getMock(SystemRequirements::class, ['checkMbString', 'checkModRewrite']);

        $systemRequirementsMock->expects($this->once())->method('checkMbString');
        $systemRequirementsMock->expects($this->never())->method('checkModRewrite');

        $systemRequirementsMock->getModuleInfo('mb_string');
    }

    /**
     * Testing SystemRequirements::checkServerPermissions()
     */
    public function testCheckServerPermissions()
    {
        $systemRequirementsMock = $this
            ->getMockBuilder(SystemRequirements::class)
            ->setMethods(['isAdmin'])
            ->getMock();

        $systemRequirementsMock->method('isAdmin')->willReturn(false);

        $this->assertEquals(2, $systemRequirementsMock->checkServerPermissions());
    }

    public function testCheckServerPermissionsReturnsSetupBlockedStatusIfDirectoriesDoNotExist()
    {
        $systemRequirementsMock = $this
            ->getMockBuilder(SystemRequirements::class)
            ->setMethods(['isAdmin'])
            ->getMock();

        $systemRequirementsMock->method('isAdmin')->willReturn(false);

        $this->assertEquals(0, $systemRequirementsMock->checkServerPermissions('nonExistentSourcePath'));
    }

    public function testCheckCollation()
    {
        $systemRequirements = new SystemRequirements();

        $collations = $systemRequirements->checkCollation();

        $this->assertEquals(0, count($collations));
    }

    public function testGetSysReqStatus()
    {
        /** @var SystemRequirements|Mock $systemRequirementsMock */
        $systemRequirementsMock = $this->getMock(SystemRequirements::class, ['getSystemInfo']);
        $systemRequirementsMock->expects($this->once())->method('getSystemInfo');

        $this->assertTrue($systemRequirementsMock->getSysReqStatus());
    }

    public function testGetReqInfoUrlWillReturnStringParsableAsUrl(): void
    {
        $url = (new SystemRequirements())->getReqInfoUrl('');

        $this->assertTrue(
            \array_key_exists('scheme', \parse_url((string) $url))
        );
    }

    public function testGetReqInfoUrlWithKnownParameterWillAddAnchorToUrl(): void
    {
        $parameter = 'php_version';
        $anchor = '#php';

        $url = (new SystemRequirements())->getReqInfoUrl($parameter);

        $this->assertStringContainsString($anchor, $url);
    }

    public function testGetReqInfoUrlWithServerPermissionsParameterWillAddAnchorToUrl(): void
    {
        $parameter = 'server_permissions';
        $anchor = '#schritt-customising-file-and-directory-permissions';

        $url = (new SystemRequirements())->getReqInfoUrl($parameter);

        $this->assertStringContainsString($anchor, $url);
    }

    public function testGetReqInfoUrlWithUnknownParameterWillReturnUnchangedUrl(): void
    {
        $unknownParameter = uniqid('parameter-', true);

        $url1 = (new SystemRequirements())->getReqInfoUrl('');
        $url2 = (new SystemRequirements())->getReqInfoUrl($unknownParameter);

        $this->assertEquals($url1, $url2);
    }

    /**
     * Testing SystemRequirements::getShopHostInfoFromConfig()
     *
     * @return null
     */
    public function testGetShopHostInfoFromConfig()
    {
        $this->getConfig()->setConfigParam('sShopURL', 'http://www.testshopurl.lt/testsubdir1/insideit2/');
        $systemRequirements = new SystemRequirements();
        $this->assertEquals(
            ['host' => 'www.testshopurl.lt', 'port' => 80, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => false],
            $systemRequirements->getShopHostInfoFromConfig()
        );
        $this->getConfig()->setConfigParam('sShopURL', 'https://www.testshopurl.lt/testsubdir1/insideit2/');
        $this->assertEquals(
            ['host' => 'www.testshopurl.lt', 'port' => 443, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => true],
            $systemRequirements->getShopHostInfoFromConfig()
        );
        $this->getConfig()->setConfigParam('sShopURL', 'https://51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            ['host' => '51.1586.51.15', 'port' => 21, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => true],
            $systemRequirements->getShopHostInfoFromConfig()
        );
        $this->getConfig()->setConfigParam('sShopURL', '51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            ['host' => '51.1586.51.15', 'port' => 21, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => false],
            $systemRequirements->getShopHostInfoFromConfig()
        );
    }

    /**
     * Testing SystemRequirements::_getShopSSLHostInfoFromConfig()
     *
     * @return null
     */
    public function testGetShopSSLHostInfoFromConfig()
    {
        $this->getConfig()->setConfigParam('sSSLShopURL', 'http://www.testshopurl.lt/testsubdir1/insideit2/');
        $systemRequirements = new SystemRequirements();
        $this->assertEquals(
            ['host' => 'www.testshopurl.lt', 'port' => 80, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => false],
            $systemRequirements->getShopSSLHostInfoFromConfig()
        );
        $this->getConfig()->setConfigParam('sSSLShopURL', 'https://www.testshopurl.lt/testsubdir1/insideit2/');
        $this->assertEquals(
            ['host' => 'www.testshopurl.lt', 'port' => 443, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => true],
            $systemRequirements->getShopSSLHostInfoFromConfig()
        );
        $this->getConfig()->setConfigParam('sSSLShopURL', 'https://51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            ['host' => '51.1586.51.15', 'port' => 21, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => true],
            $systemRequirements->getShopSSLHostInfoFromConfig()
        );
        $this->getConfig()->setConfigParam('sSSLShopURL', '51.1586.51.15:21/testsubdir1/insideit2/');
        $this->assertEquals(
            ['host' => '51.1586.51.15', 'port' => 21, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => false],
            $systemRequirements->getShopSSLHostInfoFromConfig()
        );
    }

    /**
     * Testing SystemRequirements::getShopHostInfoFromServerVars()
     *
     * @return null
     */
    public function testGetShopHostInfoFromServerVars()
    {
        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = null;
        $_SERVER['SERVER_PORT'] = null;
        $_SERVER['HTTP_HOST'] = 'www.testshopurl.lt';

        $systemRequirements = new SystemRequirements();
        $this->assertEquals(
            ['host' => 'www.testshopurl.lt', 'port' => 80, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => false],
            $systemRequirements->getShopHostInfoFromServerVars()
        );

        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = null;
        $_SERVER['HTTP_HOST'] = 'www.testshopurl.lt';
        $this->assertEquals(
            ['host' => 'www.testshopurl.lt', 'port' => 443, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => true],
            $systemRequirements->getShopHostInfoFromServerVars()
        );

        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['SERVER_PORT'] = 21;
        $_SERVER['HTTP_HOST'] = '51.1586.51.15';
        $this->assertEquals(
            ['host' => '51.1586.51.15', 'port' => 21, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => true],
            $systemRequirements->getShopHostInfoFromServerVars()
        );

        $_SERVER['SCRIPT_NAME'] = '/testsubdir1/insideit2/setup/index.php';
        $_SERVER['HTTPS'] = null;
        $_SERVER['SERVER_PORT'] = '21';
        $_SERVER['HTTP_HOST'] = '51.1586.51.15';
        $this->assertEquals(
            ['host' => '51.1586.51.15', 'port' => 21, 'dir'  => '/testsubdir1/insideit2/', 'ssl'  => false],
            $systemRequirements->getShopHostInfoFromServerVars()
        );
    }

    /**
     * Provides different server configuration to check memory limit.
     *
     * @return array
     */
    public function providerCheckMemoryLimit()
    {
        $memoryLimitsWithExpectedSystemHealth = [['8M', 0], ['31M', 0], ['32M', 1], ['59M', 1], ['60M', 2], ['61M', 2], ['-1', 2]];

        return $memoryLimitsWithExpectedSystemHealth;
    }

    /**
     * Testing SystemRequirements::checkMemoryLimit()
     * contains assertion for bug #5083
     *
     * @param string $memoryLimit    how much memory allocated.
     * @param int    $expectedResult if fits system requirements.
     *
     * @dataProvider providerCheckMemoryLimit
     *
     * @return null
     */
    public function testCheckMemoryLimit($memoryLimit, $expectedResult)
    {
        if ($this->getTestConfig()->getShopEdition() === 'EE') {
            $this->markTestSkipped('This test is for Community and Professional editions only.');
        }

        $systemRequirements = new SystemRequirements();

        $this->assertEquals($expectedResult, $systemRequirements->checkMemoryLimit($memoryLimit));
    }

    public function testFilterSystemRequirementsInfo()
    {
        $systemRequirementsInfoInput = [
            'group_a' => [
                'module_a' => SystemRequirements::MODULE_STATUS_BLOCKS_SETUP,
                'module_b' => SystemRequirements::MODULE_STATUS_OK,
            ],
            'group_b' => [
                'module_c' => SystemRequirements::MODULE_STATUS_FITS_MINIMUM_REQUIREMENTS,
            ]
        ];

        $expectedSystemRequirementsInfo = [
            'group_a' => [
                'module_a' => SystemRequirements::MODULE_STATUS_OK,
                'module_b' => SystemRequirements::MODULE_STATUS_FITS_MINIMUM_REQUIREMENTS,
            ],
            'group_b' => [
                'module_c' => SystemRequirements::MODULE_STATUS_BLOCKS_SETUP,
            ]
        ];

        $filterFunction = function ($groupId, $moduleId, $status) {
            if (($groupId === 'group_a') && ($moduleId === 'module_a')) {
                $status = SystemRequirements::MODULE_STATUS_OK;
            }
            if (($groupId === 'group_a') && ($moduleId === 'module_b')) {
                $status = SystemRequirements::MODULE_STATUS_FITS_MINIMUM_REQUIREMENTS;
            }
            if (($groupId === 'group_b') && ($moduleId === 'module_c')) {
                $status = SystemRequirements::MODULE_STATUS_BLOCKS_SETUP;
            }

            return $status;
        };

        $actualSystemRequirementsInfo = SystemRequirements::filter($systemRequirementsInfoInput, $filterFunction);

        $this->assertSame($expectedSystemRequirementsInfo, $actualSystemRequirementsInfo);
    }

    /**
     * @dataProvider canSetupContinuePositiveValuesProvider
     *
     * @param array $systemRequirementsInfo
     */
    public function testCanSetupContinueWithPositiveValues($systemRequirementsInfo)
    {
        $expectedValue = true;
        $actualValue = SystemRequirements::canSetupContinue($systemRequirementsInfo);

        $this->assertSame($expectedValue, $actualValue);
    }

    public function canSetupContinuePositiveValuesProvider()
    {
        $testCase1 = [
            'group_a' => [
                'module_a' => SystemRequirements::MODULE_STATUS_OK
            ]
        ];

        $testCase2 = [
            'group_a' => [
                'module_a' => SystemRequirements::MODULE_STATUS_FITS_MINIMUM_REQUIREMENTS,
                'module_b' => SystemRequirements::MODULE_STATUS_OK,
            ],
            'group_b' => [
                'module_c' => SystemRequirements::MODULE_STATUS_UNABLE_TO_DETECT,
            ]
        ];

        return [
            [$testCase1],
            [$testCase2],
        ];
    }

    /**
     * @dataProvider canSetupContinueNegativeValuesProvider
     *
     * @param array $systemRequirementsInfo
     */
    public function testSetupCantContinueWithNegativeValue($systemRequirementsInfo)
    {
        $expectedValue = false;
        $actualValue = SystemRequirements::canSetupContinue($systemRequirementsInfo);

        $this->assertSame($expectedValue, $actualValue);
    }

    public function canSetupContinueNegativeValuesProvider()
    {
        $testCase1 = [
            'group_a' => [
                'module_a' => SystemRequirements::MODULE_STATUS_BLOCKS_SETUP
            ]
        ];

        $testCase2 = [
            'group_a' => [
                'module_a' => SystemRequirements::MODULE_STATUS_UNABLE_TO_DETECT,
                'module_b' => SystemRequirements::MODULE_STATUS_FITS_MINIMUM_REQUIREMENTS,
            ],
            'group_b' => [
                'module_c' => SystemRequirements::MODULE_STATUS_BLOCKS_SETUP,
            ],
        ];

        return [
            [$testCase1],
            [$testCase2],
        ];
    }

    public function testIterateThroughSystemRequirementsInfo()
    {
        $systemRequirementsInfo = [
            'group_a' => [
                'module_a' => 0,
                'module_b' => 1,
            ],
            'group_b' => [
                'module_c' => 2,
                'module_d' => -1,
            ],
        ];

        $expectedOutput = [
            ['group_a', 'module_a', 0],
            ['group_a', 'module_b', 1],
            ['group_b', 'module_c', 2],
            ['group_b', 'module_d', -1],
        ];

        $actualOutput = [];
        $iteration = SystemRequirements::iterateThroughSystemRequirementsInfo($systemRequirementsInfo);
        foreach ($iteration as [$groupId, $moduleId, $moduleState]) {
            $actualOutput[] = [$groupId, $moduleId, $moduleState];
        }

        $this->assertSame($expectedOutput, $actualOutput);
    }

    public function testCheckCryptographicallySufficientConfigurationWithSecureConfig(): void
    {
        $result = (new SystemRequirements())->checkCryptographicallySufficientConfiguration();

        $this->assertEquals(SystemRequirements::MODULE_STATUS_OK, $result);
    }
}
