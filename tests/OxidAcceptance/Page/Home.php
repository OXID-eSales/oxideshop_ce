<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Footer\NewsletterBox;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\AccountMenu;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\MiniBasket;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\Navigation;
use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Header\SearchWidget;

class Home extends Page
{
    use AccountMenu, NewsletterBox, SearchWidget, Navigation, MiniBasket;

    // include url of current page
    public static $URL = '/';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param)
    {
        return static::$URL.$param;
    }
}
