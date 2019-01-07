<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;


class Basket
{
    // include url of current page
    public $URL = '';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $basketSummary = '#basketGrandTotal';

    public $basketItemAmount = '#basketcontents_table #am_%s';

    public $basketItemTotalPrice = '//tr[@id="table_cartItem_%s"]/td[@class="totalPrice"]';

    public $basketItemTitle = '//tr[@id="table_cartItem_%s"]/td[2]/div[2]/a';

    public $basketItemId = '//tr[@id="table_cartItem_%s"]/td[2]/div[2]/div[1]';

    public $basketBundledItemAmount = '//tr[@id="table_cartItem_%s"]/td[4]';

    public $basketUpdateButton = '#basketcontents_table #basketUpdate';

}
