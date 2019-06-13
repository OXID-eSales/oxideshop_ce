<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component;

/**
 * News list manager, loads some news informetion.
 *
 * @subpackage oxcmp
 *
 * @deprecated since v.5.3.0 (2016-06-17); The Admin Menu: Customer Info -> News feature will be moved to a
 *                                         module in v6.0.0
 *
 */
class NewsComponent extends \OxidEsales\Eshop\Core\Controller\BaseController
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

        $oActNews = oxNew(\OxidEsales\Eshop\Application\Model\NewsList::class);
        $oActNews->loadNews(0, $iNewsToLoad);

        return $oActNews;
    }
}
