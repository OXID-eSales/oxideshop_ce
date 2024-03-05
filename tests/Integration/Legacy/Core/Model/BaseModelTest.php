<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Core\Model;

use OxidEsales\Eshop\Application\Model\Article;
use PHPUnit\Framework\TestCase;

final class BaseModelTest extends TestCase
{
    public function testFunctionIsPropertyLoadedReturnsFalseWhenPropertyIsNotLoadedAndIsField(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertFalse($model->isPropertyLoaded($fieldName));
    }

    public function testFunctionIsPropertyLoadedReturnsTrueWhenPropertyIsLoadedAndIsField(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $model->{$fieldName};

        $this->assertTrue($model->isPropertyLoaded($fieldName));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsNotLoadedAndIsField(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->{$fieldName}));
    }

    public function testLazyLoadingMagicIssetLoadsPropertyWhenPropertyIsNotLoadedAndIsField(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->{$fieldName}));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsLoadedAndIsField(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $model->{$fieldName};

        $this->assertTrue(isset($model->{$fieldName}));
    }

    public function testLazyLoadingMagicIssetOnValueOfFieldReturnsTrueWhenFieldIsNotLoaded(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->{$fieldName}->value));
    }

    public function testLazyLoadingMagicIssetOnValueOfFieldReturnsTrueWhenFieldIsLoaded(): void
    {
        $model = $this->getModelWithLazyLoading();
        $fieldName = $this->getTestFieldNameOfModelWithLazyLoading();

        $model->{$fieldName};

        $this->assertTrue(isset($model->{$fieldName}->value));
    }

    public function testLazyLoadingMagicIssetOnValueOfPropertyReturnsFalseWhenPropertyIsNotFieldAndNotLoaded(): void
    {
        $model = $this->getModelWithLazyLoading();

        $this->assertFalse(isset($model->someProperty->value));
    }

    private function getModelWithLazyLoading()
    {
        $model = oxNew(Article::class);
        $model->init('oxarticles');

        $model->setId('2000');
        $model->save();

        return $model;
    }

    private function getTestFieldNameOfModelWithLazyLoading(): string
    {
        return 'oxarticles__oxartnum';
    }
}
