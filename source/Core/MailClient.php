<?php
namespace OxidEsales\Eshop\Core;

/**
 * Class MailClient
 */
class MailClient implements MailClientInterface
{
    /**
     * Default Smtp server port
     *
     * @var int
     */
    public $SMTP_PORT = 25;

    /**
     * Shop object
     *
     * @var object
     */
    protected $_oShop;

    public function __construct()
    {
        // !! proxy to the real PHPMailer !!

        $this->mailer = new \PHPMailer(true);

        $this->setSmtp();

        $this->mailer->isHtml(true);
        $this->mailer->set('CharSet', 'utf-8');

    }

    private $_oConfig;
    /**
     * oxConfig instance getter
     *
     * @return \oxConfig
     */
    public function getConfig()
    {
        if ($this->_oConfig == null) {
            $this->_oConfig = \oxRegistry::getConfig();
        }

        return $this->_oConfig;
    }

    /**
     * Sets SMTP mailer parameters, such as user name, password, location.
     *
     * @param \oxShop $oShop Object, that keeps base shop info
     *
     * @return null
     */
    public function setSmtp()
    {
        $myConfig = $this->getConfig();
        $oShop = $this->_getShop();


        $this->mailer->setFrom($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());


        $sSmtpUrl = $this->_setSmtpProtocol($oShop->oxshops__oxsmtp->value);

        if (!$this->_isValidSmtpHost($sSmtpUrl)) {
            $this->mailer->set('mail', 'mail');

            return;
        }

        $this->mailer->set('Host', $sSmtpUrl);
        $this->mailer->set('mail', 'smtp');
        $this->mailer->set('WordWrap', 100);

        if ($oShop->oxshops__oxsmtpuser->value) {
            $this->_setSmtpAuthInfo($oShop->oxshops__oxsmtpuser->value, $oShop->oxshops__oxsmtppwd->value);
        }

        if ($myConfig->getConfigParam('iDebug') == 6) {
            $this->mailer->set('SMTPDebug', true);
        }
    }

    /**
     * Get active shop and set global params for it
     * If is set language parameter, load shop in given language
     *
     * @param int $iLangId language id
     * @param int $iShopId shop id
     *
     * @return \oxShop
     */
    protected function _getShop($iLangId = null, $iShopId = null)
    {
        if ($iLangId === null && $iShopId === null) {
            if (isset($this->_oShop)) {
                return $this->_oShop;
            } else {
                return $this->_oShop = $this->getConfig()->getActiveShop();
            }
        }

        $myConfig = $this->getConfig();

        $oShop = oxNew('oxShop');
        if ($iShopId !== null) {
            $oShop->setShopId($iShopId);
        }
        if ($iLangId !== null) {
            $oShop->setLanguage($iLangId);
        }
        $oShop->load($myConfig->getShopId());

        return $oShop;
    }


    /**
     * Sets smtp parameters depending on the protocol used
     * returns smtp url which should be used for fsockopen
     *
     * @param string $sUrl initial smtp
     *
     * @return string
     */
    protected function _setSmtpProtocol($sUrl)
    {
        $sProtocol = '';
        $sSmtpHost = $sUrl;
        $aMatch = array();
        if (getStr()->preg_match('@^([0-9a-z]+://)?(.*)$@i', $sUrl, $aMatch)) {
            if ($aMatch[1]) {
                if (($aMatch[1] == 'ssl://') || ($aMatch[1] == 'tls://')) {
                    $this->mailer->set('SMTPSecure', substr($aMatch[1], 0, 3));
                } else {
                    $sProtocol = $aMatch[1];
                }
            }
            $sSmtpHost = $aMatch[2];
        }

        return $sProtocol . $sSmtpHost;
    }

    /**
     * Checks if smtp host is valid (tries to connect to it)
     *
     * @param string $sSmtpHost currently used smtp server host name
     *
     * @return bool
     */
    protected function _isValidSmtpHost($sSmtpHost)
    {
        $blIsSmtp = false;
        if ($sSmtpHost) {
            $sSmtpPort = $this->SMTP_PORT;
            $aMatch = array();
            if (getStr()->preg_match('@^(.*?)(:([0-9]+))?$@i', $sSmtpHost, $aMatch)) {
                $sSmtpHost = $aMatch[1];
                $sSmtpPort = (int) $aMatch[3];
                if (!$sSmtpPort) {
                    $sSmtpPort = $this->SMTP_PORT;
                }
            }
            if ($blIsSmtp = (bool) ($rHandle = @fsockopen($sSmtpHost, $sSmtpPort, $iErrNo, $sErrStr, 30))) {
                // closing connection ..
                fclose($rHandle);
            }
        }

        return $blIsSmtp;
    }


    /**
     * Sets smtp authentification parameters.
     *
     * @param string $sUserName     smtp user
     * @param \oxShop $sUserPassword smtp password
     */
    protected function _setSmtpAuthInfo($sUserName = null, $sUserPassword = null)
    {
        $this->mailer->set('SMTPAuth', true);
        $this->mailer->set('Username', $sUserName);
        $this->mailer->set('Password', $sUserPassword);
    }

    /**
     * Outputs email fields thought email output processor, includes images, and initiate email sending
     * If fails to send mail via SMTP, tries to send via mail(). On failing to send, sends mail to
     * shop administrator about failing mail sending
     *
     * @return bool
     */
    public function send(MailContainerReaderInterface $container)
    {
        $this->mailer->isHTML($container->isHtml());

        $this->mailer->addAddress($address, $name = '');
        $this->mailer->setSubject($container->getSubject());
        $this->mailer->setBody($container->getBody());
        $this->mailer->setAltBody($container->getAltBody());
        $this->mailer->addReplyTo($sEmail, $sName);
        $this->mailer->setFrom($container->getFrom(), $container->getFromName());
        $this->mailer->addEmbeddedImage($sFullPath, $sCid, $sAttFile, $sEncoding, $sType);
        $this->mailer->addAttachment($sAttPath, $sAttFile, $sEncoding, $sType);

        $sent = $this->mailer->send();

        // try to send mail via SMTP
        if (false === $sent && $this->mailer->Mailer === 'smtp') {
            $this->mailer->set('Mailer', 'mail');
            $sent = $this->mailer->send();
        }

        return $sent;
    }

    /**
     * Gets mailing error info.
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->mailer->ErrorInfo;
    }
}
