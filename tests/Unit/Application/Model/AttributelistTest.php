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
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
namespace Unit\Application\Model;

use \oxDb;
use OxidEsales\Eshop\Core\ShopIdCalculator;
use \oxRegistry;

/**
 * testing oxattributelist class.
 */
class AttributelistTest extends \OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $myDB = oxDb::getDB();

        $myDB->Execute('delete from oxattribute where oxid = "test%" ');
        $myDB->Execute('delete from oxobject2attribute where oxid = "test%" ');

        $myDB->Execute("update oxattribute set oxdisplayinbasket = 0 where oxid = '8a142c3f0b9527634.96987022' ");

        parent::tearDown();
    }

    /**
     * Test load attributes by ids.
     *
     * @return null
     */
    public function testLoadAttributesByIds()
    {
        $oAttrList = oxNew('oxAttributelist');
        $aAttributes = $oAttrList->loadAttributesByIds(array('1672'));

        $sSelect = "select oxattrid, oxvalue from oxobject2attribute where oxobjectid = '1672'";
        $rs = oxDb::getDB()->select($sSelect);
        $sSelect = "select oxtitle from oxattribute where oxid = '" . $rs->fields[0] . "'";
        $sTitle = oxDb::getDB()->getOne($sSelect);
        $this->assertEquals($rs->fields[1], $aAttributes[$rs->fields[0]]->aProd['1672']->value);
        $this->assertEquals($sTitle, $aAttributes[$rs->fields[0]]->title);
    }

    /**
     * Test load attributes by ids in other language.
     *
     * @return null
     */
    public function testLoadAttributesByIdsInOtherLang()
    {
        oxRegistry::getLang()->setBaseLanguage(1);
        $oAttrList = oxNew('oxAttributelist');
        $aAttributes = $oAttrList->loadAttributesByIds(array('1672'));

        $sSelect = "select oxattrid, oxvalue_1 from oxobject2attribute where oxobjectid = '1672'";
        $rs = oxDb::getDB()->select($sSelect);
        $sSelect = "select oxtitle_1 from oxattribute where oxid = '" . $rs->fields[0] . "'";
        $sTitle = oxDb::getDB()->getOne($sSelect);
        $this->assertEquals($rs->fields[1], $aAttributes[$rs->fields[0]]->aProd['1672']->value);
        $this->assertEquals($sTitle, $aAttributes[$rs->fields[0]]->title);
    }

    /**
     * Test load attributes by ids with empty array.
     *
     * @return null
     */
    public function testLoadAttributesByIdsNoIds()
    {
        $oAttrList = oxNew('oxAttributelist');
        $aAttributes = $oAttrList->loadAttributesByIds(null);

        $this->assertNull($aAttributes);
    }

    /**
     * Test load attributes.
     *
     * @return null
     */
    public function testLoadAttributes()
    {
        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributes('1672');
        $sSelect = "select oxattrid from oxobject2attribute where oxobjectid = '$sArtID'";
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = "select oxvalue from oxobject2attribute where oxattrid = '$sID' and oxobjectid = '$sArtID'";
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $sAttribValue = $oAttrList[$sID]->oxobject2attribute__oxvalue->value;
        $this->assertEquals($sExpectedValue, $sAttribValue);
    }

    /**
     * Test load attributes.
     *
     * @return null
     */
    public function testLoadAttributesWithParent()
    {
        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributes('1672', '1351');
        $this->assertEquals(9, $oAttrList->count());
    }


    /**
     * Test load displayable in basket/order attributes.
     *
     * @return null
     */
    public function testLoadAttributesDisplayableInBasket()
    {
        $sSelect = "update oxattribute set oxdisplayinbasket = 1 where oxid = '8a142c3f0b9527634.96987022' ";
        oxDb::getDB()->execute($sSelect);
        $sSelect = "update oxattribute set oxdisplayinbasket = 1 where oxid = 'd8842e3b7c5e108c1.63072778' ";
        oxDb::getDB()->execute($sSelect);

        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributesDisplayableInBasket('1672', '1351');
        $sAttribValue = $oAttrList['8a142c3f0c0baa3f4.54955953']->oxattribute__oxvalue->rawValue;
        $sAttribParentValue = $oAttrList['d8842e3b7d4e7acb1.34583879']->oxattribute__oxvalue->rawValue;
        $this->assertEquals('25 cm', $sAttribValue);
        $this->assertEquals('Granit', $sAttribParentValue);
    }

    /**
     * Test load displayable in basket/order attributes, when all are not displayable.
     *
     * @return null
     */
    public function testLoadAttributesDisplayableInBasketNoAttributes()
    {
        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributesDisplayableInBasket('1672');
        $this->assertEquals(0, count($oAttrList));

    }


    /**
     * Test load attributes in other language.
     *
     * @return null
     */
    public function testLoadAttributesInOtherLang()
    {
        oxRegistry::getLang()->setBaseLanguage(1);
        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributes('1672');
        $sSelect = "select oxattrid from oxobject2attribute where oxobjectid = '$sArtID'";
        $sID = oxDb::getDB()->getOne($sSelect);
        $sSelect = "select oxvalue_1 from oxobject2attribute where oxattrid = '$sID' and oxobjectid = '$sArtID'";
        $sExpectedValue = oxDb::getDB()->getOne($sSelect);
        $sAttribValue = $oAttrList[$sID]->oxobject2attribute__oxvalue->value;
        $this->assertEquals($sExpectedValue, $sAttribValue);
    }

    /**
     * Test load attributes with sorting.
     */
    public function testLoadAttributesWithSort()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        oxRegistry::getLang()->setBaseLanguage(0);

        $myDB = oxDb::getDB();

        $sSql = "insert into oxattribute (oxid, oxshopid, oxtitle, oxpos ) values ('test3', ".ShopIdCalculator::BASE_SHOP_ID.", 'test3', '3'), ('test1', ".ShopIdCalculator::BASE_SHOP_ID.", 'test1', '1'), ('test2', ".ShopIdCalculator::BASE_SHOP_ID.", 'test2', '2')";
        $myDB->execute($sSql);

        $sArtId = 'testArt';
        $sSql = "insert into oxobject2attribute (oxid, oxobjectid, oxattrid, oxvalue ) values ('test3', '$sArtId', 'test3', '3'), ('test1', '$sArtId', 'test1', '1'), ('test2', '$sArtId', 'test2', '2')";
        $myDB->execute($sSql);

        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributes($sArtId);
        $iCnt = 1;
        foreach ($oAttrList as $sId => $aAttr) {
            $this->assertEquals('test' . $iCnt, $sId);
            $this->assertEquals((string) $iCnt, $aAttr->oxattribute__oxvalue->value);
            $iCnt++;
        }
    }

    /**
     * Test load attributes with empty article id.
     *
     * @return null
     */
    public function testLoadAttributesEmptyId()
    {
        $oAttrList = oxNew('oxAttributelist');
        $oAttrList->loadAttributes(null);

        $this->assertEquals(0, count($oAttrList));
    }

    public function testGetCategoryAttributes()
    {
        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $this->markTestSkipped('This test is for Community or Professional edition only.');
        }

        $sCategoryId = '8a142c3e60a535f16.78077188';
        $sAttributeId = '8a142c3e9cd961518.80299776';

        $myDB = oxDb::getDb();
        $myDB->Execute('insert into oxcategory2attribute (oxid, oxobjectid, oxattrid, oxsort) values ("test3","' . $sCategoryId . '","' . $sAttributeId . '", "333")');

        $oAttrList = oxNew("oxattributelist");
        $oAttrList->getCategoryAttributes($sCategoryId, 1);
        $oAttribute = $oAttrList->offsetGet($sAttributeId);

        $this->assertEquals(1, $oAttrList->count());
        $this->assertEquals(6, count($oAttribute->getValues()));
    }

}
