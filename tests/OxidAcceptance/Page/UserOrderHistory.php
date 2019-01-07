<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

use OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Footer\UserLogin;

class UserOrderHistory extends Page
{
    use UserLogin;

    // include url of current page
    public static $URL = '/en/order-history/';

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
