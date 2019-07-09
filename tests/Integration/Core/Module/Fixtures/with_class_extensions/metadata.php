<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.1';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'with_class_extensions',
    'title'        => 'Smarty plugin directoies',
    'description'  => 'Test defining smarty plugin directoies',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend'       => [
        \OxidEsales\Eshop\Application\Model\Article::class => 'with_class_extensions/ModuleArticle',
    ]
);
