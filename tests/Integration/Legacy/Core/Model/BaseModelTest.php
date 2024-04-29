<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Model;

use OxidEsales\Eshop\Application\Model\Article;
use PHPUnit\Framework\TestCase;

class BaseModelTest extends TestCase
{
    /** @runInSeparateProcess  */
    public function testFunctionIsPropertyLoadedReturnsFalseWhenPropertyIsNotLoadedAndIsField()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertFalse($model->isPropertyLoaded($fieldName));
    }

    public function testFunctionIsPropertyLoadedReturnsTrueWhenPropertyIsLoadedAndIsField()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $model->$fieldName;

        $this->assertTrue($model->isPropertyLoaded($fieldName));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsNotLoadedAndIsField()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->$fieldName));
    }

    public function testLazyLoadingMagicIssetLoadsPropertyWhenPropertyIsNotLoadedAndIsField()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->$fieldName));
    }

    public function testLazyLoadingMagicIssetReturnsTrueWhenPropertyIsLoadedAndIsField()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $model->$fieldName;

        $this->assertTrue(isset($model->$fieldName));
    }

    /** @runInSeparateProcess  */
    public function testLazyLoadingMagicIssetOnValueOfFieldReturnsTrueWhenFieldIsNotLoaded()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->$fieldName->value));
    }

    /** @runInSeparateProcess  */
    public function testLazyLoadingMagicIssetOnValueOfFieldReturnsTrueWhenFieldIsLoaded()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $model->$fieldName;

        $this->assertTrue(isset($model->$fieldName->value));
    }

    public function testLazyLoadingMagicIssetOnValueOfPropertyReturnsFalseWhenPropertyIsNotFieldAndNotLoaded()
    {
        $model = $this->getModelWithLazyLoading();

        $this->assertFalse(isset($model->someProperty->value));
    }

    private function getModelWithLazyLoading()
    {
        $model = oxNew(Article::class);
        $model->init("oxarticles");

        $model->setId('2000');
        $model->save();

        return $model;
    }

    private function getTestFieldNameOfModelWithLazyLoading()
    {
        return 'oxarticles__oxartnum';
    }
}
