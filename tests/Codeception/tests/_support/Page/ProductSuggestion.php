<?php
namespace Page;

class ProductSuggestion extends Page
{
    // include url of current page
    public static $URL = '/en/recommend/';

    // include bread crumb of current page
    public static $breadCrumb = '#breadcrumb';

    public static $headerTitle = 'h1';

    public static $recipientName = 'editval[rec_name]';

    public static $recipientEmail = 'editval[rec_email]';

    public static $senderName = 'editval[send_name]';

    public static $senderEmail = 'editval[send_email]';

    public static $emailSubject = 'editval[send_subject]';

    public static $emailMessage = 'editval[send_message]';

    /**
     * Basic route example for your current URL
     * You can append any additional parameter to URL
     * and use it in tests like: Page\Edit::route('/123-post');
     */
    public static function route($param)
    {
        return static::$URL.'/index.php?'.http_build_query(['anid' => $param]);
    }

    /**
     * @param array $suggestionEmailData
     *
     * @return $this
     */
    public function sendSuggestionEmail($suggestionEmailData)
    {
        $I = $this->user;
        $I->fillField(self::$recipientName, $suggestionEmailData['recipient_name']);
        $I->fillField(self::$recipientEmail, $suggestionEmailData['recipient_email']);
        $I->fillField(self::$senderName, $suggestionEmailData['sender_name']);
        $I->fillField(self::$senderEmail, $suggestionEmailData['sender_email']);
        if (isset($suggestionEmailData['message'])) {
            $I->fillField(self::$emailMessage, $suggestionEmailData['message']);
        }
        if (isset($suggestionEmailData['subject'])) {
            $I->fillField(self::$emailSubject, $suggestionEmailData['subject']);
        }
        $I->click($I->translate('SEND'));
        return $this;
    }

}
