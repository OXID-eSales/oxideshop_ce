<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement\Account;

class NewsletterSettings
{
    // include url of current page
    public $URL = '/index.php?lang=1&cl=account_newsletter';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $newsletterStatusSelect = '//button[@data-id="status"]';

    public $newsletterSubscribeButton = '#newsletterSettingsSave';
}
