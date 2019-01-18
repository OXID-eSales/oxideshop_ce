<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

/**
 * @internal Do not make a module extension for this class.
 *
 * Email builder class
 */
abstract class EmailBuilder
{
    protected $buildParam = null;

    /**
     * Set configuration first, build and return the email after.
     *
     * @param mixed $buildParam
     *
     * @return Email
     */
    public function build($buildParam = null)
    {
        $this->buildParam = $buildParam;

        return $this->buildEmail();
    }

    /**
     * Builds and returns the email object
     *
     * @return \OxidEsales\Eshop\Core\Email
     */
    protected function buildEmail()
    {
        $email = $this->getEmailObject();

        $email->setSubject($this->getSubject());
        $email->setRecipient($this->getRecipient());
        $email->setFrom($this->getSender());
        $email->setBody($this->getBody());

        return $email;
    }

    /**
     * @return \OxidEsales\Eshop\Core\Email
     */
    protected function getEmailObject()
    {
        return oxNew(\OxidEsales\Eshop\Core\Email::class);
    }

    /**
     * Prepare and get recipient address
     *
     * @return string
     */
    protected function getRecipient()
    {
        return $this->getShopInfoAddress();
    }

    /**
     * Prepare and get sender address
     *
     * @return string
     */
    protected function getSender()
    {
        return $this->getShopInfoAddress();
    }

    /**
     * Prepare and get subject
     *
     * @return string
     */
    protected function getSubject()
    {
        return '';
    }

    /**
     * Prepare and get body
     *
     * @return string
     */
    protected function getBody()
    {
        return '';
    }

    /**
     * Returns active shop info email address.
     *
     * @return string
     */
    protected function getShopInfoAddress()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();
        $activeShop = $config->getActiveShop();
        return $activeShop->getFieldData('oxinfoemail');
    }

    /**
     * Returns the message with email origin information.
     *
     * @return string
     */
    protected function getEmailOriginMessage()
    {
        $lang = \OxidEsales\Eshop\Core\Registry::getLang();
        $shopUrl = \OxidEsales\Eshop\Core\Registry::getConfig()->getConfigParam('sShopURL');

        return "<br>" . sprintf(
            $lang->translateString(
                'SHOP_EMAIL_ORIGIN_MESSAGE',
                null,
                true
            ),
            $shopUrl
        );
    }
}
