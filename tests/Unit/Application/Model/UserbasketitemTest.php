<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use OxidEsales\EshopCommunity\Application\Model\Article;
use \oxArticle;
use oxArticleException;
use \oxField;
use \stdclass;

class modOxArticle_oxUserBasketItem extends oxArticle
{
    public function getClassVar($sName)
    {
        return $this->$sName;
    }

    public function setClassVar($sName, $sVal)
    {
        return $this->$sName = $sVal;
    }
}

class UserbasketitemTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oArticle = oxNew('oxArticle');
        $oArticle->setId('xxx');
        $oArticle->oxarticles__oxshopid = new oxField($this->getConfig()->getBaseShopId(), oxField::T_RAW);
        $oArticle->oxarticles__oxparentid = new oxField('2000', oxField::T_RAW);
        $oArticle->oxarticles__oxvarselect = new oxField('yyy', oxField::T_RAW);
        $oArticle->oxarticles__oxtitle = new oxField('xxx', oxField::T_RAW);
        $oArticle->save();

        $oSel = oxNew('oxbase');
        $oSel->init('oxselectlist');
        $oSel->setId('xxx');
        $oSel->oxselectlist__oxvaldesc = new oxField('S, 10!P!10__@@M, 20!P!20__@@L, 30!P!30__@@', oxField::T_RAW);
        $oSel->save();

        $oSel = oxNew('oxbase');
        $oSel->init('oxselectlist');
        $oSel->setId('yyy');
        $oSel->oxselectlist__oxvaldesc = new oxField('R, 10!P!10%__@@G, 20!P!20%__@@B, 30!P!30%__@@', oxField::T_RAW);
        $oSel->save();

        $oO2Sel = oxNew('oxbase');
        $oO2Sel->init('oxobject2selectlist');
        $oO2Sel->oxobject2selectlist__oxobjectid = new oxField('xxx', oxField::T_RAW);
        $oO2Sel->oxobject2selectlist__oxselnid = new oxField('xxx', oxField::T_RAW);
        $oO2Sel->oxobject2selectlist__oxsort = new oxField(20, oxField::T_RAW);
        $oO2Sel->save();

        $oO2Sel = oxNew('oxbase');
        $oO2Sel->init('oxobject2selectlist');
        $oO2Sel->oxobject2selectlist__oxobjectid = new oxField('xxx', oxField::T_RAW);
        $oO2Sel->oxobject2selectlist__oxselnid = new oxField('yyy', oxField::T_RAW);
        $oO2Sel->oxobject2selectlist__oxsort = new oxField(10, oxField::T_RAW);
        $oO2Sel->save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        oxRemClassModule('modOxArticle_oxUserBasketItem');

        $oArticle = oxNew('oxArticle');
        $oArticle->delete('xxx');

        $oSel = oxNew('oxbase');
        $oSel->init('oxselectlist');
        $oSel->delete('xxx');
        $oSel->delete('yyy');

        parent::tearDown();
    }

    public function testSetFieldData()
    {
        $oUserBasketItem = oxNew('oxuserbasketitem');

        $sValue = '<script>alert("oxid");</script>';

        $oUserBasketItem->UNITsetFieldData('oxuserbasketitems__oxsellist', $sValue);
        $this->assertEquals($sValue, $oUserBasketItem->oxuserbasketitems__oxsellist->value);
        $oUserBasketItem->UNITsetFieldData('oxsellist', $sValue);
        $this->assertEquals($sValue, $oUserBasketItem->oxuserbasketitems__oxsellist->value);
        $oUserBasketItem->UNITsetFieldData('oxuserbasketitems__oxtestfield', $sValue);
        $this->assertEquals(htmlentities($sValue, ENT_QUOTES, 'UTF-8'), $oUserBasketItem->oxuserbasketitems__oxtestfield->value);
        $oUserBasketItem->UNITsetFieldData('oxuserbasketitems__oxtestfield', $sValue, oxField::T_RAW);
        $this->assertEquals($sValue, $oUserBasketItem->oxuserbasketitems__oxtestfield->value);
    }

    public function testSelectionListSetterGetter()
    {
        $aData = array("t");

        $oUserBasketItem = oxNew('oxuserbasketitem');

        // no data for empty object
        $this->assertNull($oUserBasketItem->getSelList());

        // setting some list data
        $oUserBasketItem->setSelList($aData);
        $this->assertEquals($aData, $oUserBasketItem->getSelList());

        // chekcing direct value
        $this->assertEquals(serialize($aData), $oUserBasketItem->oxuserbasketitems__oxsellist->value);
    }

    public function testPersParamSetterGetter()
    {
        $aData = array("p");

        $oUserBasketItem = oxNew('oxuserbasketitem');

        // no data for empty object
        $this->assertNull($oUserBasketItem->getPersParams());

        // setting some list data
        $oUserBasketItem->setPersParams($aData);
        $this->assertEquals($aData, $oUserBasketItem->getPersParams());

        // chekcing direct value
        $this->assertEquals(serialize($aData), $oUserBasketItem->oxuserbasketitems__oxpersparam->value);
    }

    /**
     * Testing if serialization skips real article
     */
    public function testSleep()
    {
        $oBasketItem = $this->getProxyClass('oxuserbasketitem');
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('1126', oxField::T_RAW);

        $oBasketArticle = $oBasketItem->getArticle('xxx');
        $oArticle = $oBasketItem->getNonPublicVar('_oArticle');

        // testing if article is set
        $this->assertNotNull($oArticle);

        // testing if they are equals
        $this->assertEquals($oBasketArticle, $oArticle);

        $sSerialized = serialize($oBasketItem);
        $oUnserialized = unserialize($sSerialized);

        // testin if item is set
        $oArticle = $oUnserialized->getNonPublicVar('_oArticle');
        $this->assertNull($oArticle);
    }

    /**
     * Testing article getter
     */
    // no article is set, exception is thrown
    public function testGetArticleNoarticleIsSet()
    {
        $oBasketItem = oxNew('oxuserbasketitem');
        try {
            $oBasketItem->getArticle("");
        } catch (\OxidEsales\EshopCommunity\Core\Exception\ArticleException $oEx) {
            $this->assertEquals('EXCEPTION_ARTICLE_NOPRODUCTID', $oEx->getMessage());

            return;
        }
        $this->fail('failed testing article getter');
    }

    // trying to get non existing article
    public function testGetArticleNonExisting()
    {
        $oBasketItem = oxNew('oxuserbasketitem');
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('nothing', oxField::T_RAW);
        $this->assertFalse($oBasketItem->getArticle("xxx"));
    }

    // testing if getter returns article we expect
    public function testGetArticleTestingIfGetterWorksFine()
    {
        $sProductId = "2000";

        $oBasketItem = oxNew('oxuserbasketitem');
        $oBasketItem->setVariantParentBuyable(true);
        $oBasketItem->oxuserbasketitems__oxartid = new oxField($sProductId, oxField::T_RAW);

        $oArticle = $oBasketItem->getArticle("123");
        $this->assertTrue($oArticle instanceof article);
        $this->assertEquals($oArticle->getItemKey(), "123");

        // if thi line one day will faile, probebly becaus these parameters are not public any more :)
        // removed due to #4178
        //$this->assertFalse( $oArticle->getClassVar('_blLoadVariants') );
    }

    // testing article title formatting - article has NO parent
    public function testGetArticleTitleFormatterArticleHasNoParent()
    {
        $oArticle = oxNew('oxi18n');
        $oArticle->init('oxarticles');
        $oArticle->load('xxx');
        $oArticle->oxarticles__oxparentid = new oxField(null, oxField::T_RAW);
        $oArticle->save();

        $oArticle = oxNew('oxArticle');
        $oArticle->load('xxx');

        $oBasketItem = oxNew('oxuserbasketitem');
        $oBasketItem->oxuserbasketitems__oxartid = new oxField('xxx', oxField::T_RAW);
        $oArticle = $oBasketItem->getArticle('xxx');

        $this->assertEquals('xxx', $oArticle->oxarticles__oxtitle->value);
        $aSelectList = $oArticle->getDispSelList();
        $this->assertFalse(isset($aSelectList));
    }

    public function testGetArticleSelectListTesting()
    {
        $this->getConfig()->setConfigParam('bl_perfLoadSelectLists', true);

        $oBasketItem = oxNew('oxuserbasketitem');
        $oBasketItem->oxuserbasketitems__oxartid = new oxField("xxx", oxField::T_RAW);
        $oBasketItem->oxuserbasketitems__oxsellist = new oxField(serialize(array(0, 1)), oxField::T_RAW);

        $oArticle = $oBasketItem->getArticle("123");

        $oR = new stdclass();
        $oR->name = 'R, 10';
        $oR->value = '';
        $oR->selected = 1;

        $oG = new stdclass();
        $oG->name = 'G, 20';
        $oG->value = '';

        $oB = new stdclass();
        $oB->name = 'B, 30';
        $oB->value = '';

        $oS = new stdclass();
        $oS->name = 'S, 10';
        $oS->value = '';

        $oM = new stdclass();
        $oM->name = 'M, 20';
        $oM->value = '';
        $oM->selected = 1;

        $oL = new stdclass();
        $oL->name = 'L, 30';
        $oL->value = '';

        $aSel[] = array($oR, $oG, $oB, 'name' => '');
        $aSel[] = array($oS, $oM, $oL, 'name' => '');

        // if this assertion will fail, probably due to protected variable
        $this->assertEquals($aSel, $oArticle->getDispSelList());
    }
}
