<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class ProductCompare
{
    // include url of current page
    public $URL = '/en/my-product-comparison/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $productTitle = '//tr[@class="products"]/td[%s]/div[2]/strong/a';

    public $productNumber = '//tr[@class="products"]/td[%s]/div[2]/span';

    public $productPrice = '//tr[@class="products"]/td[%s]/div[2]/form[1]/div[2]/div[1]/span[1]';

    public $attributeName = '//div[@id="compareLandscape"]/table/tbody/tr[%s]/th';

    public $attributeValue = '//div[@id="compareLandscape"]/table/tbody/tr[%s]/td[%s]';

    public $rightArrow = '#compareRight_%s';

    public $leftArrow = '#compareLeft_%s';

    public $removeButton = '#remove_cmp_%s';
}
