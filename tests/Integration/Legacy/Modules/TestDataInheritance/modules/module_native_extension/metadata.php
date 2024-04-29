<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

use OxidEsales\Eshop\Application\Controller\ContentController as EshopContentController;
use OxidEsales\Eshop\Application\Model\Article as EshopArticleModel;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_native_extension\Article as ModuleArticleModel;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_native_extension\ContentController as ModuleContentController;
use OxidEsales\EshopCommunity\Tests\Integration\Legacy\Modules\TestDataInheritance\modules\module_native_extension\NativeExtendingContentController;

/**
 * Metadata version
 */
$sMetadataVersion = '2.0';

/**
 * Module information
 */
$aModule = array(
    'id'          => 'module_native_extension',
    'title'       => 'Test OXID eShop native extend of chain extended class',
    'version'     => '1.0',
    'author'      => 'OXID eSales AG',
    'extend'      => [
        EshopContentController::class => ModuleContentController::class,
        EshopArticleModel::class => ModuleArticleModel::class
    ],
    'controllers' => [
        'nativeinheritedcontentcontroller' => NativeExtendingContentController::class
    ],
);
