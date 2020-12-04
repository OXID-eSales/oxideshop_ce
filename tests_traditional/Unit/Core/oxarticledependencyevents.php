<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

/**
 * Testing oxArticle class.
 */
class Unit_Core_oxArticleTest extends OxidTestCase
{
    public function testHasSortingFieldsChangedWhenNoFieldsWereChanged()
    {
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("title", oxField::T_RAW);
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenSortingFieldsWereChanged()
    {
        $this->getConfig()->setConfigParam('aSortCols', array('oxtitle', 'oxprice'));

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("title", oxField::T_RAW);
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField("NewTitle", oxField::T_RAW);
        $this->assertTrue($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenSortingFieldValueSetToTheSameOne()
    {
        $this->getConfig()->setConfigParam('aSortCols', array('oxtitle', 'oxprice'));

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10);
        $oArticle->oxarticles__oxtitle = new oxField("title");
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField("title");
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenNonSortingFieldChanged()
    {
        $this->getConfig()->setConfigParam('aSortCols', array('oxprice'));

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10);
        $oArticle->oxarticles__oxtitle = new oxField("title");
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField("changed title");
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenNoSortingFieldsSet()
    {
        $this->getConfig()->setConfigParam('aSortCols', '');

        $oArticle = oxNew('oxArticle');
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("title", oxField::T_RAW);
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(100, oxField::T_RAW);
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }
}
