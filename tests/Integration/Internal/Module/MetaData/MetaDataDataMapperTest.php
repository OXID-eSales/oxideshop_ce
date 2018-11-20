<?php declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Module\MetaData;

use OxidEsales\EshopCommunity\Internal\Application\ContainerBuilder;
use OxidEsales\EshopCommunity\Internal\Module\Configuration\DataObject\ModuleSetting;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Event\InvalidMetaDataEvent;
use OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\UnsupportedMetaDataValueTypeException;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class MetaDataDataMapperTest extends TestCase
{
    public function testModuleMetaData20()
    {
        $moduleDirectory = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'TestData' . DIRECTORY_SEPARATOR . $moduleDirectory . DIRECTORY_SEPARATOR . 'metadata.php';
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

        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $metaDataProviderDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadataprovider');
        $metaDataProviderDefinition->setPublic(true);

        $metaDataDataMapperDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadatamapper');
        $metaDataDataMapperDefinition->setPublic(true);

        $container->compile();

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

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.metadatadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.metadatadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);
        $settings = $moduleConfiguration->getSettings();

        /**
         * No InvalidMetaDataEvents should be dispatched
         */
        $this->assertEquals('', $message);
        $this->assertEquals('', $errorLevel);

        $this->assertEquals($expectedModuleData['id'], $moduleConfiguration->getId());
        $this->assertEquals($expectedModuleData['title'], $moduleConfiguration->getTitle());
        $this->assertEquals($expectedModuleData['description'], $moduleConfiguration->getDescription());
        $this->assertEquals($expectedModuleData['lang'], $moduleConfiguration->getLang());
        $this->assertEquals($expectedModuleData['thumbnail'], $moduleConfiguration->getThumbnail());
        $this->assertEquals($expectedModuleData['author'], $moduleConfiguration->getAuthor());
        $this->assertEquals($expectedModuleData['url'], $moduleConfiguration->getUrl());
        $this->assertEquals($expectedModuleData['email'], $moduleConfiguration->getEmail());
        $this->assertEquals($expectedModuleData['extend'], $settings[ModuleSetting::CLASS_EXTENSIONS]);
        $this->assertEquals($expectedModuleData['controllers'], $settings[ModuleSetting::CONTROLLERS]);
        $this->assertEquals($expectedModuleData['templates'], $settings[ModuleSetting::TEMPLATES]);
        $this->assertEquals($expectedModuleData['version'], $settings[ModuleSetting::VERSION]);
        $this->assertEquals($moduleDirectory . DIRECTORY_SEPARATOR, $settings[ModuleSetting::PATH]);
        //$this->assertEquals($moduleId, $settings[ModuleSetting::SMARTY_PLUGIN_DIRECTORIES]);
        $this->assertEquals($expectedModuleData['blocks'], $settings[ModuleSetting::TEMPLATE_BLOCKS]);
        $this->assertEquals($expectedModuleData['settings'], $settings[ModuleSetting::SHOP_MODULE_SETTING]);
        $this->assertEquals($expectedModuleData['events'], $settings[ModuleSetting::EVENTS]);
    }

    public function testModuleMetaData21()
    {
        $moduleDirectory = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'TestData' . DIRECTORY_SEPARATOR . $moduleDirectory . DIRECTORY_SEPARATOR . 'metadata.php';
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

        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $metaDataProviderDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadataprovider');
        $metaDataProviderDefinition->setPublic(true);

        $metaDataDataMapperDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadatamapper');
        $metaDataDataMapperDefinition->setPublic(true);

        $container->compile();

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

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.metadatadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.metadatadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);
        $settings = $moduleConfiguration->getSettings();

        /**
         * No InvalidMetaDataEvents should be dispatched
         */
        $this->assertEquals('', $message);
        $this->assertEquals('', $errorLevel);

        $this->assertEquals($expectedModuleData['id'], $moduleConfiguration->getId());
        $this->assertEquals($expectedModuleData['title'], $moduleConfiguration->getTitle());
        $this->assertEquals($expectedModuleData['description'], $moduleConfiguration->getDescription());
        $this->assertEquals($expectedModuleData['lang'], $moduleConfiguration->getLang());
        $this->assertEquals($expectedModuleData['thumbnail'], $moduleConfiguration->getThumbnail());
        $this->assertEquals($expectedModuleData['author'], $moduleConfiguration->getAuthor());
        $this->assertEquals($expectedModuleData['url'], $moduleConfiguration->getUrl());
        $this->assertEquals($expectedModuleData['email'], $moduleConfiguration->getEmail());
        $this->assertEquals($expectedModuleData['extend'], $settings[ModuleSetting::CLASS_EXTENSIONS]);
        $this->assertEquals($expectedModuleData['controllers'], $settings[ModuleSetting::CONTROLLERS]);
        $this->assertEquals($expectedModuleData['templates'], $settings[ModuleSetting::TEMPLATES]);
        $this->assertEquals($expectedModuleData['version'], $settings[ModuleSetting::VERSION]);
        $this->assertEquals($moduleDirectory . DIRECTORY_SEPARATOR, $settings[ModuleSetting::PATH]);
        $this->assertEquals($expectedModuleData['blocks'], $settings[ModuleSetting::TEMPLATE_BLOCKS]);
        $this->assertEquals($expectedModuleData['settings'], $settings[ModuleSetting::SHOP_MODULE_SETTING]);
        $this->assertEquals($expectedModuleData['events'], $settings[ModuleSetting::EVENTS]);
        $this->assertEquals($expectedModuleData['smartyPluginDirectories'], $settings[ModuleSetting::SMARTY_PLUGIN_DIRECTORIES]);
    }

    /**
     * Test that on metadata.php, which is only partially filled, safe types are returned by the corresponding methods
     */
    public function testModuleWithPartialMetaData()
    {
        $moduleDirectory = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'TestData' . DIRECTORY_SEPARATOR . $moduleDirectory . DIRECTORY_SEPARATOR . 'metadata.php';
        $expectedModuleData = [
            'extend' => [
                'OxidEsales\Eshop\Application\Model\Payment' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleWithPartialMetaData\Payment',
                'OxidEsales\Eshop\Application\Model\Article' => 'OxidEsales\EshopCommunity\Tests\Integration\Internal\Module\MetaData\TestData\TestModuleWithPartialMetaData\Article'
            ],
        ];

        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $metaDataProviderDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadataprovider');
        $metaDataProviderDefinition->setPublic(true);

        $metaDataDataMapperDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadatamapper');
        $metaDataDataMapperDefinition->setPublic(true);

        $container->compile();

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

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.metadatadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.metadatadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);
        $settings = $moduleConfiguration->getSettings();

        /**
         * The module directory name should be set as the module ID is missing in metadata.
         */
        $this->assertEquals($moduleDirectory, $moduleConfiguration->getId());
        /**
         * Additionally an event should have been fired, which mentions the missing Id and is of level ERROR
         */
        $this->assertContains('id', strtolower($message));
        $this->assertEquals(LogLevel::ERROR, $errorLevel);

        /** All methods should return type safe default values, if there were no values defined in metadata.php */
        $this->assertEquals('', $moduleConfiguration->getTitle());
        $this->assertEquals([], $moduleConfiguration->getDescription());
        $this->assertEquals('', $moduleConfiguration->getLang());
        $this->assertEquals('', $moduleConfiguration->getThumbnail());
        $this->assertEquals('', $moduleConfiguration->getAuthor());
        $this->assertEquals('', $moduleConfiguration->getUrl());
        $this->assertEquals('', $moduleConfiguration->getEmail());
        $this->assertEquals([], $settings[ModuleSetting::CONTROLLERS]);
        $this->assertEquals([], $settings[ModuleSetting::TEMPLATES]);
        $this->assertEquals('', $settings[ModuleSetting::VERSION]);
        $this->assertEquals($moduleDirectory . DIRECTORY_SEPARATOR, $settings[ModuleSetting::PATH]);
        $this->assertEquals([], $settings[ModuleSetting::TEMPLATE_BLOCKS]);
        $this->assertEquals([], $settings[ModuleSetting::SHOP_MODULE_SETTING]);
        $this->assertEquals([], $settings[ModuleSetting::EVENTS]);
        $this->assertEquals([], $settings[ModuleSetting::SMARTY_PLUGIN_DIRECTORIES]);

        /** This is the only value defined in metadata.php */
        $this->assertEquals($expectedModuleData['extend'], $settings[ModuleSetting::CLASS_EXTENSIONS]);
    }

    /**
     * @expectedException  \OxidEsales\EshopCommunity\Internal\Module\MetaData\Validator\UnsupportedMetaDataValueTypeException
     */
    public function testModuleWithSurplusData()
    {
        $moduleDirectory = $moduleId = ucfirst(__FUNCTION__);
        /** The content of metadata.php and $expectedModuleData must match  */
        $metaDataFilePath = __DIR__ . DIRECTORY_SEPARATOR . 'TestData' . DIRECTORY_SEPARATOR . $moduleDirectory . DIRECTORY_SEPARATOR . 'metadata.php';
        $expectedModuleData = [
            'id' => $moduleId,
        ];

        $containerBuilder = new ContainerBuilder();
        $container = $containerBuilder->getContainer();

        $metaDataProviderDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadataprovider');
        $metaDataProviderDefinition->setPublic(true);

        $metaDataDataMapperDefinition = $container->getDefinition('oxid_esales.module.metadata.metadatadatamapper');
        $metaDataDataMapperDefinition->setPublic(true);

        $container->compile();

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

        $metaDataDataProvider = $container->get('oxid_esales.module.metadata.metadatadataprovider');
        $normalizedMetaData = $metaDataDataProvider->getData($metaDataFilePath);

        $metaDataDataMapper = $container->get('oxid_esales.module.metadata.metadatadatamapper');
        $moduleConfiguration = $metaDataDataMapper->fromData($normalizedMetaData);

        $this->assertEquals(LogLevel::ERROR, $errorLevel);
        $this->assertContains('extrastuff', strtolower($message));

        $this->assertEquals($expectedModuleData['id'], $moduleConfiguration->getId());
    }
}

