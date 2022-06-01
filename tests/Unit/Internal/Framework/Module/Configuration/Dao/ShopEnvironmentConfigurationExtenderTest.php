<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Module\Configuration\Dao;

use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationDaoInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentWithOrphanSettingEvent;
use PHPUnit\Framework\TestCase;
use OxidEsales\EshopCommunity\Internal\Framework\Module\Configuration\Dao\ShopEnvironmentConfigurationExtender;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ShopEnvironmentConfigurationExtenderTest extends TestCase
{
    use ProphecyTrait;

    private ShopEnvironmentConfigurationDaoInterface|ObjectProphecy $environmentDao;
    private ShopEnvironmentConfigurationExtender $environmentExtension;
    private ObjectProphecy|EventDispatcherInterface $eventDispatcher;
    private int $shopId = 1;

    protected function setUp(): void
    {
        parent::setUp();
        $this->environmentDao = $this->prophesize(ShopEnvironmentConfigurationDaoInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $this->environmentExtension = new ShopEnvironmentConfigurationExtender(
            $this->environmentDao->reveal(),
            $this->eventDispatcher->reveal()
        );
    }

    public function testGetExtendedConfigurationWithEmpty(): void
    {
        $shopConfiguration = [
            'modules' => [
                'abc' => [],
            ],
        ];
        $environmentConfiguration = [];
        $this->environmentDao->get($this->shopId)->willReturn($environmentConfiguration);

        $result = $this->environmentExtension->getExtendedConfiguration($this->shopId, $shopConfiguration);

        $this->assertSame($shopConfiguration, $result);
    }

    public function testGetExtendedConfigurationWithModuleChains(): void
    {
        $shopConfiguration = [
            'modules' => [
                'abc' => [],
            ],
        ];
        $environmentConfiguration = [
            'moduleChains' => ['xyz' => 123],
        ];
        $this->environmentDao->get($this->shopId)->willReturn($environmentConfiguration);

        $result = $this->environmentExtension->getExtendedConfiguration($this->shopId, $shopConfiguration);

        $this->assertSame($shopConfiguration, $result);
    }

    public function testGetExtendedConfigurationWithModule(): void
    {
        $shopConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'group' => 'original-group-1',
                            'some-type' => 'original-type-1',
                            'value' => 'original-value-1',
                        ],
                    ],
                ],
            ],
        ];
        $environmentConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'value' => 'new-value-1',
                        ],
                    ],
                ],
            ],
        ];
        $expectedConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'group' => 'original-group-1',
                            'some-type' => 'original-type-1',
                            'value' => 'new-value-1',
                        ],
                    ],
                ],
            ],
        ];
        $this->environmentDao->get($this->shopId)->willReturn($environmentConfiguration);

        $result = $this->environmentExtension->getExtendedConfiguration($this->shopId, $shopConfiguration);

        $this->assertSame($expectedConfiguration, $result);
    }

    public function testGetExtendedConfigurationWithMissingSetting(): void
    {
        $missingSettingId = 'some-missing-setting-1';
        $shopConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'group' => 'original-group-1',
                            'some-type' => 'original-type-1',
                            'value' => 'original-value-1',
                        ],
                    ],
                ],
            ],
        ];
        $environmentConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        $missingSettingId => [
                            'value' => 'new-value-1',
                        ],
                    ],
                ],
            ],
        ];
        $this->environmentDao->get($this->shopId)->willReturn($environmentConfiguration);
        $event =  new ShopEnvironmentWithOrphanSettingEvent(
            $this->shopId,
            'abc',
            $missingSettingId
        );
        $this->eventDispatcher
            ->dispatch($event)
            ->willReturnArgument();

        $this->environmentExtension->getExtendedConfiguration($this->shopId, $shopConfiguration);

        $this->eventDispatcher
            ->dispatch($event)
            ->shouldHaveBeenCalledOnce();
    }

    public function testGetExtendedConfigurationWithMissingModuleIdAndSetting(): void
    {
        $missingModuleId = 'missing-module-id';
        $missingSettingId = 'some-missing-setting-1';
        $shopConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'group' => 'original-group-1',
                            'some-type' => 'original-type-1',
                            'value' => 'original-value-1',
                        ],
                    ],
                ],
                'def' => [
                    'moduleSettings' => [
                        'some-setting-2' => [
                            'group' => 'original-group-2',
                            'some-type' => 'original-type-2',
                            'value' => 'original-value-2',
                        ],
                    ],
                ],
            ],
        ];
        $environmentConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'value' => 'new-value-1',
                        ],
                        $missingSettingId => [
                            'value' => 123,
                        ],
                    ],
                ],
                $missingModuleId => [
                    'moduleSettings' => [],
                ],
                'def' => [
                    'moduleSettings' => [
                        'some-setting-2' => [
                            'value' => 'new-value-2',
                        ]
                    ],
                ],
            ],
        ];
        $expectedConfiguration = [
            'modules' => [
                'abc' => [
                    'moduleSettings' => [
                        'some-setting-1' => [
                            'group' => 'original-group-1',
                            'some-type' => 'original-type-1',
                            'value' => 'new-value-1',
                        ],
                    ],
                ],
                'def' => [
                    'moduleSettings' => [
                        'some-setting-2' => [
                            'group' => 'original-group-2',
                            'some-type' => 'original-type-2',
                            'value' => 'new-value-2',
                        ],
                    ],
                ],
            ],
        ];
        $event = new ShopEnvironmentWithOrphanSettingEvent(
            $this->shopId,
            'abc',
            $missingSettingId
        );
        $this->environmentDao->get($this->shopId)->willReturn($environmentConfiguration);
        $this->eventDispatcher
            ->dispatch($event)
            ->willReturnArgument();

        $result = $this->environmentExtension->getExtendedConfiguration($this->shopId, $shopConfiguration);

        $this->assertSame($expectedConfiguration, $result);
    }
}
