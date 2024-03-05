<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

use OxidEsales\Eshop\Application\Model\Payment;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\MetaData\TestData\TestModuleWithPartialMetaData\Article;

$sMetadataVersion = '2.0';

$aModule = [
    'extend' => [
        Payment::class => \OxidEsales\EshopCommunity\Tests\Integration\Internal\Framework\Module\MetaData\TestData\TestModuleWithPartialMetaData\Payment::class,
        'oxArticle'                                        => Article::class
    ],
];
