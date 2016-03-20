<?php
namespace OxidEsales\Eshop\Core;

/**
 * Class MailClient
 */
class MailClientConfig
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

    public function getCharset()
    {
        return 'utf-8';
    }

    public function isDebugEnabled()
    {
        return $this->config->getConfigParam('iDebug') == 6;
    }

    public function getProtocol()
    {
        return 'smtp';
    }

    public function getSmtpHost()
    {
        return $this->_setSmtpProtocol($this->_getShop()->oxshops__oxsmtp->value);
    }

    public function getSmtpUser()
    {
        return $this->_getShop()->oxshops__oxsmtpuser->value;
    }

    public function getSmtpPassword()
    {
        return $this->_getShop()->oxshops__oxsmtppwd->value;
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
    protected function _getShop()
    {
        return $this->config->getActiveShop();
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

    public function isSecureConnection()
    {
        $aMatch = array();
        if (getStr()->preg_match('@^([0-9a-z]+://)?(.*)$@i', $this->_getShop()->oxshops__oxsmtp->value, $aMatch)) {
            if ($aMatch[1]) {
                if (($aMatch[1] == 'ssl://') || ($aMatch[1] == 'tls://')) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getSecureChannel()
    {
        $aMatch = array();
        if (getStr()->preg_match('@^([0-9a-z]+://)?(.*)$@i', $this->_getShop()->oxshops__oxsmtp->value, $aMatch)) {
            if ($aMatch[1]) {
                if (($aMatch[1] == 'ssl://') || ($aMatch[1] == 'tls://')) {
                    return substr($aMatch[1], 0, 3);
                }
            }
        }
    }

    public function requiresAuthorization()
    {
        return !empty($this->_getShop()->oxshops__oxsmtpuser->value);
    }
}
