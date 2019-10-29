<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;
use OxidEsales\Eshop\Application\Model\OrderFileList;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * Mailing manager.
 * Collects mailing configuration, other parameters, performs mailing functions
 * (newsletters, ordering, registration emails, etc.).
 */
class Email extends PHPMailer
{
    /**
     * @deprecated since v6.3.2 (2018-12-14); This property will be removed completely. In v6.4 a new version of PHPMailer will be introduced, which uses const VERSION instead.
     *
     * The PHPMailer Version number.
     * @var string
     */
    public $Version = '5.2.27';

    /**
     * Default Smtp server port
     *
     * @deprecated use $smtpPort instead
     *
     * @var int
     */
    public $SMTP_PORT = 25;

    /**
     * Default Smtp server port
     *
     * @var int
     */
    public $smtpPort = 25;

    /**
     * Password reminder mail template
     *
     * @var string
     */
    protected $_sForgotPwdTemplate = "email/html/forgotpwd.tpl";

    /**
     * Password reminder plain mail template
     *
     * @var string
     */
    protected $_sForgotPwdTemplatePlain = "email/plain/forgotpwd.tpl";

    /**
     * Newsletter registration mail template
     *
     * @var string
     */
    protected $_sNewsletterOptInTemplate = "email/html/newsletteroptin.tpl";

    /**
     * Newsletter registration plain mail template
     *
     * @var string
     */
    protected $_sNewsletterOptInTemplatePlain = "email/plain/newsletteroptin.tpl";

    /**
     * Product suggest mail template
     *
     * @var string
     */
    protected $_sSuggestTemplate = "email/html/suggest.tpl";

    /**
     * Product suggest plain mail template
     *
     * @var string
     */
    protected $_sSuggestTemplatePlain = "email/plain/suggest.tpl";

    /**
     * Product suggest mail template
     *
     * @var string
     */
    protected $_sInviteTemplate = "email/html/invite.tpl";

    /**
     * Product suggest plain mail template
     *
     * @var string
     */
    protected $_sInviteTemplatePlain = "email/plain/invite.tpl";

    /**
     * Send order notification mail template
     *
     * @var string
     */
    protected $_sSenedNowTemplate = "email/html/ordershipped.tpl";

    /**
     * Send order notification plain mail template
     *
     * @var string
     */
    protected $_sSenedNowTemplatePlain = "email/plain/ordershipped.tpl";

    /**
     * Send ordered download links mail template
     *
     * @var string
     */
    protected $_sSendDownloadsTemplate = "email/html/senddownloadlinks.tpl";

    /**
     * Send ordered download links plain mail template
     *
     * @var string
     */
    protected $_sSendDownloadsTemplatePlain = "email/plain/senddownloadlinks.tpl";

    /**
     * Wishlist mail template
     *
     * @var string
     */
    protected $_sWishListTemplate = "email/html/wishlist.tpl";

    /**
     * Wishlist plain mail template
     *
     * @var string
     */
    protected $_sWishListTemplatePlain = "email/plain/wishlist.tpl";

    /**
     * Name of template used during registration
     *
     * @var string
     */
    protected $_sRegisterTemplate = "email/html/register.tpl";

    /**
     * Name of plain template used during registration
     *
     * @var string
     */
    protected $_sRegisterTemplatePlain = "email/plain/register.tpl";

    /**
     * Name of template used by reminder function (article).
     *
     * @var string
     */
    protected $_sReminderMailTemplate = "email/html/owner_reminder.tpl";

    /**
     * Order e-mail for customer HTML template
     *
     * @var string
     */
    protected $_sOrderUserTemplate = "email/html/order_cust.tpl";

    /**
     * Order e-mail for customer plain text template
     *
     * @var string
     */
    protected $_sOrderUserPlainTemplate = "email/plain/order_cust.tpl";

    /**
     * Order e-mail for shop owner HTML template
     *
     * @var string
     */
    protected $_sOrderOwnerTemplate = "email/html/order_owner.tpl";

    /**
     * Order e-mail for shop owner plain text template
     *
     * @var string
     */
    protected $_sOrderOwnerPlainTemplate = "email/plain/order_owner.tpl";

    // #586A - additional templates for more customizable subjects

    /**
     * Order e-mail subject for customer template
     *
     * @var string
     */
    protected $_sOrderUserSubjectTemplate = "email/html/order_cust_subj.tpl";

    /**
     * Order e-mail subject for shop owner template
     *
     * @var string
     */
    protected $_sOrderOwnerSubjectTemplate = "email/html/order_owner_subj.tpl";

    /**
     * Price alarm e-mail for shop owner template
     *
     * @var string
     */
    protected $_sOwnerPricealarmTemplate = "email/html/pricealarm_owner.tpl";

    /**
     * Price alarm e-mail for shop owner template
     *
     * @var string
     */
    protected $_sPricealamrCustomerTemplate = "email_pricealarm_customer.tpl";

    /**
     * Language specific viewconfig object array containing view data, view config and shop object
     *
     * @var array
     */
    protected $_aShops = [];

    /**
     * Add inline images to mail
     *
     * @var bool
     */
    protected $_blInlineImgEmail = null;

    /**
     * Array of recipient email addresses
     *
     * @var array
     */
    protected $_aRecipients = [];

    /**
     * Array of reply addresses used
     *
     * @var array
     */
    protected $_aReplies = [];

    /**
     * Attachment info array
     *
     * @var array
     */
    protected $_aAttachments = [];

    /**
     * Smarty instance
     *
     * @deprecated since v6.4 (2019-10-10); Will be removed
     *
     * @var \Smarty
     */
    protected $_oSmarty = null;

    /**
     * Email view data
     *
     * @var array
     */
    protected $_aViewData = [];

    /**
     * Shop object
     *
     * @var \OxidEsales\Eshop\Application\Model\Shop
     */
    protected $_oShop = null;

    /**
     * Email charset
     *
     * @var string
     */
    protected $_sCharSet = null;

    /**
     * @var \OxidEsales\Eshop\Core\Config
     *
     * @deprecated since v6.4.0 (2018-10-15); This property will be removed completely. Use \OxidEsales\Eshop\Core\Registry::getConfig().
     */
    protected $_oConfig = null;

