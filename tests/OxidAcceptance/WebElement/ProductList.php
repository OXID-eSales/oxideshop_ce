<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class ProductList
{
    // include url of current page
    public $URL = '';

    public $listItemTitle = '#productList_%s';

    public $listItemDescription = '//form[@name="tobasketproductList_%s"]/div[2]/div[2]/div/div[@class="shortdesc"]';

    public $listItemPrice = '//form[@name="tobasketproductList_%s"]/div[2]/div[2]/div/div[@class="price"]/div/span[@class="lead text-nowrap"]';

}
