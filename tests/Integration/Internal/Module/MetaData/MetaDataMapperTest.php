<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Event\InvalidMetaDataEvent;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class MetaDataMapperTest extends TestCase
{
    public function testModuleMetaData20()
    {
        $testModuleDirectory = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = $this->getMetaDataFilePath($testModuleDirectory);
        $metaDataCheckSum = md5_file($metaDataFilePath);
        $expectedModuleData = [
            'id'          => 'TestModuleMetaData20',
            'title'       => 'Module for testModuleMetaData20',
            'description' => [
                'de' => 'de description for testModuleMetaData20',
                'en' => 'en description for testModuleMetaData20',
            ],
            'lang'        => 'en',
            'thumbnail'   => 'picture.png',
            'version'     => '1.0',
            'author'      => 'OXID eSales AG',
            'url'         => 'https://www.oxid-esales.com',
            'email'       => 'info@oxid-esales.com',
            'extend'      => [
                'OxidEsales\Eshop\Application\Model\Payment' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData20\Payment',
                'OxidEsales\Eshop\Application\Model\Article' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData20\Article'
            ],
            'controllers' => [
                'myvendor_mymodule_MyModuleController'      => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData20\Controller',
                'myvendor_mymodule_MyOtherModuleController' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData20\OtherController',
            ],
            'templates'   => [
                'mymodule.tpl'       => 'TestModuleMetaData20/mymodule.tpl',
                'mymodule_other.tpl' => 'TestModuleMetaData20/mymodule_other.tpl'
            ],
            'blocks'      => [
                [
                    'theme'    => 'theme_id',
                    'template' => 'template_1.tpl',
                    'block'    => 'block_1',
                    'file'     => '/blocks/template_1.tpl',
                    'position' => '1'
                ],
                [
                    'template' => 'template_2.tpl',
                    'block'    => 'block_2',
                    'file'     => '/blocks/template_2.tpl',
                    'position' => '2'
                ],
            ],
            'settings'    => [
                ['group' => 'main', 'name' => 'setting_1', 'type' => 'select', 'value' => '0', 'constraints' => '0|1|2|3', 'position' => 3],
                ['group' => 'main', 'name' => 'setting_2', 'type' => 'password', 'value' => 'changeMe']
            ],
            'events'      => [
                'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData20\Events::onActivate',
                'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData20\Events::onDeactivate'
            ],
        ];

        $container = $this->getCompiledTestContainer();

        $errorLevel = '';
        $message = '';
        $eventDispatcher = $container->get('event_dispatcher');
        $eventDispatcher->addListener(
            InvalidMetaDataEvent::NAME,
            function (InvalidMetaDataEvent $event) use (&$errorLevel, &$message) {
                $errorLevel = $event->getLevel();
                $message = $event->getMessage();
            }
        );

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.service.metadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.datamapper.metadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);
        $settings = $moduleConfiguration->getSettings();

        /**
         * No InvalidMetaDataEvents should be dispatched
         */
        $this->assertSame('', $message);
        $this->assertSame('', $errorLevel);

        $this->assertSame($expectedModuleData['id'], $moduleConfiguration->getId());
        $this->assertSame($expectedModuleData['title'], $moduleConfiguration->getTitle());
        $this->assertSame($expectedModuleData['description'], $moduleConfiguration->getDescription());
        $this->assertSame($expectedModuleData['lang'], $moduleConfiguration->getLang());
        $this->assertSame($expectedModuleData['thumbnail'], $moduleConfiguration->getThumbnail());
        $this->assertSame($expectedModuleData['author'], $moduleConfiguration->getAuthor());
        $this->assertSame($expectedModuleData['url'], $moduleConfiguration->getUrl());
        $this->assertSame($expectedModuleData['email'], $moduleConfiguration->getEmail());
        $this->assertSame($metaDataCheckSum, $moduleConfiguration->getMetaDataCheckSum());
        $this->assertSame($expectedModuleData['extend'], $settings[ModuleSetting::CLASS_EXTENSIONS]);
        $this->assertSame($expectedModuleData['controllers'], $settings[ModuleSetting::CONTROLLERS]);
        $this->assertSame($expectedModuleData['templates'], $settings[ModuleSetting::TEMPLATES]);
        $this->assertSame($expectedModuleData['version'], $settings[ModuleSetting::VERSION]);
        $this->assertSame($testModuleDirectory . DIRECTORY_SEPARATOR, $settings[ModuleSetting::PATH]);
        $this->assertSame($expectedModuleData['blocks'], $settings[ModuleSetting::TEMPLATE_BLOCKS]);
        $this->assertSame($expectedModuleData['settings'], $settings[ModuleSetting::SHOP_MODULE_SETTING]);
        $this->assertSame($expectedModuleData['events'], $settings[ModuleSetting::EVENTS]);
    }

    public function testModuleMetaData21()
    {
        $testModuleDirectory = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = $this->getMetaDataFilePath($testModuleDirectory);
        $expectedModuleData = [
            'id'                      => 'TestModuleMetaData21',
            'title'                   => 'Module for testModuleMetaData21',
            'description'             => [
                'de' => 'de description for testModuleMetaData21',
                'en' => 'en description for testModuleMetaData21',
            ],
            'lang'                    => 'en',
            'thumbnail'               => 'picture.png',
            'version'                 => '1.0',
            'author'                  => 'OXID eSales AG',
            'url'                     => 'https://www.oxid-esales.com',
            'email'                   => 'info@oxid-esales.com',
            'extend'                  => [
                'OxidEsales\Eshop\Application\Model\Payment' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData21\Payment',
                'OxidEsales\Eshop\Application\Model\Article' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData21\Article'
            ],
            'controllers'             => [
                'myvendor_mymodule_MyModuleController'      => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData21\Controller',
                'myvendor_mymodule_MyOtherModuleController' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData21\OtherController',
            ],
            'templates'               => [
                'mymodule.tpl'       => 'TestModuleMetaData21/mymodule.tpl',
                'mymodule_other.tpl' => 'TestModuleMetaData21/mymodule_other.tpl'
            ],
            'blocks'                  => [
                [
                    'theme'    => 'theme_id',
                    'template' => 'template_1.tpl',
                    'block'    => 'block_1',
                    'file'     => '/blocks/template_1.tpl',
                    'position' => '1'
                ],
                [
                    'template' => 'template_2.tpl',
                    'block'    => 'block_2',
                    'file'     => '/blocks/template_2.tpl',
                    'position' => '2'
                ],
            ],
            'settings'                => [
                ['group' => 'main', 'name' => 'setting_1', 'type' => 'select', 'value' => '0', 'constraints' => '0|1|2|3', 'position' => 3],
                ['group' => 'main', 'name' => 'setting_2', 'type' => 'password', 'value' => 'changeMe']
            ],
            'events'                  => [
                'onActivate'   => '\OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData21\Events::onActivate',
                'onDeactivate' => '\OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleMetaData21\Events::onDeactivate'
            ],
            'smartyPluginDirectories' => [
                'Smarty/PluginDirectory'
            ],
        ];

        $container = $this->getCompiledTestContainer();

        $errorLevel = '';
        $message = '';
        $eventDispatcher = $container->get('event_dispatcher');
        $eventDispatcher->addListener(
            InvalidMetaDataEvent::NAME,
            function (InvalidMetaDataEvent $event) use (&$errorLevel, &$message) {
                $errorLevel = $event->getLevel();
                $message = $event->getMessage();
            }
        );

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.service.metadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.datamapper.metadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);
        $settings = $moduleConfiguration->getSettings();

        /**
         * No InvalidMetaDataEvents should be dispatched
         */
        $this->assertEquals('', $message);
        $this->assertEquals('', $errorLevel);

        $this->assertSame($expectedModuleData['id'], $moduleConfiguration->getId());
        $this->assertSame($expectedModuleData['title'], $moduleConfiguration->getTitle());
        $this->assertSame($expectedModuleData['description'], $moduleConfiguration->getDescription());
        $this->assertSame($expectedModuleData['lang'], $moduleConfiguration->getLang());
        $this->assertSame($expectedModuleData['thumbnail'], $moduleConfiguration->getThumbnail());
        $this->assertSame($expectedModuleData['author'], $moduleConfiguration->getAuthor());
        $this->assertSame($expectedModuleData['url'], $moduleConfiguration->getUrl());
        $this->assertSame($expectedModuleData['email'], $moduleConfiguration->getEmail());
        $this->assertSame($expectedModuleData['extend'], $settings[ModuleSetting::CLASS_EXTENSIONS]);
        $this->assertSame($expectedModuleData['controllers'], $settings[ModuleSetting::CONTROLLERS]);
        $this->assertSame($expectedModuleData['templates'], $settings[ModuleSetting::TEMPLATES]);
        $this->assertSame($expectedModuleData['version'], $settings[ModuleSetting::VERSION]);
        $this->assertSame($testModuleDirectory . DIRECTORY_SEPARATOR, $settings[ModuleSetting::PATH]);
        $this->assertSame($expectedModuleData['blocks'], $settings[ModuleSetting::TEMPLATE_BLOCKS]);
        $this->assertSame($expectedModuleData['settings'], $settings[ModuleSetting::SHOP_MODULE_SETTING]);
        $this->assertSame($expectedModuleData['events'], $settings[ModuleSetting::EVENTS]);
        $this->assertSame($expectedModuleData['smartyPluginDirectories'], $settings[ModuleSetting::SMARTY_PLUGIN_DIRECTORIES]);
    }

    /**
     * Test that on metadata.php, which is only partially filled, safe types are returned by the corresponding methods
     */
    public function testModuleWithPartialMetaData()
    {
        $testModuleDirectory = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = $this->getMetaDataFilePath($testModuleDirectory);
        $expectedModuleData = [
            'extend' => [
                'OxidEsales\Eshop\Application\Model\Payment' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleWithPartialMetaData\Payment',
                'OxidEsales\Eshop\Application\Model\Article' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleWithPartialMetaData\Article'
            ],
        ];

        $container = $this->getCompiledTestContainer();

        /**
         * As no module ID was set, an InvalidMetaDataEvent should be fired
         */
        $errorLevel = '';
        $message = '';
        $eventDispatcher = $container->get('event_dispatcher');
        $eventDispatcher->addListener(
            InvalidMetaDataEvent::NAME,
            function (InvalidMetaDataEvent $event) use (&$errorLevel, &$message) {
                $errorLevel = $event->getLevel();
                $message = $event->getMessage();
            }
        );

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.service.metadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.datamapper.metadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);
        $settings = $moduleConfiguration->getSettings();

        /**
         * The module directory name should be set as the module ID is missing in metadata.Same
         */
        $this->assertEquals($testModuleDirectory, $moduleConfiguration->getId());
        /**
         * Additionally an event should have been fired, which mentions the missing Id and is of level ERROR
         */
        $this->assertContains('id', strtolower($message));
        $this->assertEquals(LogLevel::ERROR, $errorLevel);

        /** All methods should return type safe default values, if there were no values defined in metadata.php */
        $this->assertSame('', $moduleConfiguration->getTitle());
        $this->assertSame([], $moduleConfiguration->getDescription());
        $this->assertSame('', $moduleConfiguration->getLang());
        $this->assertSame('', $moduleConfiguration->getThumbnail());
        $this->assertSame('', $moduleConfiguration->getAuthor());
        $this->assertSame('', $moduleConfiguration->getUrl());
        $this->assertSame('', $moduleConfiguration->getEmail());
        $this->assertSame([], $settings[ModuleSetting::CONTROLLERS]);
        $this->assertSame([], $settings[ModuleSetting::TEMPLATES]);
        $this->assertSame('', $settings[ModuleSetting::VERSION]);
        $this->assertSame($testModuleDirectory . DIRECTORY_SEPARATOR, $settings[ModuleSetting::PATH]);
        $this->assertSame([], $settings[ModuleSetting::TEMPLATE_BLOCKS]);
        $this->assertSame([], $settings[ModuleSetting::SHOP_MODULE_SETTING]);
        $this->assertSame([], $settings[ModuleSetting::EVENTS]);
        $this->assertSame([], $settings[ModuleSetting::SMARTY_PLUGIN_DIRECTORIES]);

        /** This is the only value defined in metadata.php */
        $this->assertEquals($expectedModuleData['extend'], $settings[ModuleSetting::CLASS_EXTENSIONS]);
    }

    /**
     * @expectedException  \OxidEsales\EshopCommunity\Internal\Module\MetaData\Exception\UnsupportedMetaDataValueTypeException
     */
    public function testModuleWithSurplusData()
    {
        $testModuleDirectory = $moduleId = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = $this->getMetaDataFilePath($testModuleDirectory);
        $expectedModuleData = [
            'id' => $moduleId,
        ];

        $container = $this->getCompiledTestContainer();

        /**
         * As an extra key is present in metadata.php, an InvalidMetaDataEvent should be fired
         */
        $errorLevel = '';
        $message = '';
        $eventDispatcher = $container->get('event_dispatcher');
        $eventDispatcher->addListener(
            InvalidMetaDataEvent::NAME,
            function (InvalidMetaDataEvent $event) use (&$errorLevel, &$message) {
                $errorLevel = $event->getLevel();
                $message = $event->getMessage();
            }
        );

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.service.metadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.datamapper.metadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);

        $this->assertEquals(LogLevel::ERROR, $errorLevel);
        $this->assertContains('extrastuff', strtolower($message));

        $this->assertEquals($expectedModuleData['id'], $moduleConfiguration->getId());
    }

    /**
     * @return \Symfony\Component\DependencyInjection\ContainerBuilder
     */
    private function getCompiledTestContainer(): \Symfony\Component\DependencyInjection\ContainerBuilder
    {
        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $metaDataProviderDefinition = $container->getDefinition('oxid_esales.module.metadata.service.metadataprovider');
        $metaDataProviderDefinition->setPublic(true);

        $metaDataDataMapperDefinition = $container->getDefinition('oxid_esales.module.metadata.datamapper.metadatamapper');
        $metaDataDataMapperDefinition->setPublic(true);

        $container->compile();

        return $container;
    }

    /**
     * @param string $testModuleDirectory
     *
     * @return string
     */
    private function getMetaDataFilePath(string $testModuleDirectory): string
    {
        $metaDataFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'TestData' . DIRECTORY_SEPARATOR . $testModuleDirectory . DIRECTORY_SEPARATOR . 'metadata.php';

        return $metaDataFilePath;
}
}

