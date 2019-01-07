<?php
namespace OxidEsales\EshopCommunity\Tests\OxidAcceptance\Page\Account;


interface NewsletterSettingsInterface
{
    /**
     * @return $this
     */
    public function subscribeNewsletter();

    /**
     * @return $this
     */
    public function unSubscribeNewsletter();

    /**
     * Check if newsletter is subscribed
     *
     * TODO: should it be here?
     *
     * @return $this
     */
    public function seeNewsletterSubscribed();

    /**
     * Check if newsletter is subscribed
     *
     * TODO: should it be here?
     *
     * @return $this
     */
    public function seeNewsletterUnSubscribed();

}
