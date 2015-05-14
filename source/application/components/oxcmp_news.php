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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * News list manager, loads some news informetion.
 *
 * @subpackage oxcmp
 */
class oxcmp_news extends oxView
{

    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Executes parent::render() and loads news list. Returns current
     * news array element (if user in admin sets to show more than 1
     * item in news box - will return whole array).
     *
     * @return array $oActNews a List of news, or null if not configured to load news
     */
    public function render()
    {
        parent::render();

        $myConfig = $this->getConfig();
        $oActView = $myConfig->getActiveView();

        // news loading is disabled
        if (!$myConfig->getConfigParam('bl_perfLoadNews') ||
            ($myConfig->getConfigParam('blDisableNavBars') &&
             $oActView->getIsOrderStep())
        ) {
            return;
        }

        // if news must be displayed only on start page ?
        if ($myConfig->getConfigParam('bl_perfLoadNewsOnlyStart') &&
            $oActView->getClassName() != "start"
        ) {
            return;
        }

        $iNewsToLoad = $myConfig->getConfigParam('sCntOfNewsLoaded');
        $iNewsToLoad = $iNewsToLoad ? $iNewsToLoad : 1;

        $oActNews = oxNew('oxnewslist');
        $oActNews->loadNews(0, $iNewsToLoad);

        return $oActNews;
    }
}