    /**
     * Class constructor.
     */
    public function __construct()
    {
        //enabling exception handling in phpMailer class
        parent::__construct(true);

        $myConfig = $this->getConfig();

        $this->setSmtp();

        $this->setUseInlineImages($myConfig->getConfigParam('blInlineImgEmail'));
        $this->setMailWordWrap(100);

        $this->isHTML(true);
        $this->setLanguage("en", $myConfig->getConfigParam('sShopDir') . "/Core/phpmailer/language/");

        $this->_getSmarty();

        //setting default view
        $this->setViewData('oEmailView', $this);

        $this->Encoding = 'base64';
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $method Methods name
     * @param array  $args   Argument array
     *
     * @throws SystemComponentException Throws an exception if the called method does not exist or is not accessible in current class
     *
     * @return string
     */
    public function __call($method, $args)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($method, 0, 4) == "UNIT") {
                $method = str_replace("UNIT", "_", $method);
            }
            if (method_exists($this, $method)) {
                return call_user_func_array([& $this, $method], $args);
            }
        }

        throw new SystemComponentException("Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL);
    }

    /**
     * oxConfig instance getter
     *
     * @deprecated since v6.4.0 (2018-10-15); This method will be removed completely. Use \OxidEsales\Eshop\Core\Registry::getConfig().
     *
     * @return \OxidEsales\Eshop\Core\Config
     */
    public function getConfig()
    {
        if ($this->_oConfig == null) {
            $this->_oConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        }

        return $this->_oConfig;
    }

    /**
     * oxConfig instance setter
     *
     * @param \OxidEsales\Eshop\Core\Config $config config object
     *
     * @deprecated since v6.4.0 (2018-10-15); This method will be removed completely. Use \OxidEsales\Eshop\Core\Registry::set(\OxidEsales\Eshop\Core\Config:class, $config).
     */
    public function setConfig($config)
    {
        $this->_oConfig = $config;
    }


    /**
     * @deprecated since v6.4 (2019-10-10); Use TemplateRendererBridgeInterface
     *
     * Smarty instance getter, assigns this oxEmail instance to "oEmailView" variable
     *
     * @return \Smarty
     */
    protected function _getSmarty()
    {
        if ($this->_oSmarty === null) {
            $this->_oSmarty = \OxidEsales\Eshop\Core\Registry::getUtilsView()->getSmarty();
        }

        return $this->_oSmarty;
    }

    /**
     * Templating instance getter
     *
     * @return TemplateRendererInterface
     */
    private function getRenderer()
    {
        $bridge = $this->getContainer()->get(TemplateRendererBridgeInterface::class);
        $bridge->setEngine($this->_getSmarty());

        return $bridge->getTemplateRenderer();
    }

    /**
     * @internal
     *
     * @return \Psr\Container\ContainerInterface
     */
    protected function getContainer()
    {
        return \OxidEsales\EshopCommunity\Internal\Container\ContainerFactory::getInstance()->getContainer();
    }

    /**
     * Outputs email fields thought email output processor, includes images, and initiate email sending
     * If fails to send mail via SMTP, tries to send via mail(). On failing to send, sends mail to
     * shop administrator about failing mail sending
     *
     * @return bool
     */
    public function send()
    {
        // if no recipients found, skipping sending
        if (count($this->getRecipient()) < 1) {
            return false;
        }

        $myConfig = $this->getConfig();
        $this->setCharSet();

        if ($this->_getUseInlineImages()) {
            $this->_includeImages(
                $myConfig->getImageUrl(),
                $myConfig->getImageUrl(false, false),
                $myConfig->getPictureUrl(null, false),
                $myConfig->getImageDir(),
                $myConfig->getPictureDir(false)
            );
        }

        $this->_makeOutputProcessing();

        // try to send mail via SMTP
        if ($this->getMailer() == 'smtp') {
            $ret = $this->_sendMail();

            // if sending failed, try to send via mail()
            if (!$ret) {
                // failed sending via SMTP, sending notification to shop owner
                $this->_sendMailErrorMsg();

                // trying to send using standard mailer
                $this->setMailer('mail');
                $ret = $this->_sendMail();
            }
        } else {
            // sending mail via mail()
            $this->setMailer('mail');
            $ret = $this->_sendMail();
        }

        if (!$ret) {
            // failed sending, giving up, trying to send notification to shop owner
            $this->_sendMailErrorMsg();
        }

        return $ret;
    }

    /**
     * Sets smtp parameters depending on the protocol used
     * returns smtp url which should be used for fsockopen
     *
     * @param string $url initial smtp
     *
     * @return string
     */
    protected function _setSmtpProtocol($url)
    {
        $protocol = '';
        $smtpHost = $url;
        $match = [];
        if (Str::getStr()->preg_match('@^([0-9a-z]+://)?(.*)$@i', $url, $match)) {
            if ($match[1]) {
                if (($match[1] == 'ssl://') || ($match[1] == 'tls://')) {
                    $this->set("SMTPSecure", substr($match[1], 0, 3));
                } else {
                    $protocol = $match[1];
                }
            }
            $smtpHost = $match[2];
        }

        return $protocol . $smtpHost;
    }

    /**
     * Sets SMTP mailer parameters, such as user name, password, location.
     *
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop Object, that keeps base shop info
     *
     * @return null
     */
    public function setSmtp($shop = null)
    {
        $myConfig = $this->getConfig();
        $shop = ($shop) ? $shop : $this->_getShop();

        $smtpUrl = $this->_setSmtpProtocol($shop->oxshops__oxsmtp->value);

        if (!$this->_isValidSmtpHost($smtpUrl)) {
            $this->setMailer("mail");

            return;
        }

        $this->setHost($smtpUrl);
        $this->setMailer("smtp");

        if ($shop->oxshops__oxsmtpuser->value) {
            $this->_setSmtpAuthInfo($shop->oxshops__oxsmtpuser->value, $shop->oxshops__oxsmtppwd->value);
        }

        if ($myConfig->getConfigParam('iDebug') == 6) {
            $this->_setSmtpDebug(true);
        }
    }

    /**
     * Checks if smtp host is valid (tries to connect to it)
     *
     * @param string $smtpHost currently used smtp server host name
     *
     * @return bool
     */
    protected function _isValidSmtpHost($smtpHost)
    {
        $isSmtp = false;
        if ($smtpHost) {
            $match = [];
            $smtpPort = isset($this->SMTP_PORT)
                ? $this->SMTP_PORT
                : $this->smtpPort;
            if (Str::getStr()->preg_match('@^(.*?)(:([0-9]+))?$@i', $smtpHost, $match)) {
                $smtpHost = $match[1];
                if (isset($match[3]) && (int) $match[3] !== 0) {
                    $smtpPort = (int) $match[3];
                }
            }
            if ($isSmtp = (bool) ($rHandle = @fsockopen($smtpHost, $smtpPort, $errNo, $errStr, 30))) {
                // closing connection ..
                fclose($rHandle);
            }
        }

        return $isSmtp;
    }

    /**
     * Sets mailer additional settings and sends ordering mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order   Order object
     * @param string                                    $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendOrderEmailToUser($order, $subject = null)
    {
        // add user defined stuff if there is any
        $order = $this->_addUserInfoOrderEMail($order);

        $shop = $this->_getShop();
        $this->_setMailParams($shop);

        $user = $order->getOrderUser();
        $this->setUser($user);

        // create messages
        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);

        $this->setViewData("blShowReviewLink", $this->shouldProductReviewLinksBeIncluded());

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sOrderUserTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sOrderUserPlainTemplate, $this->getViewData()));

        // #586A
        if ($subject === null) {
            if ($renderer->exists($this->_sOrderUserSubjectTemplate)) {
                $subject = $renderer->renderTemplate($this->_sOrderUserSubjectTemplate, $this->getViewData());
            } else {
                $subject = $shop->oxshops__oxordersubject->getRawValue() . " (#" . $order->oxorder__oxordernr->value . ")";
            }
        }

        $this->setSubject($subject);

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();

        $this->setRecipient($user->oxuser__oxusername->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends ordering mail to shop owner.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order   Order object
     * @param string                                    $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendOrderEmailToOwner($order, $subject = null)
    {
        $config = $this->getConfig();

        $shop = $this->_getShop();

        // cleanup
        $this->_clearMailer();

        // add user defined stuff if there is any
        $order = $this->_addUserInfoOrderEMail($order);

        $user = $order->getOrderUser();
        $this->setUser($user);

        // send confirmation to shop owner
        // send not pretending from order user, as different email domain rise spam filters
        $this->setFrom($shop->oxshops__oxowneremail->value);

        $language = \OxidEsales\Eshop\Core\Registry::getLang();
        $orderLanguage = $language->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ($shop->getLanguage() != $orderLanguage) {
            $shop = $this->_getShop($orderLanguage);
        }

        $this->setSmtp($shop);

        // create messages
        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sOrderOwnerTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sOrderOwnerPlainTemplate, $this->getViewData()));

        //Sets subject to email
        // #586A
        if ($subject === null) {
            if ($renderer->exists($this->_sOrderOwnerSubjectTemplate)) {
                $subject = $renderer->renderTemplate($this->_sOrderOwnerSubjectTemplate, $this->getViewData());
            } else {
                $subject = $shop->oxshops__oxordersubject->getRawValue() . " (#" . $order->oxorder__oxordernr->value . ")";
            }
        }

        $this->setSubject($subject);
        $this->setRecipient($shop->oxshops__oxowneremail->value, $language->translateString("order"));

        if ($user->oxuser__oxusername->value != "admin") {
            $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();
            $this->setReplyTo($user->oxuser__oxusername->value, $fullName);
        }

        $result = $this->send();

        $this->onOrderEmailToOwnerSent($user, $order);

        if ($config->getConfigParam('iDebug') == 6) {
            \OxidEsales\Eshop\Core\Registry::getUtils()->showMessageAndExit("");
        }

        return $result;
    }

    /**
     * Method is called when order email is sent to owner.
     *
     * @param \OxidEsales\Eshop\Application\Model\User  $user
     * @param \OxidEsales\Eshop\Application\Model\Order $order
     */
    protected function onOrderEmailToOwnerSent($user, $order)
    {
        // add user history
        $remark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
        $remark->oxremark__oxtext = new \OxidEsales\Eshop\Core\Field($this->getAltBody(), \OxidEsales\Eshop\Core\Field::T_RAW);
        $remark->oxremark__oxparentid = new \OxidEsales\Eshop\Core\Field($user->getId(), \OxidEsales\Eshop\Core\Field::T_RAW);
        $remark->oxremark__oxtype = new \OxidEsales\Eshop\Core\Field("o", \OxidEsales\Eshop\Core\Field::T_RAW);
        $remark->save();
    }

    /**
     * Sets mailer additional settings and sends registration mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user    user object
     * @param string                                   $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendRegisterConfirmEmail($user, $subject = null)
    {
        // setting content ident

        $this->setViewData("contentident", "oxregisteraltemail");
        $this->setViewData("contentplainident", "oxregisterplainaltemail");

        // sending email
        return $this->sendRegisterEmail($user, $subject);
    }

    /**
     * Sets mailer additional settings and sends registration mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user    user object
     * @param string                                   $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendRegisterEmail($user, $subject = null)
    {
        // add user defined stuff if there is any
        $user = $this->_addUserRegisterEmail($user);

        // shop info
        $shop = $this->_getShop();

        //set mail params (from, fromName, smtp )
        $this->_setMailParams($shop);

        // create messages
        $renderer = $this->getRenderer();
        $this->setUser($user);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sRegisterTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sRegisterTemplatePlain, $this->getViewData()));

        $this->setSubject(($subject !== null) ? $subject : $shop->oxshops__oxregistersubject->getRawValue());

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();

        $this->setRecipient($user->oxuser__oxusername->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends "forgot password" mail to user.
     * Returns true on success.
     *
     * @param string $emailAddress user email address
     * @param string $subject      user defined subject [optional]
     *
     * @return mixed true - success, false - user not found, -1 - could not send
     */
    public function sendForgotPwdEmail($emailAddress, $subject = null)
    {
        $result = false;

        $shop = $this->_addForgotPwdEmail($this->_getShop());

        $oxid = $this->getUserIdByUserName($emailAddress, $shop->getId());
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($oxid && $user->load($oxid)) {
            // create messages
            $renderer = $this->getRenderer();
            $this->setUser($user);
            $this->_processViewArray();

            $this->_setMailParams($shop);
            $this->setBody($renderer->renderTemplate($this->_sForgotPwdTemplate, $this->getViewData()));
            $this->setAltBody($renderer->renderTemplate($this->_sForgotPwdTemplatePlain, $this->getViewData()));
            $this->setSubject(($subject !== null) ? $subject : $shop->oxshops__oxforgotpwdsubject->getRawValue());

            $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();
            $recipientAddress = $user->oxuser__oxusername->getRawValue();

            $this->setRecipient($recipientAddress, $fullName);
            $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

            if (!$this->send()) {
                $result = -1; // failed to send
            } else {
                $result = true; // success
            }
        }

        return $result;
    }

    /**
     * Sets mailer additional settings and sends contact info mail to user.
     * Returns true on success.
     *
     * @param string $emailAddress Email address
     * @param string $subject      Email subject
     * @param string $message      Email message text
     *
     * @return bool
     */
    public function sendContactMail($emailAddress = null, $subject = null, $message = null)
    {

        // shop info
        $shop = $this->_getShop();

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        $this->setBody($message);
        $this->setSubject($subject);

        $this->setRecipient($shop->oxshops__oxinfoemail->value, "");
        $this->setFrom($shop->oxshops__oxowneremail->value, $shop->oxshops__oxname->getRawValue());
        $this->setReplyTo($emailAddress, "");

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends "NewsletterDBOptInMail" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user    user object
     * @param string                                   $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendNewsletterDbOptInMail($user, $subject = null)
    {
        // add user defined stuff if there is any
        $user = $this->_addNewsletterDbOptInMail($user);

        // shop info
        $shop = $this->_getShop();

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        // create messages
        $renderer = $this->getRenderer();
        $confirmCode = md5($user->oxuser__oxusername->value . $user->oxuser__oxpasssalt->value);
        $this->setViewData("subscribeLink", $this->_getNewsSubsLink($user->oxuser__oxid->value, $confirmCode));
        $this->setUser($user);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sNewsletterOptInTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sNewsletterOptInTemplatePlain, $this->getViewData()));
        $this->setSubject(($subject !== null) ? $subject : \OxidEsales\Eshop\Core\Registry::getLang()->translateString("NEWSLETTER") . " " . $shop->oxshops__oxname->getRawValue());

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();

        $this->setRecipient($user->oxuser__oxusername->value, $fullName);
        $this->setFrom($shop->oxshops__oxinfoemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setReplyTo($shop->oxshops__oxinfoemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Returns newsletter subscription link
     *
     * @param string $id          user id
     * @param string $confirmCode confirmation code
     *
     * @return string $url
     */
    protected function _getNewsSubsLink($id, $confirmCode = null)
    {
        $myConfig = $this->getConfig();
        $actShopLang = $myConfig->getActiveShop()->getLanguage();

        $url = $myConfig->getShopHomeUrl() . 'cl=newsletter&amp;fnc=addme&amp;uid=' . $id;
        $url .= '&amp;lang=' . $actShopLang;
        $url .= ($confirmCode) ? '&amp;confirm=' . $confirmCode : "";

        return $url;
    }

    /**
     * Sets mailer additional settings and sends "newsletter" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\Newsletter $newsLetter newsletter object
     * @param \OxidEsales\Eshop\Application\Model\User       $user       user object
     * @param string                                         $subject    user defined subject [optional]
     *
     * @return bool
     */
    public function sendNewsletterMail($newsLetter, $user, $subject = null)
    {
        // shop info
        $shop = $this->_getShop();

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        $body = $newsLetter->getHtmlText();

        if (!empty($body)) {
            $this->setBody($body);
            $this->setAltBody($newsLetter->getPlainText());
        } else {
            $this->isHTML(false);
            $this->setBody($newsLetter->getPlainText());
        }

        $this->setSubject(($subject !== null) ? $subject : $newsLetter->oxnewsletter__oxtitle->getRawValue());

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();
        $this->setRecipient($user->oxuser__oxusername->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends "SuggestMail" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user    Mailing parameters object
     * @param object                                   $product Product object
     *
     * @return bool
     */
    public function sendSuggestMail($user, $product)
    {
        $myConfig = $this->getConfig();

        //sets language of shop
        $currLang = $myConfig->getActiveShop()->getLanguage();

        // shop info
        $shop = $this->_getShop($currLang);

        //sets language to article
        if ($product->getLanguage() != $currLang) {
            $product->setLanguage($currLang);
            $product->load($product->getId());
        }

        // mailer stuff
        // send not pretending from suggesting user, as different email domain rise spam filters
        $this->setFrom($shop->oxshops__oxinfoemail->value);
        $this->setSmtp();

        // create messages
        $renderer = $this->getRenderer();
        $this->setViewData("product", $product);
        $this->setUser($user);

        $articleUrl = $product->getLink();

        //setting recommended user id
        if ($myConfig->getActiveView()->isActive('Invitations') && $activeUser = $shop->getUser()) {
            $articleUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendParamSeparator($articleUrl);
            $articleUrl .= "su=" . $activeUser->getId();
        }

        $this->setViewData("sArticleUrl", $articleUrl);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sSuggestTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sSuggestTemplatePlain, $this->getViewData()));
        $this->setSubject($user->send_subject);

        $this->setRecipient($user->rec_email, $user->rec_name);
        $this->setReplyTo($user->send_email, $user->send_name);

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends "InviteMail" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user Mailing parameters object
     *
     * @return bool
     */
    public function sendInviteMail($user)
    {
        $myConfig = $this->getConfig();

        //sets language of shop
        $currLang = $myConfig->getActiveShop()->getLanguage();

        // shop info
        $shop = $this->_getShop($currLang);

        // mailer stuff
        $this->setFrom($user->send_email, $user->send_name);
        $this->setSmtp();

        // create messages
        /** @var TemplateRendererInterface $renderer */
        $renderer = $this->getContainer()->get(TemplateRendererBridgeInterface::class)->getTemplateRenderer();
        $this->setUser($user);

        $homeUrl = $this->getViewConfig()->getHomeLink();

        //setting recommended user id
        if ($myConfig->getActiveView()->isActive('Invitations') && $activeUser = $shop->getUser()) {
            $homeUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendParamSeparator($homeUrl);
            $homeUrl .= "su=" . $activeUser->getId();
        }

        if (is_array($user->rec_email) && count($user->rec_email) > 0) {
            foreach ($user->rec_email as $email) {
                if (!empty($email)) {
                    $registerUrl = \OxidEsales\Eshop\Core\Registry::getUtilsUrl()->appendParamSeparator($homeUrl);
                    //setting recipient user email
                    $registerUrl .= "re=" . md5($email);
                    $this->setViewData("sHomeUrl", $registerUrl);

                    // Process view data array through oxoutput processor
                    $this->_processViewArray();

                    $this->setBody($renderer->renderTemplate($this->_sInviteTemplate, $this->getViewData()));

                    $this->setAltBody($renderer->renderTemplate($this->_sInviteTemplatePlain, $this->getViewData()));
                    $this->setSubject($user->send_subject);

                    $this->setRecipient($email);
                    $this->setReplyTo($user->send_email, $user->send_name);
                    $this->send();
                    $this->clearAllRecipients();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Sets mailer additional settings and sends "SendedNowMail" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order   order object
     * @param string                                    $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendSendedNowMail($order, $subject = null)
    {
        $myConfig = $this->getConfig();

        $orderLang = (int) (isset($order->oxorder__oxlang->value) ? $order->oxorder__oxlang->value : 0);

        // shop info
        $shop = $this->_getShop($orderLang);

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        //create messages
        $lang = \OxidEsales\Eshop\Core\Registry::getLang();
        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);
        $this->setViewData("shopTemplateDir", $myConfig->getTemplateDir(false));

        if ($myConfig->getConfigParam('bl_perfLoadReviews', false)) {
            $this->setViewData("blShowReviewLink", true);
            $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
            $this->setViewData("reviewuserhash", $user->getReviewUserHash($order->oxorder__oxuserid->value));
        } else {
            $this->setViewData("blShowReviewLink", false);
        }

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        //V send email in order language
        $oldTplLang = $lang->getTplLanguage();
        $oldBaseLang = $lang->getBaseLanguage();
        $lang->setTplLanguage($orderLang);
        $lang->setBaseLanguage($orderLang);

        // force non admin to get correct paths (tpl, img)
        $myConfig->setAdminMode(false);
        $this->setBody($renderer->renderTemplate($this->_sSenedNowTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sSenedNowTemplatePlain, $this->getViewData()));
        $myConfig->setAdminMode(true);
        $lang->setTplLanguage($oldTplLang);
        $lang->setBaseLanguage($oldBaseLang);

        //Sets subject to email
        $this->setSubject(($subject !== null) ? $subject : $shop->oxshops__oxsendednowsubject->getRawValue());

        $fullName = $order->oxorder__oxbillfname->getRawValue() . " " . $order->oxorder__oxbilllname->getRawValue();

        $this->setRecipient($order->oxorder__oxbillemail->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends "SendDownloadLinks" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order   order object
     * @param string                                    $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendDownloadLinksMail($order, $subject = null)
    {
        $myConfig = $this->getConfig();

        $orderLang = (int) (isset($order->oxorder__oxlang->value) ? $order->oxorder__oxlang->value : 0);

        // shop info
        $shop = $this->_getShop($orderLang);

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        //create messages
        $lang = \OxidEsales\Eshop\Core\Registry::getLang();
        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);
        $this->setViewData("shopTemplateDir", $myConfig->getTemplateDir(false));

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $this->setViewData("reviewuserhash", $user->getReviewUserHash($order->oxorder__oxuserid->value));

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        //V send email in order language
        $oldTplLang = $lang->getTplLanguage();
        $oldBaseLang = $lang->getTplLanguage();
        $lang->setTplLanguage($orderLang);
        $lang->setBaseLanguage($orderLang);

        // force non admin to get correct paths (tpl, img)
        $myConfig->setAdminMode(false);
        $this->setBody($renderer->renderTemplate($this->_sSendDownloadsTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sSendDownloadsTemplatePlain, $this->getViewData()));
        $myConfig->setAdminMode(true);
        $lang->setTplLanguage($oldTplLang);
        $lang->setBaseLanguage($oldBaseLang);

        //Sets subject to email
        $this->setSubject(($subject !== null) ? $subject : $lang->translateString("DOWNLOAD_LINKS", null, false));

        $fullName = $order->oxorder__oxbillfname->getRawValue() . " " . $order->oxorder__oxbilllname->getRawValue();

        $this->setRecipient($order->oxorder__oxbillemail->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends backup data to user.
     * Returns true on success.
     *
     * @param array  $attFiles     Array of file names to attach
     * @param string $attPath      Path to files to attach
     * @param string $emailAddress Email address
     * @param string $subject      Email subject
     * @param string $message      Email body message
     * @param array  $status       Pointer to mailing status array
     * @param array  $error        Pointer to error status array
     *
     * @return bool
     */
    public function sendBackupMail($attFiles, $attPath, $emailAddress, $subject, $message, &$status, &$error)
    {
        // shop info
        $shop = $this->_getShop();

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        $this->setBody($message);
        $this->setSubject($subject);

        $this->setRecipient($shop->oxshops__oxinfoemail->value, "");
        $emailAddress = $emailAddress ? $emailAddress : $shop->oxshops__oxowneremail->value;

        $this->setFrom($emailAddress, "");
        $this->setReplyTo($emailAddress, "");

        //attaching files
        $attashSucc = true;
        $attPath = \OxidEsales\Eshop\Core\Registry::getUtilsFile()->normalizeDir($attPath);
        foreach ($attFiles as $num => $attFile) {
            $fullPath = $attPath . $attFile;
            if (@is_readable($fullPath) && @is_file($fullPath)) {
                $attashSucc = $this->addAttachment($fullPath, $attFile);
            } else {
                $attashSucc = false;
                $error[] = [5, $attFile]; //"Error: backup file $attFile not found";
            }
        }

        if (!$attashSucc) {
            $error[] = [4, ""]; //"Error: backup files was not sent to email ...";
            $this->clearAttachments();

            return false;
        }

        $status[] = 3; //"Mailing backup files ...";
        $send = $this->send();
        $this->clearAttachments();

        return $send;
    }

    /**
     * Basic wrapper for email message sending with default parameters from the oxBaseShop.
     * Returns true on success.
     *
     * @param mixed  $to      Recipient or an array of the recipients
     * @param string $subject Mail subject
     * @param string $body    Mail body
     *
     * @return bool
     */
    public function sendEmail($to, $subject, $body)
    {
        //set mail params (from, fromName, smtp)
        $this->_setMailParams();

        if (is_array($to)) {
            foreach ($to as $address) {
                $this->setRecipient($address, "");
                $this->setReplyTo($address, "");
            }
        } else {
            $this->setRecipient($to, "");
            $this->setReplyTo($to, "");
        }

        //may be changed later
        $this->isHTML(false);

        $this->setSubject($subject);
        $this->setBody($body);

        return $this->send();
    }

    /**
     * Sends reminder email to shop owner.
     *
     * @param array  $basketContents array of objects to pass to template
     * @param string $subject        user defined subject [optional]
     *
     * @return bool
     */
    public function sendStockReminder($basketContents, $subject = null)
    {
        $send = false;

        $articleList = oxNew(\OxidEsales\Eshop\Application\Model\ArticleList::class);
        $articleList->loadStockRemindProducts($basketContents);

        // nothing to remind?
        if ($articleList->count()) {
            $shop = $this->_getShop();

            //set mail params (from, fromName, smtp... )
            $this->_setMailParams($shop);
            $lang = \OxidEsales\Eshop\Core\Registry::getLang();

            $renderer = $this->getRenderer();
            $this->setViewData("articles", $articleList);

            // Process view data array through oxOutput processor
            $this->_processViewArray();

            $this->setRecipient($shop->oxshops__oxowneremail->value, $shop->oxshops__oxname->getRawValue());
            $this->setFrom($shop->oxshops__oxowneremail->value, $shop->oxshops__oxname->getRawValue());
            $this->setBody($renderer->renderTemplate($this->getConfig()->getTemplatePath($this->_sReminderMailTemplate, false), $this->getViewData()));
            $this->setAltBody("");
            $this->setSubject(($subject !== null) ? $subject : $lang->translateString('STOCK_LOW'));

            $send = $this->send();
        }

        return $send;
    }

    /**
     * Sets mailer additional settings and sends "WishlistMail" mail to user.
     * Returns true on success.
     *
     * @param \OxidEsales\Eshop\Application\Model\User|object $params Mailing parameters object
     *
     * @return bool
     */
    public function sendWishlistMail($params)
    {
        $this->_clearMailer();

        // mailer stuff
        $this->setFrom($params->send_email, $params->send_name);
        $this->setSmtp();

        // create messages
        $renderer = $this->getRenderer();
        $this->setUser($params);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sWishListTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sWishListTemplatePlain, $this->getViewData()));
        $this->setSubject($params->send_subject);

        $this->setRecipient($params->rec_email, $params->rec_name);
        $this->setReplyTo($params->send_email, $params->send_name);

        return $this->send();
    }

    /**
     * Sends a notification to the shop owner that price alarm was subscribed.
     * Returns true on success.
     *
     * @param array                                          $params  Parameters array
     * @param \OxidEsales\Eshop\Application\Model\PriceAlarm $alarm   oxPriceAlarm object
     * @param string                                         $subject user defined subject [optional]
     *
     * @return bool
     */
    public function sendPriceAlarmNotification($params, $alarm, $subject = null)
    {
        $this->_clearMailer();
        $shop = $this->_getShop();

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        $alarmLang = $alarm->oxpricealarm__oxlang->value;

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        //$article->setSkipAbPrice( true );
        $article->loadInLang($alarmLang, $params['aid']);
        $lang = \OxidEsales\Eshop\Core\Registry::getLang();

        // create messages
        $renderer = $this->getRenderer();
        $this->setViewData("product", $article);
        $this->setViewData("email", $params['email']);
        $this->setViewData("bidprice", $lang->formatCurrency($alarm->oxpricealarm__oxprice->value));

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setRecipient($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setSubject(($subject !== null) ? $subject : $lang->translateString('PRICE_ALERT_FOR_PRODUCT', $alarmLang) . " " . $article->oxarticles__oxtitle->getRawValue());
        $this->setBody($renderer->renderTemplate($this->_sOwnerPricealarmTemplate, $this->getViewData()));
        $this->setFrom($params['email'], "");
        $this->setReplyTo($params['email'], "");

        return $this->send();
    }

    /**
     * Sends price alarm to customer.
     * Returns true on success.
     *
     * @param string                                         $recipient      email
     * @param \OxidEsales\Eshop\Application\Model\PriceAlarm $alarm          oxPriceAlarm object
     * @param string                                         $body           optional mail body
     * @param bool                                           $returnMailBody returns mail body instead of sending
     *
     * @return bool
     */
    public function sendPricealarmToCustomer($recipient, $alarm, $body = null, $returnMailBody = null)
    {
        $this->_clearMailer();

        $shop = $this->_getShop();

        if ($shop->getId() != $alarm->oxpricealarm__oxshopid->value) {
            $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
            $shop->load($alarm->oxpricealarm__oxshopid->value);
            $this->setShop($shop);
        }

        //set mail params (from, fromName, smtp)
        $this->_setMailParams($shop);

        // create messages
        $renderer = $this->getRenderer();

        $this->setViewData("product", $alarm->getArticle());
        $this->setViewData("oPriceAlarm", $alarm);
        $this->setViewData("bidprice", $alarm->getFProposedPrice());
        $this->setViewData("currency", $alarm->getPriceAlarmCurrency());

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setRecipient($recipient, $recipient);
        $this->setSubject($shop->oxshops__oxname->value);

        if ($body === null) {
            $body = $renderer->renderTemplate($this->_sPricealamrCustomerTemplate, $this->getViewData());
        }

        $this->setBody($body);

        $this->addAddress($recipient, $recipient);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        if ($returnMailBody) {
            return $this->getBody();
        } else {
            return $this->send();
        }
    }

    /**
     * Checks for external images and embeds them to email message if possible
     *
     * @param string $imageDir       Images directory url
     * @param string $imageDirNoSSL  Images directory url (no SSL)
     * @param string $dynImageDir    Path to Dyn images
     * @param string $absImageDir    Absolute path to images
     * @param string $absDynImageDir Absolute path to Dyn images
     */
    protected function _includeImages($imageDir = null, $imageDirNoSSL = null, $dynImageDir = null, $absImageDir = null, $absDynImageDir = null)
    {
        $body = $this->getBody();
        if (preg_match_all('/<\s*img\s+[^>]*?src[\s]*=[\s]*[\'"]?([^[\'">]]+|.*?)?[\'">]/i', $body, $matches, PREG_SET_ORDER)) {
            $fileUtils = \OxidEsales\Eshop\Core\Registry::getUtilsFile();
            $reSetBody = false;

            // preparing imput
            $dynImageDir = $fileUtils->normalizeDir($dynImageDir);
            $imageDir = $fileUtils->normalizeDir($imageDir);
            $imageDirNoSSL = $fileUtils->normalizeDir($imageDirNoSSL);

            if (is_array($matches) && count($matches)) {
                $imageCache = [];
                $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
                $myUtilsObject = $this->getUtilsObjectInstance();
                $imgGenerator = oxNew(\OxidEsales\Eshop\Core\DynamicImageGenerator::class);

                foreach ($matches as $image) {
                    $imageName = $image[1];
                    $fileName = '';
                    if (strpos($imageName, $dynImageDir) === 0) {
                        $fileName = $fileUtils->normalizeDir($absDynImageDir) . str_replace($dynImageDir, '', $imageName);
                    } elseif (strpos($imageName, $imageDir) === 0) {
                        $fileName = $fileUtils->normalizeDir($absImageDir) . str_replace($imageDir, '', $imageName);
                    } elseif (strpos($imageName, $imageDirNoSSL) === 0) {
                        $fileName = $fileUtils->normalizeDir($absImageDir) . str_replace($imageDirNoSSL, '', $imageName);
                    }

                    if ($fileName && !@is_readable($fileName)) {
                        $fileName = $imgGenerator->getImagePath($fileName);
                    }

                    if ($fileName) {
                        if (isset($imageCache[$fileName]) && $imageCache[$fileName]) {
                            $cId = $imageCache[$fileName];
                        } else {
                            $cId = $myUtilsObject->generateUId();
                            $mIME = $myUtils->oxMimeContentType($fileName);
                            if ($mIME == 'image/jpeg' || $mIME == 'image/gif' || $mIME == 'image/png') {
                                if ($this->addEmbeddedImage($fileName, $cId, "image", "base64", $mIME)) {
                                    $imageCache[$fileName] = $cId;
                                } else {
                                    $cId = '';
                                }
                            }
                        }
                        if ($cId && $cId == $imageCache[$fileName]) {
                            if ($replTag = str_replace($imageName, 'cid:' . $cId, $image[0])) {
                                $body = str_replace($image[0], $replTag, $body);
                                $reSetBody = true;
                            }
                        }
                    }
                }
            }

            if ($reSetBody) {
                $this->setBody($body);
            }
        }
    }

    /**
     * Sets mail subject
     *
     * @param string $subject mail subject
     */
    public function setSubject($subject = null)
    {
        // A. HTML entities in subjects must be replaced
        $subject = str_replace(['&amp;', '&quot;', '&#039;', '&lt;', '&gt;'], ['&', '"', "'", '<', '>'], $subject);

        $this->set("Subject", $subject);
    }

    /**
     * Gets mail subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->Subject;
    }

    /**
     * Set mail body. If second parameter (default value is true) is set to true,
     * performs search for "sid", removes it and adds shop id to string.
     *
     * @param string $body     mail body
     * @param bool   $clearSid clear sid in mail body
     */
    public function setBody($body = null, $clearSid = true)
    {
        if ($clearSid) {
            $body = $this->_clearSidFromBody($body);
        }

        $this->set("Body", $body);
    }

    /**
     * Gets mail body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->Body;
    }

    /**
     * Sets text-only body of the message. If second parameter is set to true,
     * performs search for "sid", removes it and adds shop id to string.
     *
     * @param string $altBody  mail subject
     * @param bool   $clearSid clear sid in mail body (default value is true)
     */
    public function setAltBody($altBody = null, $clearSid = true)
    {
        if ($clearSid) {
            $altBody = $this->_clearSidFromBody($altBody);
        }

        // A. alt body is used for plain text emails so we should eliminate HTML entities
        $altBody = str_replace(['&amp;', '&quot;', '&#039;', '&lt;', '&gt;'], ['&', '"', "'", '<', '>'], $altBody);

        $this->set("AltBody", $altBody);
    }

    /**
     * Gets mail text-only body
     *
     * @return string
     */
    public function getAltBody()
    {
        return $this->AltBody;
    }

    /**
     * Sets mail recipient to recipients array
     *
     * @param string $address recipient email address
     * @param string $name    recipient name
     */
    public function setRecipient($address = null, $name = null)
    {
        try {
            $address = $this->idnToAscii($address);

            parent::addAddress($address, $name);

            // copying values as original class does not allow to access recipients array
            $this->_aRecipients[] = [$address, $name];
        } catch (Exception $exception) {
        }
    }

    /**
     * Gets recipients array.
     * Returns array of recipients
     * f.e. array( array('mail1@mail1.com', 'user1Name'), array('mail2@mail2.com', 'user2Name') )
     *
     * @return array
     */
    public function getRecipient()
    {
        return $this->_aRecipients;
    }

    /**
     * Clears all recipients assigned in the TO, CC and BCC array.
     */
    public function clearAllRecipients()
    {
        $this->_aRecipients = [];
        parent::clearAllRecipients();
    }

    /**
     * Sets user address and name to "reply to" array.
     * On error (wrong email) default shop email is added as a reply address.
     * Returns array of recipients
     * f.e. array( array('mail1@mail1.com', 'user1Name'), array('mail2@mail2.com', 'user2Name') )
     *
     * @param string $email email address
     * @param string $name  user name
     */
    public function setReplyTo($email = null, $name = null)
    {
        if (!oxNew(\OxidEsales\Eshop\Core\MailValidator::class)->isValidEmail($email)) {
            $email = $this->_getShop()->oxshops__oxorderemail->value;
        }

        $this->_aReplies[] = [$email, $name];

        try {
            parent::addReplyTo($email, $name);
        } catch (Exception $ex) {
        }
    }

    /**
     * Gets array of users for which reply is used.
     *
     * @return array
     */
    public function getReplyTo()
    {
        return $this->_aReplies;
    }

    /**
     * Clears all recipients assigned in the ReplyTo array.  Returns void.
     */
    public function clearReplyTos()
    {
        $this->_aReplies = [];
        parent::clearReplyTos();
    }

    /**
     * Preventing possible email spam over php mail() exploit (http://www.securephpwiki.com/index.php/Email_Injection)
     *
     * @param string $address
     * @param null   $name
     * @param bool   $auto
     *
     * @return bool
     */
    public function setFrom($address, $name = null, $auto = true)
    {
        $address = substr($address, 0, 150);
        $name = substr($name, 0, 150);

        $success = false;
        try {
            $success = parent::setFrom($address, $name, $auto);
        } catch (Exception $exception) {
        }

        return $success;
    }

    /**
     * Gets mail "from address" field.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->From;
    }

    /**
     * Gets mail "from name" field.
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->FromName;
    }

    /**
     * Sets mail charset.
     * If $charSet is not defined, sets charset from translation file.
     *
     * @param string $charSet email charset
     */
    public function setCharSet($charSet = null)
    {
        if ($charSet) {
            $this->_sCharSet = $charSet;
        } else {
            $this->_sCharSet = \OxidEsales\Eshop\Core\Registry::getLang()->translateString("charset");
        }
        $this->set("CharSet", $this->_sCharSet);
    }

    /**
     * Sets mail mailer. Set to send mail via smtp, mail() or sendmail.
     *
     * @param string $mailer email mailer
     */
    public function setMailer($mailer = null)
    {
        $this->set("Mailer", $mailer);
    }

    /**
     * Gets mail mailer.
     *
     * @return string
     */
    public function getMailer()
    {
        return $this->Mailer;
    }

    /**
     * Sets smtp host.
     *
     * @param string $host smtp host
     */
    public function setHost($host = null)
    {
        $this->set("Host", $host);
    }

    /**
     * Gets mailing error info.
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->ErrorInfo;
    }

    /**
     * Sets word wrapping on the body of the message to a given number of
     * characters
     *
     * @param int $wordWrap word wrap
     */
    public function setMailWordWrap($wordWrap = null)
    {
        $this->set("WordWrap", $wordWrap);
    }

    /**
     * Sets use inline images. If true, images will be embedded into mail.
     *
     * @param bool $useImages embed or not images into mail
     */
    public function setUseInlineImages($useImages = null)
    {
        $this->_blInlineImgEmail = $useImages;
    }

    /**
     * @param string $path
     * @param string $name
     * @param string $encoding
     * @param string $type
     * @param string $disposition
     *
     * @return bool
     */
    public function addAttachment(
        $path,
        $name = '',
        $encoding = 'base64',
        $type = 'application/octet-stream',
        $disposition = 'attachment'
    ) {
        $this->_aAttachments[] = [$path, $name, $encoding, $type, $disposition];
        $result = false;

        try {
            $result = parent::addAttachment($path, $name, $encoding, $type, $disposition);
        } catch (Exception $exception) {
        }

        return $result;
    }

    /**
     * @param string $path
     * @param string $cid
     * @param string $name
     * @param string $encoding
     * @param string $type
     * @param string $disposition
     *
     * @return bool
     */
    public function addEmbeddedImage(
        $path,
        $cid,
        $name = '',
        $encoding = 'base64',
        $type = 'application/octet-stream',
        $disposition = 'inline'
    ) {
        $this->_aAttachments[] = [
            $path,
            basename($path),
            $name,
            $encoding,
            $type,
            false,
            $disposition,
            $cid
        ];

        return parent::addEmbeddedImage($path, $cid, $name, $encoding, $type, $disposition);
    }

    /**
     * Gets mail attachment.
     *
     * @return array
     */
    public function getAttachments()
    {
        return $this->_aAttachments;
    }

    /**
     * Clears all attachments from mail.
     */
    public function clearAttachments()
    {
        $this->_aAttachments = [];
        parent::clearAttachments();
    }

    /**
     * Inherited phpMailer function adding a header to email message.
     * We override it to skip X-Mailer header.
     *
     * @param string $name  header name
     * @param string $value header value
     *
     * @return string|null
     */
    public function headerLine($name, $value)
    {
        if (stripos($name, 'X-') !== false) {
            return null;
        }

        return parent::headerLine($name, $value);
    }

    /**
     * Gets use inline images.
     *
     * @return bool
     */
    protected function _getUseInlineImages()
    {
        return $this->_blInlineImgEmail;
    }

    /**
     * Try to send error message when original mailing by smtp and via mail() fails
     *
     * @return bool
     */
    protected function _sendMailErrorMsg()
    {
        // build addresses
        $recipients = $this->getRecipient();

        $ownerMessage = "Error sending eMail(" . $this->getSubject() . ") to: \n\n";

        foreach ($recipients as $eMail) {
            $ownerMessage .= $eMail[0];
            $ownerMessage .= (!empty($eMail[1])) ? ' (' . $eMail[1] . ')' : '';
            $ownerMessage .= " \n ";
        }
        $ownerMessage .= "\n\nError : " . $this->getErrorInfo();

        // shop info
        $shop = $this->_getShop();

        return @mail($shop->oxshops__oxorderemail->value, "eMail problem in shop!", $ownerMessage);
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxEmail::sendOrderEMailToUser() to do
     * additional operation with order object before sending email
     *
     * @param \OxidEsales\Eshop\Application\Model\Order $order Ordering object
     *
     * @return \OxidEsales\Eshop\Application\Model\Order
     */
    protected function _addUserInfoOrderEMail($order)
    {
        return $order;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxEmail::SendRegisterEMail() to do
     * additional operation with user object before sending email
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user User object
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    protected function _addUserRegisterEmail($user)
    {
        return $user;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxemail::SendForgotPWDEMail() to do
     * additional operation with shop object before sending email
     *
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop Shop object
     *
     * @return \OxidEsales\Eshop\Application\Model\Shop
     */
    protected function _addForgotPwdEmail($shop)
    {
        return $shop;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxEmail::SendNewsletterDBOptInMail() to do
     * additional operation with user object before sending email
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user User object
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    protected function _addNewsletterDbOptInMail($user)
    {
        return $user;
    }

    /**
     * Clears mailer settings (AllRecipients, ReplyTos, Attachments, Errors)
     */
    protected function _clearMailer()
    {
        $this->clearAllRecipients();
        $this->clearReplyTos();
        $this->clearAttachments();

        $this->ErrorInfo = '';
    }

    /**
     * Set mail From, FromName, SMTP values
     *
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop Shop object
     */
    protected function _setMailParams($shop = null)
    {
        $this->_clearMailer();

        if (!$shop) {
            $shop = $this->_getShop();
        }

        $this->setFrom($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setSmtp($shop);
    }

    /**
     * Get active shop and set global params for it
     * If is set language parameter, load shop in given language
     *
     * @param int $langId language id
     * @param int $shopId shop id
     *
     * @return \OxidEsales\Eshop\Application\Model\Shop
     */
    protected function _getShop($langId = null, $shopId = null)
    {
        if ($langId === null && $shopId === null) {
            if (isset($this->_oShop)) {
                return $this->_oShop;
            } else {
                return $this->_oShop = $this->getConfig()->getActiveShop();
            }
        }

        $myConfig = $this->getConfig();

        $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
        if ($shopId !== null) {
            $shop->setShopId($shopId);
        }
        if ($langId !== null) {
            $shop->setLanguage($langId);
        }
        $shop->load($myConfig->getShopId());

        return $shop;
    }

    /**
     * Sets smtp authentification parameters.
     *
     * @param string                                   $userName     smtp user
     * @param \OxidEsales\Eshop\Application\Model\Shop $userPassword smtp password
     */
    protected function _setSmtpAuthInfo($userName = null, $userPassword = null)
    {
        $this->set("SMTPAuth", true);
        $this->set("Username", $userName);
        $this->set("Password", $userPassword);
    }

    /**
     * Sets SMTP class debugging on or off
     *
     * @param bool $debug show debug info or not
     */
    protected function _setSmtpDebug($debug = null)
    {
        $this->set("SMTPDebug", $debug);
    }

    /**
     * Process email body and alt body thought oxOutput.
     * Calls \OxidEsales\Eshop\Core\Output::processEmail() on class instance.
     */
    protected function _makeOutputProcessing()
    {
        $output = oxNew(\OxidEsales\Eshop\Core\Output::class);
        $this->setBody($output->process($this->getBody(), "oxemail"));
        $this->setAltBody($output->process($this->getAltBody(), "oxemail"));
        $output->processEmail($this);
    }

    /**
     * Sends email via phpmailer.
     *
     * @return bool
     */
    protected function _sendMail()
    {
        $result = false;
        try {
            $result = parent::send();
        } catch (Exception $exception) {
            $ex = oxNew(\OxidEsales\Eshop\Core\Exception\StandardException::class);
            $ex->setMessage($exception->getMessage());
            if ($this->isDebugModeEnabled()) {
                throw $ex;
            } else {
                $ex->debugOut();
            }
        }

        return $result;
    }

    /**
     * Process view data array through oxOutput processor
     */
    protected function _processViewArray()
    {
        $outputProcessor = oxNew(\OxidEsales\Eshop\Core\Output::class);

        // processing assigned smarty variables
        $newArray = $outputProcessor->processViewArray($this->_aViewData, "oxemail");

        $this->_aViewData = array_merge($this->_aViewData, $newArray);
    }

    /**
     * Get mail charset
     *
     * @return string
     */
    public function getCharset()
    {
        if (!$this->_sCharSet) {
            return \OxidEsales\Eshop\Core\Registry::getLang()->translateString("charset");
        } else {
            return $this->CharSet;
        }
    }

    /**
     * Get shop object
     *
     * @return \OxidEsales\Eshop\Application\Model\Shop
     */
    public function getShop()
    {
        return $this->_getShop();
    }

    /**
     * Set shop object
     *
     * @param \OxidEsales\Eshop\Application\Model\Shop $shop shop object
     */
    public function setShop($shop)
    {
        $this->_oShop = $shop;
    }

    /**
     * Gets viewConfig object
     *
     * @return object
     */
    public function getViewConfig()
    {
        return $this->getConfig()->getActiveView()->getViewConfig();
    }

    /**
     * Get active view
     *
     * @return object
     */
    public function getView()
    {
        return $this->getConfig()->getActiveView();
    }

    /**
     * Get active shop currency
     *
     * @return object
     */
    public function getCurrency()
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        return $config->getActShopCurrencyObject();
    }

    /**
     * Set view data to email view.
     *
     * @param string $key   key value
     * @param mixed  $value item value
     */
    public function setViewData($key, $value)
    {
        $this->_aViewData[$key] = $value;
    }

    /**
     * Get view data
     *
     * @return array
     */
    public function getViewData()
    {
        return $this->_aViewData;
    }

    /**
     * Get view data item
     *
     * @param string $key view data array key
     *
     * @return mixed
     */
    public function getViewDataItem($key)
    {
        if (isset($this->_aViewData[$key])) {
            return $this->_aViewData;
        }
    }

    /**
     * Set user to view data
     *
     * @param \OxidEsales\Eshop\Application\Model\User $user user object
     */
    public function setUser($user)
    {
        $this->_aViewData["oUser"] = $user;
    }

    /**
     * Get user
     *
     * @return \OxidEsales\Eshop\Application\Model\User
     */
    public function getUser()
    {
        return $this->_aViewData["oUser"];
    }

    /**
     * Get order files
     *
     * @param string $orderId order id
     *
     * @return bool|OrderFileList
     */
    public function getOrderFileList($orderId)
    {
        $orderFileList = oxNew(OrderFileList::class);
        $orderFileList->loadOrderFiles($orderId);

        if (count($orderFileList) > 0) {
            return $orderFileList;
        }

        return false;
    }

    /**
     * Performs search for "sid", removes it and adds shop id to string.
     *
     * @param string $altBody Body.
     *
     * @return string
     */
    private function _clearSidFromBody($altBody)
    {
        return Str::getStr()->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[A-Z0-9\.]+/i', '\1shp=' . $this->getConfig()->getShopId(), $altBody);
    }

    /**
     * @return \OxidEsales\Eshop\Core\UtilsObject
     */
    protected function getUtilsObjectInstance()
    {
        return \OxidEsales\Eshop\Core\Registry::getUtilsObject();
    }

    /**
     * Return true if debug mode is enabled.
     *
     * @return bool
     */
    private function isDebugModeEnabled()
    {
        return $this->getConfig()->getConfigParam('iDebug') != 0;
    }

    /**
     * @param string $userName
     * @param int    $shopId
     *
     * @return false|string
     */
    private function getUserIdByUserName($userName, $shopId)
    {
        $select = "SELECT `OXID` 
          FROM `oxuser` 
          WHERE `OXACTIVE` = 1 
          AND `OXUSERNAME` = :oxusername 
          AND `OXPASSWORD` != ''";
        if ($this->getConfig()->getConfigParam('blMallUsers')) {
            $select .= " ORDER BY OXSHOPID = :oxshopid DESC";
        } else {
            $select .= " AND OXSHOPID = :oxshopid";
        }

        $sOxId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($select, [
            ':oxusername' => $userName,
            ':oxshopid'   => $shopId
        ]);

        return $sOxId;
    }

    /**
     * @return bool
     */
    private function shouldProductReviewLinksBeIncluded(): bool
    {
        $config = \OxidEsales\Eshop\Core\Registry::getConfig();

        $reviewsEnabled = $config->getConfigParam('bl_perfLoadReviews', false);
        $productReviewLinkInclusionEnabled = $config->getConfigParam('includeProductReviewLinksInEmail', false);

        return $reviewsEnabled && $productReviewLinkInclusionEnabled;
    }

    /**
     * Convert domain name to IDNA ASCII form.
     *
     * @param string $idn The email address
     *
     * @return string
     */
    private function idnToAscii($idn)
    {
        if (function_exists('idn_to_ascii')) {
            // for old PHP versions support
            // remove it after the PHP 7.1 support is dropped
            if (defined('INTL_IDNA_VARIANT_UTS46')) {
                return idn_to_ascii($idn, 0, INTL_IDNA_VARIANT_UTS46);
            }

            return idn_to_ascii($idn);
        }

        return $idn;
    }
}
