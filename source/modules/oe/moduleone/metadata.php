<?php
/**
 *
 * @category      module
 * @package       moduleone
 * @author        John Doe
 * @link          www.johndoe.com
 * @copyright (C) John Doe 20162016
 */

use OxidEsales\Eshop\Application\Model\User;
use OxidEsales\Eshop\Application\Model\Article;

/**
 * Metadata version
 */
$sMetadataVersion = '1.2';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'oemoduleone',
    'title'       => array(
        'de' => 'OXID eSales example module for v6.0 - 1',
        'en' => 'OXID eSales example module for v6.0 - 1',
    ),
    'description' => array(
        'de' => 'This module overrides User::getBoni() and increments the credit rating by 20%',
        'en' => 'This module overrides User::getBoni() and increments the credit rating by 20%',
    ),
    'thumbnail'   => 'out/pictures/picture.png',
    'version'     => '1.0.0',
    'author'      => 'John Doe',
    'url'         => 'www.johndoe.com',
    'email'       => 'john@doe.com',
    'extend'      => array(
        User::class => 'oe/moduleone/application/model/oemoduleoneuser',
        Article::class => 'oe/moduleone/application/model/oemoduleonearticle',
    ),
    'files'       => array(),
    'templates'   => array(),
    'blocks'      => array(),
    'settings'    => array(),
    'events'      => array(),
);
