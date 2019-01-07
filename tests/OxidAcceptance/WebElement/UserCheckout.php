<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class UserCheckout
{
    // include url of current page
    public $URL = '';

    public $noRegistrationOption = '//div[@id="optionNoRegistration"]/div/button';

    public $registrationOption = '//div[@id="optionRegistration"]/div[3]/button';

    public $openShipAddressForm = '#showShipAddress';

    public $orderRemark = '#orderRemark';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    //save form button
    public $nextStepButton = '#userNextStepBottom';

}
