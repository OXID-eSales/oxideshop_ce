<?php
namespace Page;

class ThankYou extends Page
{
    // include url of current page
    public static $URL = '';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

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
