<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Controller\ContentController;
use OxidEsales\Eshop\Application\Model\Article;
use OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\InvalidNamespaceModule1\Controller\NonExistentClass;
use OxidEsales\EshopCommunity\Tests\Acceptance\Admin\testData\modules\oxid\InvalidNamespaceModule1\Model\NonExistentFile;

$sMetadataVersion = '2.0';
$aModule = [
    'id' => 'InvalidNamespaceModule',
    'title' => 'Invalid Namespaced Module',
    'description' => 'Test module validation for modules, which use namespaces',
    'thumbnail' => 'module.png',
    'version' => '1.0',
    'author' => 'OXID',
    'extend' => [
        /**
         * In this test case the file with the proper name is present, but it contains the wrong class.
         * This means the class cannot be loaded properly
         */
        ContentController::class => NonExistentClass::class,
        /**
         * In this test case the class file does not exist at all and thus the class cannot be loaded
         */
        Article::class => NonExistentFile::class,
    ],
];
