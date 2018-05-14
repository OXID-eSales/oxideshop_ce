<?php
namespace Page;

use Page\Footer\NewsletterBox;
use Page\Header\AccountMenu;
use Page\Header\Navigation;
use Page\Header\SearchWidget;

class Home extends Page
{
    use AccountMenu, NewsletterBox, SearchWidget, Navigation;

    // include url of current page
    public static $URL = '/';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.$param;
    }
}
