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
 * Module information - the commented things are not working atm or will not be implemented
 */
$aModule = array(
    'id'          => 'virtualnamespace_module3',
    'title'       => array(
        'de' => 'OXID eSales example module3',
        'en' => 'OXID eSales example module3',
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
        TestContent::class => 'virtualnamespace_module3/Model/Test3Content',
        // TestContent::class => 'virtualnamespace_module3/Model/Test3NamespacedContent',
        // 'Test2Content' => 'virtualnamespace_module3/Model/Test3Content',
        // 'Test2Content' => 'virtualnamespace_module3/Model/Test3NamespacedContent',
    ),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
