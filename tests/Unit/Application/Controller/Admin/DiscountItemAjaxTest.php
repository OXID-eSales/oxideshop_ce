<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Application\Controller\Admin\DiscountItemAjax;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\Eshop\Core\TableViewNameGenerator;
use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

final class DiscountItemAjaxTest extends UnitTestCase
{
    public function testGetQuery(): void
    {
        $productView = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $discountView = oxNew(TableViewNameGenerator::class)->getViewName('oxdiscount');
        $expected = "from $discountView left join $productView on $productView.oxid=$discountView.oxitmartid ";
        $expected .= " where $discountView.oxid = '_testOxid' and $discountView.oxitmartid != ''";

        $_POST['oxid'] = '_testOxid';
        $_POST['synchoxid'] = '_testOxid';

        $query = oxNew(DiscountItemAjax::class)->_getQuery();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQueryOxid(): void
    {
        $productView = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $discountView = oxNew(TableViewNameGenerator::class)->getViewName('oxdiscount');
        $objectToCategoryView = oxNew(TableViewNameGenerator::class)->getViewName('oxobject2category');
        $expected = "from $objectToCategoryView left join $productView on  $productView.oxid=$objectToCategoryView.oxobjectid ";
        $expected .= " where $objectToCategoryView.oxcatnid = '_testOxid' and $productView.oxid is not null  and ";
        $expected .= "$productView.oxvarcount = 0 and ";
        $expected .= " $productView.oxid not in (  select $productView.oxid from $discountView, $productView where $productView.oxid=$discountView.oxitmartid ";
        $expected .= " and $discountView.oxid = '_testSynchoxid' )";

        $_POST['oxid'] = '_testOxid';
        $_POST['synchoxid'] = '_testSynchoxid';
        Registry::getConfig()->setConfigParam('blVariantParentBuyable', false);

        $query = oxNew(DiscountItemAjax::class)->_getQuery();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQueryOxidParentIsBuyable(): void
    {
        $productView = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $discountView = oxNew(TableViewNameGenerator::class)->getViewName('oxdiscount');
        $objectToCategoryView = oxNew(TableViewNameGenerator::class)->getViewName('oxobject2category');
        $expected = "from $objectToCategoryView left join $productView on  $productView.oxid=$objectToCategoryView.oxobjectid ";
        $expected .= " where $objectToCategoryView.oxcatnid = '_testOxid' and $productView.oxid is not null  and ";
        $expected .= " $productView.oxid not in (  select $productView.oxid from $discountView, $productView where $productView.oxid=$discountView.oxitmartid ";
        $expected .= " and $discountView.oxid = '_testSynchoxid' )";

        $_POST['oxid'] = '_testOxid';
        $_POST['synchoxid'] = '_testSynchoxid';
        Registry::getConfig()->setConfigParam('blVariantParentBuyable', true);

        $query = oxNew(DiscountItemAjax::class)->_getQuery();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQuerySynchoxid(): void
    {
        $productView = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $discountView = oxNew(TableViewNameGenerator::class)->getViewName('oxdiscount');
        $expected = "from $productView where 1 and $productView.oxparentid = '' and $productView.oxvarcount = 0 and ";
        $expected .= " $productView.oxid not in (  select $productView.oxid from $discountView, $productView where $productView.oxid=$discountView.oxitmartid ";
        $expected .= " and $discountView.oxid = '_testSynchoxid' )";

        $_POST['synchoxid'] = '_testSynchoxid';
        Registry::getConfig()->setConfigParam('blVariantParentBuyable', false);

        $query = oxNew(DiscountItemAjax::class)->_getQuery();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQuerySynchoxidParentIsBuyable(): void
    {
        $productView = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $discountView = oxNew(TableViewNameGenerator::class)->getViewName('oxdiscount');
        $expected = "from $productView where 1 and $productView.oxparentid = ''  and ";
        $expected .= " $productView.oxid not in (  select $productView.oxid from $discountView, $productView where $productView.oxid=$discountView.oxitmartid ";
        $expected .= " and $discountView.oxid = '_testSynchoxid' )";
        $_POST['synchoxid'] = '_testSynchoxid';
        Registry::getConfig()->setConfigParam('blVariantParentBuyable', true);

        $query = oxNew(DiscountItemAjax::class)->_getQuery();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQueryCols(): void
    {
        $view = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $expected = sprintf(
            '%1$s.oxartnum as _0, %1$s.oxtitle as _1, %1$s.oxean as _2, %1$s.oxmpn as _3, %1$s.oxprice as _4, %1$s.oxstock as _5, %1$s.oxid as _6',
            $view,
        );

        $_POST['cmpid'] = $this->getContainerIdForUnassignedItemsList();
        Registry::getConfig()->setConfigParam('blVariantsSelection', false);

        $query = oxNew(DiscountItemAjax::class)->_getQueryCols();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQueryColsWithMultipleIdentifiers(): void
    {
        $columns = [
            ['oxartnum', 'oxarticles', 0, 0, 1],
            ['oxtitle', 'oxarticles', 1, 1, 0],
            ['oxean', 'oxarticles', 1, 0, 0],
            ['oxmpn', 'oxarticles', 0, 0, 0],
            ['oxprice', 'oxarticles', 0, 0, 0],
            ['oxstock', 'oxarticles', 0, 0, 0],
            ['oxid', 'oxarticles', 0, 0, 1]
        ];
        $component = oxNew(DiscountItemAjax::class);
        $component->setColumns($columns);
        $view = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $expected = sprintf(
            '%1$s.oxtitle as _1, %1$s.oxean as _2, %1$s.oxmpn as _3, %1$s.oxprice as _4, %1$s.oxstock as _5, %1$s.oxartnum as _0, %1$s.oxid as _6',
            $view,
        );

        $_POST['cmpid'] = $this->getContainerIdForUnassignedItemsList();
        Registry::getConfig()->setConfigParam('blVariantsSelection', false);

        $query = $component->_getQueryCols();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQueryColsWithVariants(): void
    {
        $view = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $expected = sprintf(
            '%1$s.oxartnum as _0,  IF( %1$s.oxtitle != \'\', %1$s.oxtitle, CONCAT((select oxart.oxtitle from %1$s as oxart where oxart.oxid = %1$s.oxparentid),\', \',%1$s.oxvarselect)) as _1, %1$s.oxean as _2, %1$s.oxmpn as _3, %1$s.oxprice as _4, %1$s.oxstock as _5, %1$s.oxid as _6',
            $view
        );

        $_POST['cmpid'] = $this->getContainerIdForUnassignedItemsList();
        Registry::getConfig()->setConfigParam('blVariantsSelection', true);

        $query = oxNew(DiscountItemAjax::class)->_getQueryCols();

        $this->assertEquals(" $expected ", $query);
    }

    public function testGetQueryColsWithDbViewsWillContainJustColumnName(): void
    {
        Registry::getConfig()->setConfigParam('blVariantsSelection', true);
        Registry::getConfig()->setConfigParam('blSkipViewUsage', false);
        $_POST['cmpid'] = $this->getContainerIdForUnassignedItemsList();
        $this->switchToALanguageWithNonZeroTag();
        $languageTag = Registry::getLang()->getLanguageTag();
        $view = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $columnName = "$view.oxvarselect";

        $query = oxNew(DiscountItemAjax::class)->_getQueryCols();

        $this->assertStringContainsString($columnName, $query);
        $this->assertStringNotContainsString("$columnName$languageTag", $query);
    }

    public function testGetQueryColsWithNoDbViewsWillContainColumnNameAndLanguageTag(): void
    {
        Registry::getConfig()->setConfigParam('blVariantsSelection', true);
        Registry::getConfig()->setConfigParam('blSkipViewUsage', true);
        $_POST['cmpid'] = $this->getContainerIdForUnassignedItemsList();
        $this->switchToALanguageWithNonZeroTag();
        $languageTag = Registry::getLang()->getLanguageTag();
        $view = oxNew(TableViewNameGenerator::class)->getViewName('oxarticles');
        $columnName = "$view.oxvarselect";

        $query = oxNew(DiscountItemAjax::class)->_getQueryCols();

        $this->assertStringContainsString("$columnName$languageTag", $query);
    }

    private function getContainerIdForUnassignedItemsList(): string
    {
        /** @see DiscountItemAjax::$_aColumns */
        return 'container1';
    }

    private function switchToALanguageWithNonZeroTag(): void
    {
        $nonDefaultLanguageId = 1;
        $language = oxNew(Language::class);
        $language->setBaseLanguage($nonDefaultLanguageId);
        Registry::set(Language::class, $language);
    }
}
