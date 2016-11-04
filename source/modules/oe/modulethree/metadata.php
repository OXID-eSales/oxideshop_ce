<?php
/**
 *
 * @category      module
 * @package       modulethree
 * @author        John Doe
 * @link          www.johndoe.com
 * @copyright (C) John Doe 20162016
 */

use OxidEsales\Eshop\Application\Model\User;

/**
 * Metadata version
 */
$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'oemodulethree',
    'title'       => array(
        'de' => 'OXID eSales example module for v6.0 - 3',
        'en' => 'OXID eSales example module for v6.0 - 3',
    ),
    'description' => array(
        'de' => 'This module overrides User::getBoni() and increments the credit rating by 100',
        'en' => 'This module overrides User::getBoni() and increments the credit rating by 100',
    ),
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '1.0.0',
    'author'      => 'John Doe',
    'url'         => 'www.johndoe.com',
    'email'       => 'john@doe.com',
    'extend'      => array(
        'oxuser' => 'oe/modulethree/application/model/oemodulethreeuser',
        'oxarticle' => 'oe/modulethree/application/model/oemodulethreearticle',
    ),
    'files'       => array(),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
