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
 * Testing oxArticle class.
 */
class Unit_Core_oxArticleTest extends OxidTestCase
{

    public function testHasSortingFieldsChangedWhenNoFieldsWereChanged()
    {
        $oArticle = new oxArticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("title", oxField::T_RAW);
        $oArticle->save();

        $oArticle = new oxArticle();
        $oArticle->load('_testArticleId');
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenSortingFieldsWereChanged()
    {
        $this->getConfig()->setConfigParam('aSortCols', array('oxtitle', 'oxprice'));

        $oArticle = new oxArticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("title", oxField::T_RAW);
        $oArticle->save();

        $oArticle = new oxArticle();
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField("NewTitle", oxField::T_RAW);
        $this->assertTrue($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenSortingFieldValueSetToTheSameOne()
    {
        $this->getConfig()->setConfigParam('aSortCols', array('oxtitle', 'oxprice'));

        $oArticle = new oxArticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10);
        $oArticle->oxarticles__oxtitle = new oxField("title");
        $oArticle->save();

        $oArticle = new oxArticle();
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField("title");
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenNonSortingFieldChanged()
    {
        $this->getConfig()->setConfigParam('aSortCols', array('oxprice'));

        $oArticle = new oxArticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10);
        $oArticle->oxarticles__oxtitle = new oxField("title");
        $oArticle->save();

        $oArticle = new oxArticle();
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxtitle = new oxField("changed title");
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }

    public function testHasSortingFieldsChangedWhenNoSortingFieldsSet()
    {
        $this->getConfig()->setConfigParam('aSortCols', '');

        $oArticle = new oxArticle();
        $oArticle->setId('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(10, oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField("title", oxField::T_RAW);
        $oArticle->save();

        $oArticle = new oxArticle();
        $oArticle->load('_testArticleId');
        $oArticle->oxarticles__oxprice = new oxField(100, oxField::T_RAW);
        $this->assertFalse($oArticle->hasSortingFieldsChanged());
    }
}

