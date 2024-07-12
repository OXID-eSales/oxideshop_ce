<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use oxDb;
use OxidEsales\Eshop\Application\Model\ManufacturerList;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Tests\FieldTestingTrait;
use oxRegistry;
use oxTestModules;

class ManufacturerlistTest extends \OxidTestCase
{
    use FieldTestingTrait;

    protected function tearDown(): void
    {
        oxTestModules::addFunction('oxManufacturer', 'cleanRootManufacturer', '{oxManufacturer::$_aRootManufacturer = array();}');
        oxNew('oxManufacturer')->cleanRootManufacturer();

        parent::tearDown();
    }

    /**
     * Test loading simple Manufacturer list by selected language
     */
    public function testLoadManufacturerListByLanguage(): void
    {
        Registry::getLang()->setBaseLanguage(1);
        $list = oxNew(ManufacturerList::class);
        $list->loadManufacturerList();
        $this->assertTrue((count($list) > 0), 'Manufacturers list not loaded');
        $resultSet = oxDb::getDB()
            ->select(
                'select oxid, oxtitle_1, oxshortdesc_1 from oxmanufacturers where oxmanufacturers.oxshopid = "'
                . Registry::getConfig()->getShopID()
                . '"'
            );
        if (!$resultSet || !$resultSet->count()) {
            $this->fail('No records found in Manufacturers table with lang id = 1');
        }

        while (!$resultSet->EOF) {
            $this->assertEquals($resultSet->fields[1], $list[$resultSet->fields[0]]->oxmanufacturers__oxtitle->value);
            $this->assertEquals(
                $this->encode($resultSet->fields[2]),
                $list[$resultSet->fields[0]]->oxmanufacturers__oxshortdesc->value
            );
            $resultSet->fetchRow();
        }
    }

    /**
     * Test loading simple Manufacturer list and counting Manufacturer articles
     */
    public function testLoadManufacturerListAndCountManufacturerArticles()
    {
        $myUtils = oxRegistry::getUtils();

        $this->getConfig()->setConfigParam('bl_perfShowActionCatArticleCnt', true);

        $oManufacturerlist = oxNew('oxManufacturerlist');
        $oManufacturerlist->setShowManufacturerArticleCnt(true);
        $oManufacturerlist->loadManufacturerList();

        foreach ($oManufacturerlist as $sVndId => $value) {
            $iArtCount = $oManufacturerlist[$sVndId]->oxmanufacturers__oxnrofarticles->value;
            $this->assertTrue(($iArtCount > 0), "Manufacturer articles were not counted");
        }
    }

    /**
     * Test creating root for Manufacturer tree, and adding category list fields for each Manufacturer item
     */
    public function testBuildManufacturerTree()
    {
        $myConfig = $this->getConfig();
        $myDB = oxDb::getDB();

        $oManufacturerlist = $this->getProxyClass("oxManufacturerList"); //oxNew('oxManufacturerlist', 'core');

        // get first Manufacturer id
        $sQ = 'select oxid from oxmanufacturers where oxmanufacturers.oxshopid = "' . $myConfig->getShopID() . ' "';
        $sFirstManufacturerId = $myDB->getOne($sQ);

        // build Manufacturers and add first Manufacturer to Manufacturers tree path array
        $oManufacturerlist->buildManufacturerTree('manufacturerList', $sFirstManufacturerId, $myConfig->getShopHomeURL());

        //check if root for Manufacturers tree was added
        $aPath = $oManufacturerlist->getPath();


        $this->assertNotNull($oManufacturerlist->getClickManufacturer());
        $this->assertEquals($sFirstManufacturerId, $oManufacturerlist->getClickManufacturer()->getId());
        $this->assertEquals($aPath[0], $oManufacturerlist->getRootCat());
        $this->assertEquals('root', $aPath[0]->getId(), 'Not added root for Manufacturer tree'); //oxManufacturer__oxid->value

        //check if first Manufacturer was added to Manufacturers tree path array
        $this->assertEquals($sFirstManufacturerId, $aPath[1]->getId(), 'Manufacturer was not added to Manufacturers tree path');

        //check if category list fields was added for each Manufacturer item
        foreach ($oManufacturerlist as $sVndId => $value) {
            if (empty($oManufacturerlist[$sVndId]->oxcategories__oxid->value)) {
                $this->fail('Category list fields was not added for each Manufacturer');
            }
        }
    }

    /**
     * Test adding category specific fields to Manufacturer object
     */
    public function testAddCategoryFields()
    {
        oxTestModules::addFunction("oxutils", "seoIsActive", "{return false;}");
        $myConfig = $this->getConfig();

        $oManufacturerlist = oxNew('oxManufacturerlist');
        $oManufacturerlist->loadManufacturerList();
        $oManufacturer = $oManufacturerlist->current();

        $oManufacturerlist->addCategoryFields($oManufacturer);

        // check if category specific fields was added to Manufacturer object
        $this->assertEquals($oManufacturer->getId(), $oManufacturer->oxcategories__oxid->value);
        $this->assertEquals($oManufacturer->oxmanufacturers__oxicon, $oManufacturer->oxcategories__oxicon);
        $this->assertEquals($oManufacturer->oxmanufacturers__oxtitle, $oManufacturer->oxcategories__oxtitle);
        $this->assertEquals($oManufacturer->oxmanufacturers__oxshortdesc, $oManufacturer->oxcategories__oxdesc);
        $this->assertEquals($myConfig->getShopHomeURL() . "cl=manufacturerlist&amp;mnid={$oManufacturer->oxcategories__oxid->value}", $oManufacturer->getLink());

        $this->assertTrue($oManufacturer->getIsVisible());
        $this->assertFalse($oManufacturer->hasVisibleSubCats);
    }

    /**
     * Test adding SEO links to Manufacturer object
     */
    public function testSEOsetManufacturerData()
    {
        $oManufacturerlist = $this->getProxyClass("oxManufacturerlist");
        $oManufacturerlist->loadManufacturerList();

        $oManufacturerlist->SeosetManufacturerData();

        //check if SEO link was added for each Manufacturer item
        foreach ($oManufacturerlist as $sVndId => $value) {
            $sManufacturerLink = $oManufacturerlist[$sVndId]->link;
            if (!$sManufacturerLink || str_contains($sManufacturerLink, 'index.php')) {
                $this->fail("SEO link was not added to Manufacturer object ({$sManufacturerLink})");
            }
        }
    }
}
