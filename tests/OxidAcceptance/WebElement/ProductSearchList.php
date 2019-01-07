<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class ProductSearchList
{
    // include url of current page
    public $URL = '';

    public $listItemTitle = '#searchList_%s';

    public $listItemDescription = '//form[@name="tobasketsearchList_%s"]/div[2]/div[2]/div/div[@class="shortdesc"]';

    public $listItemPrice = '//form[@name="tobasketsearchList_%s"]/div[2]/div[2]/div/div[@class="price"]/div/span[@class="lead text-nowrap"]';

    public $variantSelection = '#variantselector_searchList_%s button';

}
