<?php
/**
 *
 * @category      module
 * @package       moduleone
 * @author        John Doe
 * @link          www.johndoe.com
 * @copyright (C) John Doe 20162016
 */

use virtualnamespace_module1\Model\TestContent;

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'virtualnamespace_module1',
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
        'content' => 'virtualnamespace_module1/Controller/Test1ContentController',
    ),
    'files'       => array(
        TestContent::class => 'virtualnamespace_module1/Model/TestContent.php',
    ),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
