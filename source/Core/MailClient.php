<?php
namespace OxidEsales\Eshop\Core;

require \oxRegistry::getConfig()->getConfigParam('sCoreDir') . '/phpmailer/class.phpmailer.php';

/**
 * Class MailClient
 */
class MailClient extends \PHPMailer implements MailClientInterface
{
    public function __construct()
    {
        //enabling exception handling in phpMailer class
        parent::__construct(true);


        $this->isHtml(true);
        $this->setLanguage('en', \oxRegistry::getConfig()->getConfigParam('sShopDir') . '/Core/phpmailer/language/');

    }
}