<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Core\Model;

use OxidEsales\TestingLibrary\UnitTestCase;
use OxidEsales\Eshop\Application\Model\Article;

/**
 * Class OxidEsales\EshopCommunity\Core\Model\BaseModelTest
 */
class BaseModelTest extends UnitTestCase
{
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

    public function testLazyLoadingMagicIssetOnValueOfFieldReturnsTrueWhenFieldIsNotLoaded()
    {
        $model      = $this->getModelWithLazyLoading();
        $fieldName  = $this->getTestFieldNameOfModelWithLazyLoading();

        $this->assertTrue(isset($model->$fieldName->value));
    }

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
        $model->load(2000);

        return $model;
    }

    private function getTestFieldNameOfModelWithLazyLoading()
    {
        return 'oxarticles__oxartnum';
    }
}
