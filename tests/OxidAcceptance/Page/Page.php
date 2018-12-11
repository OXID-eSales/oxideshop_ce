<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

class Page
{
    protected $user;

    public function __construct(\AcceptanceTester $I)
    {
        $this->user = $I;
    }
}
