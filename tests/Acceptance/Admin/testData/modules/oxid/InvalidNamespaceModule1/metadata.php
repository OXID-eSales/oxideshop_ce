<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

$sMetadataVersion = '1.1';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'InvalidNamespaceModule1',
    'title'       => 'Invalid Namespaced Module #1',
    'description' => 'Test module validation for modules, which use namespaces',
    'thumbnail'   => 'module.png',
    'version'     => '1.0',
    'author'      => 'OXID',
    'extend'      => [
        /**
         * In this test case the file with the proper name is present, but it contains the wrong class.
         * This means the class cannot be loaded properly
         */
        \OxidEsales\Eshop\Application\Controller\ContentController::class => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\InvalidNamespaceModule1\Controller\NonExistentClass::class,
        /**
         * In this test case the class file does not exist at all and thus the class cannot be loaded
         */
        \OxidEsales\Eshop\Application\Model\Article::class                => \OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\InvalidNamespaceModule1\Model\NonExistentFile::class
    ],
);
