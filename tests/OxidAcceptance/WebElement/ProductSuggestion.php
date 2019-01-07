<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\WebElement;

class ProductSuggestion
{
    // include url of current page
    public $URL = '/en/recommend/';

    // include bread crumb of current page
    public $breadCrumb = '#breadcrumb';

    public $headerTitle = 'h1';

    public $recipientName = 'editval[rec_name]';

    public $recipientEmail = 'editval[rec_email]';

    public $senderName = 'editval[send_name]';

    public $senderEmail = 'editval[send_email]';

    public $emailSubject = 'editval[send_subject]';

    public $emailMessage = 'editval[send_message]';

}
