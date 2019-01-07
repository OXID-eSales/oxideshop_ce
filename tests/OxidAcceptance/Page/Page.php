<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

class Page
{
    protected $user;

    protected $webElement;

    protected $webElementName = 'WebElement\Base';

    public function __construct(\AcceptanceTester $I)
    {
        $this->user = $I;
        $this->webElement = $I->grabService($this->webElementName);
    }

    /**
     * TODO: Do we need it?
     * @param $property
     * @return mixed
     */
    public function __get($property)
    {
        if (property_exists($this->webElement, $property)) {
            return $this->webElement->$property;
        }
    }

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param)
    {
        return $this->webElement->URL.$param;
    }

}
