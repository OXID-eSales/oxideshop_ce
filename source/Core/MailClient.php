<?php
namespace OxidEsales\Eshop\Core;

/**
 * Class MailClient
 */
class MailClient extends \PHPMailer implements MailClientInterface
{
    public function __construct()
    {
        // proxy to the real PHPMailer


        //enabling exception handling in phpMailer class
        parent::__construct(true);


        $this->isHtml(true);
        $this->setLanguage('en', \oxRegistry::getConfig()->getConfigParam('sShopDir') . '/Core/phpmailer/language/');

    }
}