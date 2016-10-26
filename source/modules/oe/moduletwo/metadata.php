<?php
/**
 *
 * @category      module
 * @package       moduletwo
 * @author        John Doe
 * @link          www.johndoe.com
 * @copyright (C) John Doe 20162016
 */

use OxidEsales\Eshop\Application\Model\User;

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'oemoduletwo',
    'title'       => array(
        'de' => 'OXID eSales example module for v6.0 - 2',
        'en' => 'OXID eSales example module for v6.0 - 2',
    ),
    'description' => array(
        'de' => 'This module overrides User::getBoni() and increments the credit rating by 10',
        'en' => 'This module overrides User::getBoni() and increments the credit rating by 10',
    ),
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '1.0.0',
    'author'      => 'John Doe',
    'url'         => 'www.johndoe.com',
    'email'       => 'john@doe.com',
    'extend'      => array(
        User::class => 'oe/moduletwo/application/model/oemoduletwouser',
    ),
    'files'       => array(),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
