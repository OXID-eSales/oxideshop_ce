<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core;

use Exception;
use OxidEsales\Eshop\Application\Model\OrderFileList;
use OxidEsales\Eshop\Core\DynamicImageGenerator;
use OxidEsales\Eshop\Core\Exception\SystemComponentException;
use OxidEsales\Eshop\Core\Field as OxidEsalesField;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\Eshop\Core\Str;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Domain\Admin\Event\AdminModeChangedEvent;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererBridgeInterface;
use OxidEsales\EshopCommunity\Internal\Framework\Templating\TemplateRendererInterface;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Email\EmailAdapterInterface;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceBridgeInterface;
use PHPMailer\PHPMailer\PHPMailer;
use Psr\Log\LoggerInterface;
use Symfony\Component\Mailer\MailerInterface;
use Throwable;

/**
 * Mailing manager.
 * Collects mailing configuration, other parameters, performs mailing functions (ordering, registration emails, etc.).
 */
class Email extends PHPMailer
{
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
    protected $_sForgotPwdTemplate = "email/html/forgotpwd";

    /**
     * Password reminder plain mail template
     *
     * @var string
     */
    protected $_sForgotPwdTemplatePlain = "email/plain/forgotpwd";

    /**
     * Newsletter registration mail template
     *
     * @var string
     */
    protected $_sNewsletterOptInTemplate = "email/html/newsletteroptin";

    /**
     * Newsletter registration plain mail template
     *
     * @var string
     */
    protected $_sNewsletterOptInTemplatePlain = "email/plain/newsletteroptin";

    /**
     * Product suggest mail template
     *
     * @var string
     */
    protected $_sInviteTemplate = "email/html/invite";

    /**
     * Product suggest plain mail template
     *
     * @var string
     */
    protected $_sInviteTemplatePlain = "email/plain/invite";

    /**
     * Send order notification mail template
     *
     * @var string
     */
    protected $_sSenedNowTemplate = "email/html/ordershipped";

    /**
     * Send order notification plain mail template
     *
     * @var string
     */
    protected $_sSenedNowTemplatePlain = "email/plain/ordershipped";

    /**
     * Send ordered download links mail template
     *
     * @var string
     */
    protected $_sSendDownloadsTemplate = "email/html/senddownloadlinks";

    /**
     * Send ordered download links plain mail template
     *
     * @var string
     */
    protected $_sSendDownloadsTemplatePlain = "email/plain/senddownloadlinks";

    /**
     * Wishlist mail template
     *
     * @var string
     */
    protected $_sWishListTemplate = "email/html/wishlist";

    /**
     * Wishlist plain mail template
     *
     * @var string
     */
    protected $_sWishListTemplatePlain = "email/plain/wishlist";

    /**
     * Name of template used during registration
     *
     * @var string
     */
    protected $_sRegisterTemplate = "email/html/register";

    /**
     * Name of plain template used during registration
     *
     * @var string
     */
    protected $_sRegisterTemplatePlain = "email/plain/register";

    /**
     * Name of template used by reminder function (article).
     *
     * @var string
     */
    protected $_sReminderMailTemplate = "email/html/owner_reminder";

    /**
     * Order e-mail for customer HTML template
     *
     * @var string
     */
    protected $_sOrderUserTemplate = "email/html/order_cust";

    /**
     * Order e-mail for customer plain text template
     *
     * @var string
     */
    protected $_sOrderUserPlainTemplate = "email/plain/order_cust";

    /**
     * Order e-mail for shop owner HTML template
     *
     * @var string
     */
    protected $_sOrderOwnerTemplate = "email/html/order_owner";

    /**
     * Order e-mail for shop owner plain text template
     *
     * @var string
     */
    protected $_sOrderOwnerPlainTemplate = "email/plain/order_owner";

    // #586A - additional templates for more customizable subjects

    /**
     * Order e-mail subject for customer template
     *
     * @var string
     */
    protected $_sOrderUserSubjectTemplate = "email/html/order_cust_subj";

