<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\Eshop\Application\Model\SeoEncoderArticle;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

/**
 * @internal
 */
class SeoEncoderArticleStdParametersTest extends TestCase
{
    public function testCategoryStdParameters(): void
    {
        $this->assertSame(['cnid' => 'cat1'], $this->encoder()->getCategoryStdParameters('cat1'));
    }

    public function testVendorStdParametersPrefixesIdAndKeepsListType(): void
    {
        $this->assertSame(
            ['cnid' => 'v_vend1', 'listtype' => 'vendor'],
            $this->encoder()->getVendorStdParameters('vend1', 'vendor')
        );
    }

    public function testManufacturerStdParameters(): void
    {
        $this->assertSame(
            ['mnid' => 'man1', 'listtype' => 'manufacturer'],
            $this->encoder()->getManufacturerStdParameters('man1', 'manufacturer')
        );
    }

    private function encoder(): SeoEncoderArticle
    {
        return (new ReflectionClass(SeoEncoderArticle::class))->newInstanceWithoutConstructor();
    }
}
