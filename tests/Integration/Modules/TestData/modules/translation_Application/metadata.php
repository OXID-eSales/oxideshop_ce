<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'           => 'translation_Application',
    'title'        => 'Translations in Application folder',
    'description'  => 'In this module the translations lay in the Application folder.',
    'thumbnail'    => 'picture.png',
    'version'      => '1.0',
    'author'       => 'OXID eSales AG',
    'extend' => array(
        // This one is needed, cause if the module is not extending anything, we don't search for the translations!
        'oxarticle' => 'translation_Application/myarticle',
    )
);
