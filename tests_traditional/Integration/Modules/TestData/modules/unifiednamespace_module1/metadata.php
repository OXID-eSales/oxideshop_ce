<?php

/**
 *
 * @category      module
 * @package       moduleone
 * @author        John Doe
 * @link          www.johndoe.com
 * @copyright (C) John Doe 20162016
 */

use unifiednamespace_module1\Model\TestContent;

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'unifiednamespace_module1',
    'title'       => array(
        'de' => 'OXID eSales example module 1',
        'en' => 'OXID eSales example module 1',
    ),
    'description' => array(
        'de' => 'This module overrides ContentController::getTitle()',
        'en' => 'This module overrides ContentController::getTitle()',
    ),
    'version'     => '1.0.0',
    'author'      => 'John Doe',
    'url'         => 'www.johndoe.com',
    'email'       => 'john@doe.com',
    'extend'      => array(
        'content' => OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\unifiednamespace_module1\Controller\Test1ContentController::class,
        'test1content' => OxidEsales\EshopCommunity\Tests\Integration\Modules\TestData\modules\unifiednamespace_module1\Model\Module1TestContent::class
    ),
    'controllers'       => array(),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
