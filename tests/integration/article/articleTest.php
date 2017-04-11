<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

/**
 * oxArticle integration test
 */
class Integration_Article_ArticleTest extends OxidTestCase
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

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleParent');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->save();

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleChild1');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleChild2');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        $oProduct = new oxArticle();
        $oProduct->load('_testArticleParent');
        foreach ($aParentFields as $sField) {
            $oProduct->$sField = new oxField(1);
        }
        $oProduct->save();

        $oProductChild1 = new oxArticle();
        $oProductChild1->load('_testArticleChild1');

        $oProductChild2 = new oxArticle();
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

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleParent');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        foreach ($aParentFields as $sField) {
            $oProduct->$sField = new oxField(1);
        }
        $oProduct->save();

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleChild');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        $oProductChild = new oxArticle();
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

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleParent');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        foreach ($aParentFields as $sField) {
            $oProduct->$sField = new oxField(1);
        }
        $oProduct->save();

        $oProduct = new oxArticle();
        $oProduct->setId('_testArticleChild');
        $oProduct->oxarticles__oxshopid = new oxField(1);
        $oProduct->oxarticles__oxparentid = new oxField('_testArticleParent');
        $oProduct->save();

        //values from parent
        foreach ($aParentFields as $sField) {
            $this->assertEquals(1, $oProduct->$sField->value);
        }

        // updating child
        $oProductChild = new oxArticle();
        $oProductChild->load('_testArticleChild');
        foreach ($aParentFields as $sField) {
            $oProductChild->$sField = new oxField(0);
        }
        $oProductChild->save();

        //values do not changed, from parent
        $oProductChild = new oxArticle();
        $oProductChild->load('_testArticleChild');

        foreach ($aParentFields as $sField) {
            $this->assertEquals(1, $oProductChild->$sField->value);
        }
    }

}