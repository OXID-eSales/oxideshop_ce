<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page;

class ProductSuggestion extends Page
{
    protected $webElementName = 'WebElement\ProductSuggestion';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public function route($param)
    {
        return $this->webElement->URL.'/index.php?'.http_build_query(['anid' => $param]);
    }

    /**
     * @param array $suggestionEmailData
     *
     * @return $this
     */
    public function sendSuggestionEmail($suggestionEmailData)
    {
        $I = $this->user;
        $I->fillField($this->webElement->recipientName, $suggestionEmailData['recipient_name']);
        $I->fillField($this->webElement->recipientEmail, $suggestionEmailData['recipient_email']);
        $I->fillField($this->webElement->senderName, $suggestionEmailData['sender_name']);
        $I->fillField($this->webElement->senderEmail, $suggestionEmailData['sender_email']);
        if (isset($suggestionEmailData['message'])) {
            $I->fillField($this->webElement->emailMessage, $suggestionEmailData['message']);
        }
        if (isset($suggestionEmailData['subject'])) {
            $I->fillField($this->webElement->emailSubject, $suggestionEmailData['subject']);
        }
        $I->click($I->translate('SEND'));
        return $this;
    }

}
