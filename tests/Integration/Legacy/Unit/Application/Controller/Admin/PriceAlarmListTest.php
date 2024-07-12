<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use OxidEsales\Eshop\Core\TableViewNameGenerator;
use \stdClass;

/**
 * Tests for PriceAlarm_List class
 */
class PriceAlarmListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * PriceAlarm_List::BuildSelectString() test case
     */
    public function testBuildSelectString()
    {
        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sViewName = $tableViewNameGenerator->getViewName("oxpricealarm");
        $sArtViewName = $tableViewNameGenerator->getViewName("oxarticles");

        $sSql = sprintf('select %s.*, %s.oxtitle AS articletitle, ', $sViewName, $sArtViewName);
        $sSql .= "oxuser.oxlname as userlname, oxuser.oxfname as userfname ";
        $sSql .= sprintf('from %s ', $sViewName);
        $sSql .= sprintf('left join %s on %s.oxid = %s.oxartid ', $sArtViewName, $sArtViewName, $sViewName);
        $sSql .= sprintf('left join oxuser on oxuser.oxid = %s.oxuserid WHERE 1 ', $sViewName);

        // testing..
        $oView = oxNew('PriceAlarm_List');
        $this->assertSame($sSql, $oView->buildSelectString(new stdClass()));
    }

    /**
     * PriceAlarm_List::Render() test case
     */
    public function testRender()
    {
        $oView = oxNew('PriceAlarm_List');
        $this->assertSame('pricealarm_list', $oView->render());
    }

    /**
     * PriceAlarm_List::BuildWhere() test case
     */
    public function testBuildWhere()
    {
        $this->setRequestParameter('where', ["oxpricealarm" => ["oxprice" => 15], "oxarticles" => ["oxprice" => 15]]);

        $tableViewNameGenerator = oxNew(TableViewNameGenerator::class);
        $sViewName = $tableViewNameGenerator->getViewName("oxpricealarm");
        $sArtViewName = $tableViewNameGenerator->getViewName("oxarticles");

        $oView = $this->getMock(\OxidEsales\Eshop\Application\Controller\Admin\PriceAlarmList::class, ["authorize"]);
        $oView->method('authorize')->willReturn(true);
        $oView->init();

        $queryWhereParts = $oView->buildWhere();
        $this->assertSame('%15%', $queryWhereParts[$sViewName . '.oxprice']);
        $this->assertSame('%15%', $queryWhereParts[$sArtViewName . '.oxprice']);
    }
}
