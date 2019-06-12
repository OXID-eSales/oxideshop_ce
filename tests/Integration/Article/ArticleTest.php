<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Article;

use oxField;

/**
 * oxArticle integration test
 */
class ArticleTest extends \OxidTestCase
{
    /**
     * Test setup
     */
    public function setUp()
    {
        parent::setUp();
        $this->addTableForCleanup('oxarticles');
    }

    public function testArticleParentFieldsInChild_ParentUpdate_SetParentValueToChild()
    {
        $aParentFields = array('oxarticles__oxnonmaterial',
                               'oxarticles__oxfreeshipping',
                               'oxarticles__oxisdownloadable');

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleParent');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleChild1');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleChild2');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->load('_testArticleParent');
        foreach ($aParentFields as $sField) {
            $oProduct->$sField = new oxField(1);
        }
        $oProduct->save();

        $oProductChild1 = oxNew('oxArticle');
        $oProductChild1->load('_testArticleChild1');

        $oProductChild2 = oxNew('oxArticle');
        $oProductChild2->load('_testArticleChild2');

        foreach ($aParentFields as $sField) {
            $this->assertEquals(1, $oProductChild1->$sField->value);
            $this->assertEquals(1, $oProductChild2->$sField->value);
        }
    }

    public function testArticleParentFieldsInChild_AddChild_ChildTakeParentValue()
    {
        $aParentFields = array('oxarticles__oxnonmaterial',
                               'oxarticles__oxfreeshipping',
                               'oxarticles__oxisdownloadable');

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleParent');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        foreach ($aParentFields as $sField) {
            $oProduct->$sField = new oxField(1);
        }
        $oProduct->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleChild');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        $oProductChild = oxNew('oxArticle');
        $oProductChild->load('_testArticleChild');

        foreach ($aParentFields as $sField) {
            $this->assertEquals(1, $oProductChild->$sField->value);
        }
    }


    public function testArticleParentFieldsInChild_UpdateChild_ChildTakeParentValue()
    {
        $aParentFields = array('oxarticles__oxnonmaterial',
                               'oxarticles__oxfreeshipping',
                               'oxarticles__oxisdownloadable');

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleParent');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        foreach ($aParentFields as $sField) {
            $oProduct->$sField = new oxField(1);
        }
        $oProduct->save();

        $oProduct = oxNew('oxArticle');
        $oProduct->setId('_testArticleChild');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        //values from parent
        foreach ($aParentFields as $sField) {
            $this->assertEquals(1, $oProduct->$sField->value);
        }

        // updating child
        $oProductChild = oxNew('oxArticle');
        $oProductChild->load('_testArticleChild');
        foreach ($aParentFields as $sField) {
            $oProductChild->$sField = new oxField(0);
        }
        $oProductChild->save();

        //values do not changed, from parent
        $oProductChild = oxNew('oxArticle');
        $oProductChild->load('_testArticleChild');

        foreach ($aParentFields as $sField) {
            $this->assertEquals(1, $oProductChild->$sField->value);
        }
    }
}
