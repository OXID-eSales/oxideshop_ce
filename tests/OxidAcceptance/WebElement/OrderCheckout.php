<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class OrderCheckout
{
    // include url of current page
    public $URL = '';

    public $billingAddress = '//div[@id="orderAddress"]/div[1]/form/div[2]/div[2]';

    public $deliveryAddress = '//div[@id="orderAddress"]/div[2]/form/div[2]/div[2]';

    public $userRemarkHeader = '//div[@class="panel panel-default orderRemarks"]/div[1]/h3';

    public $userRemark = '//div[@class="panel panel-default orderRemarks"]/div[2]';

}
