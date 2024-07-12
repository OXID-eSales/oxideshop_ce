<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller\Admin;

use \Exception;
use \oxTestModules;

final class CountryListTest extends \PHPUnit\Framework\TestCase
{

    /**
     * Country_List::DeleteEntry() test case
     */
    public function testDeleteEntry()
    {
        oxTestModules::addFunction('oxcountry', 'delete', '{ throw new Exception("delete");}');
        oxTestModules::addFunction('oxcountry', 'isDerived', '{ return false;}');
        $this->getConfig()->setConfigParam("blAllowSharedEdit", true);

        // testing..
        try {
            $oView = oxNew('Country_List');
            $oView->deleteEntry();
        } catch (Exception $exception) {
            $this->assertEquals("delete", $exception->getMessage(), "Error in Country_List::DeleteEntry()");

            return;
        }

        $this->fail("Error in Country_List::DeleteEntry()");
    }

    /**
     * Country_List::Render() test case
     */
    public function testRender()
    {
        $view = oxNew('Country_List');

        $this->assertEquals(['oxcountry' => ['oxactive' => 'asc', 'oxtitle' => 'asc']], $view->getListSorting());
        $this->assertEquals('country_list', $view->render());
    }

    /**
     * Test, that the country list adds the sorting by oxtitle, if there is only the oxactive sorting given.
     * We do this, cause mysql sees the order of the remainder (after sorting by active) as undefined.
     */
    public function testAddingSortingByTitleWhenOnlyActiveIsGiven(): void
    {
        $view = oxNew('Country_List');

        $countryList = $view->getItemList();
        $firstActiveTitle = null;
        $firstInactiveTitle = null;

        foreach ($countryList as $country) {
            $isCountryActive = (bool)$country->oxcountry__oxactive->value;

            $firstActiveTitle = $this->getTitleIfUnset($firstActiveTitle, $country, $isCountryActive);
            $firstInactiveTitle = $this->getTitleIfUnset($firstInactiveTitle, $country, !$isCountryActive);
        }

        $this->assertEquals('Deutschland', $firstActiveTitle);
        $this->assertEquals('Afghanistan', $firstInactiveTitle);
    }

    /**
     * If the item title is not set yet, take it from the country object.
     *
     * @param null|string $countryTitle The title of the first country we process.
     * @param oxCountry   $country      The first country we process.
     * @param bool        $process      Should we take this country or not, cause it was not of the correct activation status.
     *
     * @return string The title of the first country we process.
     */
    private function getTitleIfUnset($countryTitle, $country, $process)
    {
        if (is_null($countryTitle) && $process) {
            return $country->oxcountry__oxtitle->value;
        }

        return $countryTitle;
    }
}