    /**
     * Order e-mail subject for shop owner template
     *
     * @var string
     */
    protected $_sOrderOwnerSubjectTemplate = "email/html/order_owner_subj";

    /**
     * Price alarm e-mail for shop owner template
     *
     * @var string
     */
    protected $_sOwnerPricealarmTemplate = "email/html/pricealarm_owner";

    /**
     * Price alarm e-mail for shop owner template
     *
     * @var string
     */
    protected $_sPricealamrCustomerTemplate = "email_pricealarm_customer";

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

    public function __construct()
    {
        parent::__construct(true);

        $myConfig = Registry::getConfig();

        $this->setSmtp();
        $this::$validator = static function ($email) {
            return filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE) !== false;
        };

        $this->setUseInlineImages($myConfig->getConfigParam('blInlineImgEmail'));
        $this->setMailWordWrap(100);

        $this->isHTML();

        $this->setViewData('oEmailView', $this);
        $this->setViewData('shopUrl', $myConfig->getShopUrl());
        $this->setViewData('shopUrlWithLangAndSubshop', $myConfig->getShopUrl(null, false));

        $this->Encoding = 'base64';
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $method Methods name
     * @param array  $arguments Argument array
     * @return false|mixed
     * @throws SystemComponentException
     */
    public function __call($method, $arguments)
    {
        if (method_exists($this, $method)) {
            return call_user_func_array([&$this, $method], $arguments);
        }
        throw new SystemComponentException(
            "Function '$method' does not exist or is not accessible! (" . get_class($this) . ")" . PHP_EOL
        );
    }

    protected function getRenderer()
    {
        return ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
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
        if (count($this->getRecipient()) < 1) {
            return false;
        }

        $myConfig = Registry::getConfig();
        $this->setCharSet();

        if ($this->getUseInlineImages()) {
            $this->includeImages(
                $myConfig->getImageUrl(),
                $myConfig->getImageUrl(false, false),
                $myConfig->getPictureUrl(null, false),
                $myConfig->getImageDir(),
                $myConfig->getPictureDir(false)
            );
        }

        $this->makeOutputProcessing();

        if ($this->isSymfonyMailerEnabled()) {
            try {
                $symfonyEmail = ContainerFacade::get(EmailAdapterInterface::class)->convertToSymfonyEmail($this);
                ContainerFacade::get(MailerInterface::class)->send($symfonyEmail);

                return true;
            } catch (Throwable $e) {
                ContainerFacade::get(LoggerInterface::class)
                    ->error('Mailer failed, falling back to PHPMailer: ' . $e->getMessage(), [$e]);
            }
        }

        if ($this->getMailer() == 'smtp') {
            $ret = $this->sendMail();

            if (!$ret) {
                $this->sendMailErrorMsg();

                $this->setMailer('mail');
                $ret = $this->sendMail();
            }
        } else {
            $this->setMailer('mail');
            $ret = $this->sendMail();
        }

        if (!$ret) {
            $this->sendMailErrorMsg();
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
    protected function setSmtpProtocol($url)
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
        $shop = ($shop) ?: $this->getShop();

        $smtpUrl = $this->setSmtpProtocol($shop->oxshops__oxsmtp->value);

        if (!$this->isValidSmtpHost($smtpUrl)) {
            $this->setMailer("mail");

            return;
        }

        $this->setHost($smtpUrl);
        $this->setMailer("smtp");

        if ($shop->oxshops__oxsmtpuser->value) {
            $this->setSmtpAuthInfo($shop->oxshops__oxsmtpuser->value, $shop->oxshops__oxsmtppwd->getRawValue());
        }

        if (ContainerFacade::getParameter('oxid_esales.smtp_debug_mode')) {
            $this->setSmtpDebug(true);
        }
    }

    /**
     * Checks if smtp host is valid (tries to connect to it)
     *
     * @param string $smtpHost currently used smtp server host name
     *
     * @return bool
     */
    protected function isValidSmtpHost($smtpHost)
    {
        $isSmtp = false;
        if ($smtpHost) {
            $match = [];
            $smtpPort = $this->smtpPort;
            if (Str::getStr()->preg_match('@^(.*?)(:([0-9]+))?$@i', $smtpHost, $match)) {
                $smtpHost = $match[1];
                if (isset($match[3]) && (int) $match[3] !== 0) {
                    $smtpPort = (int) $match[3];
                }
            }
            if ($isSmtp = (bool) ($rHandle = @fsockopen($smtpHost, $smtpPort, $errNo, $errStr, 30))) {
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
        if ($this->areOrderEmailsDisabled()) {
            ContainerFacade::get(LoggerInterface::class)
                ->notice('Order email not sent to user due to disabled configuration option');
            return true;
        }
        $order = $this->addUserInfoOrderEMail($order);

        $shop = $this->getShop();
        $this->setMailParams($shop);

        $user = $order->getOrderUser();
        $this->setUser($user);

        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);

        $this->setViewData("blShowReviewLink", $this->shouldProductReviewLinksBeIncluded());

        $this->processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sOrderUserTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sOrderUserPlainTemplate, $this->getViewData()));

        // #586A
        if ($subject === null) {
            if ($renderer->exists($this->_sOrderUserSubjectTemplate)) {
                $subject = $renderer->renderTemplate($this->_sOrderUserSubjectTemplate, $this->getViewData());
            } else {
                $subject = $shop->oxshops__oxordersubject->getRawValue()
                    . " (#" . $order->oxorder__oxordernr->value . ")";
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
        if ($this->areOrderEmailsDisabled()) {
            ContainerFacade::get(LoggerInterface::class)
                ->notice('Order email not sent to owner due to disabled configuration option');
            return true;
        }
        $config = Registry::getConfig();

        $shop = $this->getShop();

        $this->clearMailer();

        $order = $this->addUserInfoOrderEMail($order);

        $user = $order->getOrderUser();
        $this->setUser($user);

        $this->setFrom($shop->oxshops__oxowneremail->value);

        $language = Registry::getLang();
        $orderLanguage = $language->getObjectTplLanguage();

        if ($shop->getLanguage() != $orderLanguage) {
            $shop = $this->getShop($orderLanguage);
        }

        $this->setSmtp($shop);

        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);

        $this->processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sOrderOwnerTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sOrderOwnerPlainTemplate, $this->getViewData()));

