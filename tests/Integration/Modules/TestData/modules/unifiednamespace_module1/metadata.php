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
$sMetadataVersion = '1.1';

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
        'content' => 'oeTest/unifiednamespace_module1/Controller/Test1ContentController',
    ),
    'files'       => array(
        'Test1Content' => 'oeTest/unifiednamespace_module1/Model/Test1Content.php',
    ),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
