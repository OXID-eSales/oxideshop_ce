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
$aModule = [
    'extend' => [
        \OxidEsales\Eshop\Application\Model\Payment::class => \OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\MetaData\TestData\TestModuleWithPartialMetaData\Payment::class,
        'oxArticle'                                        => \OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\MetaData\TestData\TestModuleWithPartialMetaData\Article::class
    ],
];