        // #586A
        if ($subject === null) {
            if ($renderer->exists($this->_sOrderOwnerSubjectTemplate)) {
                $subject = $renderer->renderTemplate($this->_sOrderOwnerSubjectTemplate, $this->getViewData());
            } else {
                $subject = $shop->oxshops__oxordersubject->getRawValue()
                    . " (#" . $order->oxorder__oxordernr->value . ")";
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
        $remark = oxNew(\OxidEsales\Eshop\Application\Model\Remark::class);
        $remark->oxremark__oxtext = new OxidEsalesField($this->getAltBody(), OxidEsalesField::T_RAW);
        $remark->oxremark__oxparentid = new OxidEsalesField($user->getId(), OxidEsalesField::T_RAW);
        $remark->oxremark__oxtype = new OxidEsalesField("o", OxidEsalesField::T_RAW);
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
        $this->setViewData("contentident", "oxregisteraltemail");
        $this->setViewData("contentplainident", "oxregisterplainaltemail");

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
        $user = $this->addUserRegisterEmail($user);

        $shop = $this->getShop();

        $this->setMailParams($shop);

        $renderer = $this->getRenderer();
        $this->setUser($user);

        $this->processViewArray();

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
        $shop = $this->addForgotPwdEmail($this->getShop());
        $oxid = $this->getUserIdByUserName($emailAddress, $shop->getId());
        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        if ($oxid && $user->load($oxid)) {
            $renderer = $this->getRenderer();
            $this->setUser($user);
            $this->processViewArray();

            $this->setMailParams($shop);
            $this->setBody($renderer->renderTemplate($this->_sForgotPwdTemplate, $this->getViewData()));
            $this->setAltBody($renderer->renderTemplate($this->_sForgotPwdTemplatePlain, $this->getViewData()));
            $this->setSubject(($subject !== null) ? $subject : $shop->oxshops__oxforgotpwdsubject->getRawValue());

            $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();
            $recipientAddress = $user->oxuser__oxusername->getRawValue();

            $this->setRecipient($recipientAddress, $fullName);
            $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

            if (!$this->send()) {
                $result = -1;
            } else {
                $result = true;
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
        $shop = $this->getShop();

        $this->setMailParams($shop);

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
        $user = $this->addNewsletterDbOptInMail($user);

        // shop info
        $shop = $this->getShop();

        $this->setMailParams($shop);

        $renderer = $this->getRenderer();
        $confirmCode = md5($user->oxuser__oxusername->value . $user->oxuser__oxpasssalt->value);
        $this->setViewData("subscribeLink", $this->getNewsSubsLink($user->oxuser__oxid->value, $confirmCode));
        $this->setUser($user);

        $this->processViewArray();

        $this->setBody($renderer->renderTemplate($this->_sNewsletterOptInTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sNewsletterOptInTemplatePlain, $this->getViewData()));
        $this->setSubject(
            ($subject !== null)
                ? $subject
                : Registry::getLang()->translateString("NEWSLETTER")
                . ' '
                . $shop->oxshops__oxname->getRawValue()
        );

        $fullName = $user->oxuser__oxfname->getRawValue() . " " . $user->oxuser__oxlname->getRawValue();

        $this->setRecipient($user->oxuser__oxusername->value, $fullName);
        $this->setFrom($shop->oxshops__oxinfoemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setReplyTo($shop->oxshops__oxinfoemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    protected function getNewsSubsLink($id, $confirmCode = null)
    {
        $myConfig = Registry::getConfig();
        $actShopLang = $myConfig->getActiveShop()->getLanguage();

        $url = $myConfig->getShopHomeUrl() . 'cl=newsletter&amp;fnc=addme&amp;uid=' . $id;
        $url .= '&amp;lang=' . $actShopLang;
        $url .= ($confirmCode) ? '&amp;confirm=' . $confirmCode : "";

        return $url;
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
        $myConfig = Registry::getConfig();

        $currLang = $myConfig->getActiveShop()->getLanguage();

        $shop = $this->getShop($currLang);

        $this->setFrom($user->send_email, $user->send_name);
        $this->setSmtp();

        /** @var TemplateRendererInterface $renderer */
        $renderer = ContainerFacade::get(TemplateRendererBridgeInterface::class)
            ->getTemplateRenderer();
        $this->setUser($user);

        $homeUrl = $this->getViewConfig()->getHomeLink();

        if ($myConfig->getActiveView()->isActive('Invitations') && $activeUser = $shop->getUser()) {
            $homeUrl = Registry::getUtilsUrl()->appendParamSeparator($homeUrl);
            $homeUrl .= "su=" . $activeUser->getId();
        }

        if (is_array($user->rec_email) && count($user->rec_email) > 0) {
            foreach ($user->rec_email as $email) {
                if (!empty($email)) {
                    $registerUrl = Registry::getUtilsUrl()->appendParamSeparator($homeUrl);
                    $registerUrl .= "re=" . md5($email);
                    $this->setViewData("sHomeUrl", $registerUrl);

                    $this->processViewArray();

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
        $myConfig = Registry::getConfig();

        $orderLang = (int) (isset($order->oxorder__oxlang->value) ? $order->oxorder__oxlang->value : 0);

        $shop = $this->getShop($orderLang);

        $this->setMailParams($shop);

        $lang = Registry::getLang();
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

        $this->processViewArray();

        $oldTplLang = $lang->getTplLanguage();
        $oldBaseLang = $lang->getBaseLanguage();
        $lang->setTplLanguage($orderLang);
        $lang->setBaseLanguage($orderLang);

        $this->switchToShopMode();
        $this->setBody($renderer->renderTemplate($this->_sSenedNowTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sSenedNowTemplatePlain, $this->getViewData()));
        $this->switchToAdminMode();
        $lang->setTplLanguage($oldTplLang);
        $lang->setBaseLanguage($oldBaseLang);

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
        $myConfig = Registry::getConfig();

        $orderLang = (int) (isset($order->oxorder__oxlang->value) ? $order->oxorder__oxlang->value : 0);

        $shop = $this->getShop($orderLang);

        $this->setMailParams($shop);

        $lang = Registry::getLang();
        $renderer = $this->getRenderer();
        $this->setViewData("order", $order);
        $this->setViewData("shopTemplateDir", $myConfig->getTemplateDir(false));

        $user = oxNew(\OxidEsales\Eshop\Application\Model\User::class);
        $this->setViewData("reviewuserhash", $user->getReviewUserHash($order->oxorder__oxuserid->value));

        $this->processViewArray();

        $oldTplLang = $lang->getTplLanguage();
        $oldBaseLang = $lang->getTplLanguage();
        $lang->setTplLanguage($orderLang);
        $lang->setBaseLanguage($orderLang);

        $this->switchToShopMode();
        $this->setBody($renderer->renderTemplate($this->_sSendDownloadsTemplate, $this->getViewData()));
        $this->setAltBody($renderer->renderTemplate($this->_sSendDownloadsTemplatePlain, $this->getViewData()));
        $this->switchToAdminMode();
        $lang->setTplLanguage($oldTplLang);
        $lang->setBaseLanguage($oldBaseLang);

        $this->setSubject(($subject !== null) ? $subject : $lang->translateString("DOWNLOAD_LINKS", null, false));

        $fullName = $order->oxorder__oxbillfname->getRawValue() . " " . $order->oxorder__oxbilllname->getRawValue();

        $this->setRecipient($order->oxorder__oxbillemail->value, $fullName);
        $this->setReplyTo($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());

        return $this->send();
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
        $this->setMailParams();

        if (is_array($to)) {
            foreach ($to as $address) {
                $this->setRecipient($address, "");
                $this->setReplyTo($address, "");
            }
        } else {
            $this->setRecipient($to, "");
            $this->setReplyTo($to, "");
        }

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
            $shop = $this->getShop();

            $this->setMailParams($shop);
            $lang = Registry::getLang();

            $renderer = $this->getRenderer();
            $this->setViewData("articles", $articleList);

            $this->processViewArray();

            $this->setRecipient($shop->oxshops__oxowneremail->value, $shop->oxshops__oxname->getRawValue());
            $this->setFrom($shop->oxshops__oxowneremail->value, $shop->oxshops__oxname->getRawValue());
            $this->setBody($renderer->renderTemplate($this->_sReminderMailTemplate, $this->getViewData()));
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
        $this->clearMailer();

        $this->setFrom($params->send_email, $params->send_name);
        $this->setSmtp();

        $renderer = $this->getRenderer();
        $this->setUser($params);

        $this->processViewArray();

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
        $this->clearMailer();
        $shop = $this->getShop();

        $this->setMailParams($shop);

        $alarmLang = $alarm->oxpricealarm__oxlang->value;

        $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
        $article->loadInLang($alarmLang, $params['aid']);
        $lang = Registry::getLang();

        $renderer = $this->getRenderer();
        $this->setViewData("product", $article);
        $this->setViewData("email", $params['email']);
        $this->setViewData("bidprice", $lang->formatCurrency($alarm->oxpricealarm__oxprice->value));

        $this->processViewArray();

        $this->setRecipient($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setSubject(
            ($subject !== null)
                ? $subject
                : $lang->translateString('PRICE_ALERT_FOR_PRODUCT', $alarmLang)
                . ' '
                . $article->oxarticles__oxtitle->getRawValue()
        );
        $this->setBody($renderer->renderTemplate($this->_sOwnerPricealarmTemplate, $this->getViewData()));
        $this->setFrom($params['email'], "");
        $this->setReplyTo($params['email'], "");

        return $this->send();
    }

    public function sendPricealarmToCustomer($recipient, $alarm, $body = null, $returnMailBody = null)
    {
        $this->clearMailer();

        $shop = $this->getShop();

        if ($shop->getId() != $alarm->oxpricealarm__oxshopid->value) {
            $shop = oxNew(\OxidEsales\Eshop\Application\Model\Shop::class);
            $shop->load($alarm->oxpricealarm__oxshopid->value);
            $this->setShop($shop);
        }

        $this->setMailParams($shop);

        $renderer = $this->getRenderer();

        $this->setViewData("product", $alarm->getArticle());
        $this->setViewData("oPriceAlarm", $alarm);
        $this->setViewData("bidprice", $alarm->getFProposedPrice());
        $this->setViewData("currency", $alarm->getPriceAlarmCurrency());

        $this->processViewArray();

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

    protected function includeImages(
        $imageDir = null,
        $imageDirNoSSL = null,
        $dynImageDir = null,
        $absImageDir = null,
        $absDynImageDir = null
    ) {
        $body = $this->getBody();
        if (
            preg_match_all(
                '/<\s*img\s+[^>]*?src[\s]*=[\s]*[\'"]?([^[\'">]]+|.*?)?[\'">]/i',
                $body,
                $matches,
                PREG_SET_ORDER
            )
        ) {
            $fileUtils = Registry::getUtilsFile();
            $reSetBody = false;

            // preparing input
            $dynImageDir = $fileUtils->normalizeDir($dynImageDir);
            $imageDir = $fileUtils->normalizeDir($imageDir);
            $imageDirNoSSL = $fileUtils->normalizeDir($imageDirNoSSL);

            if (is_array($matches) && count($matches)) {
                $imageCache = [];
                $myUtils = Registry::getUtils();
                $myUtilsObject = $this->getUtilsObjectInstance();
                $imgGenerator = oxNew(DynamicImageGenerator::class);

                foreach ($matches as $image) {
                    $imageName = $image[1];
                    $fileName = '';
                    if (is_string($dynImageDir) && str_starts_with($imageName, $dynImageDir)) {
                        $fileName = $fileUtils->normalizeDir($absDynImageDir)
                            . str_replace($dynImageDir, '', $imageName);
                    } elseif (str_starts_with($imageName, $imageDir)) {
                        $fileName = $fileUtils->normalizeDir($absImageDir) . str_replace($imageDir, '', $imageName);
                    } elseif (str_starts_with($imageName, $imageDirNoSSL)) {
                        $fileName = $fileUtils->normalizeDir($absImageDir)
                            . str_replace($imageDirNoSSL, '', $imageName);
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
                            if (in_array($mIME, ['image/jpeg', 'image/gif', 'image/png', 'image/webp'])) {
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

    public function setSubject($subject = null)
    {
        // A. HTML entities in subjects must be replaced
        $subject = str_replace(['&amp;', '&quot;', '&#039;', '&lt;', '&gt;'], ['&', '"', "'", '<', '>'], $subject);

        $this->set("Subject", $subject);
    }

    public function getSubject()
    {
        return $this->Subject;
    }

    public function setBody($body = null, $clearSid = true)
    {
        if ($clearSid) {
            $body = $this->clearSidFromBody($body);
        }

        $this->set("Body", $body);
    }

    public function getBody()
    {
        return $this->Body;
    }

    public function setAltBody($altBody = null, $clearSid = true)
    {
        if ($clearSid) {
            $altBody = $this->clearSidFromBody($altBody);
        }

        // A. alt body is used for plain text emails so we should eliminate HTML entities
        $altBody = str_replace(['&amp;', '&quot;', '&#039;', '&lt;', '&gt;'], ['&', '"', "'", '<', '>'], $altBody);

        $this->set("AltBody", $altBody);
    }

    public function getAltBody()
    {
        return $this->AltBody;
    }

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

    public function getRecipient()
    {
        return $this->_aRecipients;
    }

    public function getCc(): array
    {
        return $this->cc;
    }

    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function clearAllRecipients()
    {
        $this->_aRecipients = [];
        parent::clearAllRecipients();
    }

    public function setReplyTo($email = null, $name = null)
    {
        $emailValidator = ContainerFacade::get(EmailValidatorServiceBridgeInterface::class);
        if (!$emailValidator->isEmailValid($email)) {
            $email = $this->getShop()->oxshops__oxorderemail->value;
        }

        $this->_aReplies[] = [$email, $name];

        try {
            parent::addReplyTo($email, $name);
        } catch (Exception $ex) {
        }
    }

    public function getReplyTo()
    {
        return $this->_aReplies;
    }

    public function clearReplyTos()
    {
        $this->_aReplies = [];
        parent::clearReplyTos();
    }

    public function setFrom($address, $name = '', $auto = true)
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

    public function getFrom()
    {
        return $this->From;
    }

    public function getFromName()
    {
        return $this->FromName;
    }

    public function setCharSet($charSet = null)
    {
        if ($charSet) {
            $this->_sCharSet = $charSet;
        } else {
            $this->_sCharSet = Registry::getLang()->translateString("charset");
        }
        $this->set("CharSet", $this->_sCharSet);
    }

    public function setMailer($mailer = null)
    {
        $this->set("Mailer", $mailer);
    }

    public function getMailer()
    {
        return $this->Mailer;
    }

    public function setHost($host = null)
    {
        $this->set("Host", $host);
    }

    public function getErrorInfo()
    {
        return $this->ErrorInfo;
    }

    public function setMailWordWrap($wordWrap = null)
    {
        $this->set("WordWrap", $wordWrap);
    }

    public function setUseInlineImages($useImages = null)
    {
        $this->_blInlineImgEmail = $useImages;
    }

    public function headerLine($name, $value)
    {
        if (stripos($name, 'X-') !== false) {
            return null;
        }

        return parent::headerLine($name, $value);
    }

    protected function getUseInlineImages()
    {
        return $this->_blInlineImgEmail;
    }

    protected function sendMailErrorMsg()
    {
        $recipients = $this->getRecipient();

        $ownerMessage = "Error sending eMail(" . $this->getSubject() . ") to: \n\n";

        foreach ($recipients as $eMail) {
            $ownerMessage .= $eMail[0];
            $ownerMessage .= (!empty($eMail[1])) ? ' (' . $eMail[1] . ')' : '';
            $ownerMessage .= " \n ";
        }
        $ownerMessage .= "\n\nError : " . $this->getErrorInfo();

        $shop = $this->getShop();

        return @mail($shop->oxshops__oxorderemail->value, "eMail problem in shop!", $ownerMessage);
    }

    protected function addUserInfoOrderEMail($order)
    {
        return $order;
    }

    protected function addUserRegisterEmail($user)
    {
        return $user;
    }

    protected function addForgotPwdEmail($shop)
    {
        return $shop;
    }

    protected function addNewsletterDbOptInMail($user)
    {
        return $user;
    }

    protected function clearMailer()
    {
        $this->clearAllRecipients();
        $this->clearReplyTos();
        $this->clearAttachments();

        $this->ErrorInfo = '';
    }

    protected function setMailParams($shop = null)
    {
        $this->clearMailer();

        if (!$shop) {
            $shop = $this->getShop();
        }

        $this->setFrom($shop->oxshops__oxorderemail->value, $shop->oxshops__oxname->getRawValue());
        $this->setSmtp($shop);
    }

    public function getShop($langId = null, $shopId = null)
    {
        if ($langId === null && $shopId === null) {
            if (isset($this->_oShop)) {
                return $this->_oShop;
            } else {
                return $this->_oShop = Registry::getConfig()->getActiveShop();
            }
        }

        $myConfig = Registry::getConfig();

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

    protected function setSmtpAuthInfo($userName = null, $userPassword = null)
    {
        $this->set("SMTPAuth", true);
        $this->set("Username", $userName);
        $this->set("Password", $userPassword);
    }

    protected function setSmtpDebug($debug = null)
    {
        $this->set("SMTPDebug", $debug);
    }

    protected function makeOutputProcessing()
    {
    }

    protected function sendMail()
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
                Registry::getLogger()->error($ex->getMessage(), [$ex]);
            }
        }

        return $result;
    }

    protected function processViewArray()
    {
    }

    public function getCharset()
    {
        if (!$this->_sCharSet) {
            return Registry::getLang()->translateString("charset");
        } else {
            return $this->CharSet;
        }
    }

    public function setShop($shop)
    {
        $this->_oShop = $shop;
    }

    public function getViewConfig()
    {
        return Registry::getConfig()->getActiveView()->getViewConfig();
    }

    public function getView()
    {
        return Registry::getConfig()->getActiveView();
    }

    public function getCurrency()
    {
        $config = Registry::getConfig();

        return $config->getActShopCurrencyObject();
    }

    public function setViewData($key, $value)
    {
        $this->_aViewData[$key] = $value;
    }

    public function getViewData()
    {
        return $this->_aViewData;
    }

    public function getViewDataItem($key)
    {
        if (isset($this->_aViewData[$key])) {
            return $this->_aViewData;
        }
    }

    public function setUser($user)
    {
        $this->_aViewData["oUser"] = $user;
    }

    public function getUser()
    {
        return $this->_aViewData["oUser"];
    }

    public function getOrderFileList($orderId)
    {
        $orderFileList = oxNew(OrderFileList::class);
        $orderFileList->loadOrderFiles($orderId);

        if (count($orderFileList) > 0) {
            return $orderFileList;
        }

        return false;
    }

    private function clearSidFromBody($altBody)
    {
        return Str::getStr()->preg_replace(
            '/(\?|&(amp;)?)(force_)?(admin_)?sid=[A-Z0-9\.]+/i',
            '\1shp=' . Registry::getConfig()->getShopId(),
            $altBody
        );
    }

    protected function getUtilsObjectInstance()
    {
        return Registry::getUtilsObject();
    }

    private function isDebugModeEnabled()
    {
        return ContainerFacade::getParameter('oxid_esales.debug_mode');
    }

    private function getUserIdByUserName($userName, $shopId)
    {
        $select = "SELECT `OXID` 
          FROM `oxuser` 
          WHERE `OXACTIVE` = 1 
          AND `OXUSERNAME` = :oxusername 
          AND `OXPASSWORD` != ''";
        if (Registry::getConfig()->getConfigParam('blMallUsers')) {
            $select .= " ORDER BY OXSHOPID = :oxshopid DESC";
        } else {
            $select .= " AND OXSHOPID = :oxshopid";
        }

        $sOxId = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->getOne($select, [
            'oxusername' => $userName,
            'oxshopid'   => $shopId
        ]);

        return $sOxId;
    }

    private function shouldProductReviewLinksBeIncluded(): bool
    {
        $config = Registry::getConfig();

        $reviewsEnabled = $config->getConfigParam('bl_perfLoadReviews', false);
        $productReviewLinkInclusionEnabled = $config->getConfigParam('includeProductReviewLinksInEmail', false);

        return $reviewsEnabled && $productReviewLinkInclusionEnabled;
    }

    private function idnToAscii($idn)
    {
        if (function_exists('idn_to_ascii')) {
            $parts = explode('@', $idn);
            return $parts[0] . '@' . idn_to_ascii($parts[1]);
        }

        return $idn;
    }

    private function switchToShopMode(): void
    {
        Registry::getConfig()->setAdminMode(false);
        $this->dispatchAdminModeChangedEvent();
    }

    private function switchToAdminMode(): void
    {
        Registry::getConfig()->setAdminMode(true);
        $this->dispatchAdminModeChangedEvent();
    }

    private function dispatchAdminModeChangedEvent(): void
    {
        ContainerFacade::dispatch(new AdminModeChangedEvent());
    }

    private function areOrderEmailsDisabled(): bool
    {
        return ContainerFacade::hasParameter('oxid_esales.email.disable_order_emails')
            && ContainerFacade::getParameter('oxid_esales.email.disable_order_emails');
    }

    private function isSymfonyMailerEnabled(): bool
    {
        return ContainerFacade::hasParameter('oxid_esales.mailing.use_symfony_mailer')
            && ContainerFacade::getParameter('oxid_esales.mailing.use_symfony_mailer');
    }
}
