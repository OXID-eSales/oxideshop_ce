<?php
/**
 * This file is part of OXID eShop Community Edition.
 *
 * OXID eShop Community Edition is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Community Edition is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop CE
 */
use OxidEsales\Eshop\Core\MailClientInterface;

/**
 * Mailing manager.
 * Collects mailing configuration, other parameters, performs mailing functions
 * (newsletters, ordering, registration emails, etc.).
 */
class oxEmail
{
    /**
     * Password reminder mail template
     *
     * @var string
     */
    protected $_sForgotPwdTemplate = 'email/html/forgotpwd.tpl';

    /**
     * Password reminder plain mail template
     *
     * @var string
     */
    protected $_sForgotPwdTemplatePlain = 'email/plain/forgotpwd.tpl';

    /**
     * Newsletter registration mail template
     *
     * @var string
     */
    protected $_sNewsletterOptInTemplate = 'email/html/newsletteroptin.tpl';

    /**
     * Newsletter registration plain mail template
     *
     * @var string
     */
    protected $_sNewsletterOptInTemplatePlain = 'email/plain/newsletteroptin.tpl';

    /**
     * Product suggest mail template
     *
     * @var string
     */
    protected $_sSuggestTemplate = 'email/html/suggest.tpl';

    /**
     * Product suggest plain mail template
     *
     * @var string
     */
    protected $_sSuggestTemplatePlain = 'email/plain/suggest.tpl';

    /**
     * Product suggest mail template
     *
     * @var string
     */
    protected $_sInviteTemplate = 'email/html/invite.tpl';

    /**
     * Product suggest plain mail template
     *
     * @var string
     */
    protected $_sInviteTemplatePlain = 'email/plain/invite.tpl';

    /**
     * Send order notification mail template
     *
     * @var string
     */
    protected $_sSenedNowTemplate = 'email/html/ordershipped.tpl';

    /**
     * Send order notification plain mail template
     *
     * @var string
     */
    protected $_sSenedNowTemplatePlain = 'email/plain/ordershipped.tpl';

    /**
     * Send ordered download links mail template
     *
     * @var string
     */
    protected $_sSendDownloadsTemplate = 'email/html/senddownloadlinks.tpl';

    /**
     * Send ordered download links plain mail template
     *
     * @var string
     */
    protected $_sSendDownloadsTemplatePlain = 'email/plain/senddownloadlinks.tpl';

    /**
     * Wishlist mail template
     *
     * @var string
     */
    protected $_sWishListTemplate = 'email/html/wishlist.tpl';

    /**
     * Wishlist plain mail template
     *
     * @var string
     */
    protected $_sWishListTemplatePlain = 'email/plain/wishlist.tpl';

    /**
     * Name of template used during registration
     *
     * @var string
     */
    protected $_sRegisterTemplate = 'email/html/register.tpl';

    /**
     * Name of plain template used during registration
     *
     * @var string
     */
    protected $_sRegisterTemplatePlain = 'email/plain/register.tpl';

    /**
     * Name of template used by reminder function (article).
     *
     * @var string
     */
    protected $_sReminderMailTemplate = 'email/html/owner_reminder.tpl';

    /**
     * Order e-mail for customer HTML template
     *
     * @var string
     */
    protected $_sOrderUserTemplate = 'email/html/order_cust.tpl';

    /**
     * Order e-mail for customer plain text template
     *
     * @var string
     */
    protected $_sOrderUserPlainTemplate = 'email/plain/order_cust.tpl';

    /**
     * Order e-mail for shop owner HTML template
     *
     * @var string
     */
    protected $_sOrderOwnerTemplate = 'email/html/order_owner.tpl';

    /**
     * Order e-mail for shop owner plain text template
     *
     * @var string
     */
    protected $_sOrderOwnerPlainTemplate = 'email/plain/order_owner.tpl';

    // #586A - additional templates for more customizable subjects

    /**
     * Order e-mail subject for customer template
     *
     * @var string
     */
    protected $_sOrderUserSubjectTemplate = 'email/html/order_cust_subj.tpl';

    /**
     * Order e-mail subject for shop owner template
     *
     * @var string
     */
    protected $_sOrderOwnerSubjectTemplate = 'email/html/order_owner_subj.tpl';

    /**
     * Price alarm e-mail for shop owner template
     *
     * @var string
     */
    protected $_sOwnerPricealarmTemplate = 'email/html/pricealarm_owner.tpl';

    /**
     * Price alarm e-mail for shop owner template
     *
     * @var string
     */
    protected $_sPricealamrCustomerTemplate = 'email_pricealarm_customer.tpl';

    /**
     * Language specific viewconfig object array containing view data, view config and shop object
     *
     * @var array
     */
    protected $_aShops = array();

    /**
     * Add inline images to mail
     *
     * @var bool
     */
    protected $_blInlineImgEmail;

    /**
     * Array of recipient email addresses
     *
     * @var array
     */
    protected $_aRecipients = array();

    /**
     * Array of reply addresses used
     *
     * @var array
     */
    protected $_aReplies = array();

    /**
     * Attachment info array
     *
     * @var array
     */
    protected $_aAttachments = array();

    /**
     * Smarty instance
     *
     * @var smarty
     */
    protected $_oSmarty;

    /**
     * Email view data
     *
     * @var array
     */
    protected $_aViewData = array();

    /**
     * Shop object
     *
     * @var object
     */
    protected $_oShop;

    /** @var oxConfig */
    protected $_oConfig;

    private $mailer;

    /**
     * Class constructor.
     */
    public function __construct(MailClientInterface $mailer)
    {
        $this->mailer = $mailer;

        $this->setUseInlineImages(oxRegistry::getConfig()->getConfigParam('blInlineImgEmail'));
        $this->_getSmarty();
    }

    /**
     * Only used for convenience in UNIT tests by doing so we avoid
     * writing extended classes for testing protected or private methods
     *
     * @param string $sMethod Methods name
     * @param array  $aArgs   Argument array
     *
     * @throws oxSystemComponentException Throws an exception if the called method does not exist or is not accessible in current class
     *
     * @return string
     */
    public function __call($sMethod, $aArgs)
    {
        if (defined('OXID_PHP_UNIT')) {
            if (substr($sMethod, 0, 4) == 'UNIT') {
                $sMethod = str_replace('UNIT', '_', $sMethod);
            }
            if (method_exists($this, $sMethod)) {
                return call_user_func_array(array(& $this, $sMethod), $aArgs);
            }
        }

        throw new oxSystemComponentException("Function '$sMethod' does not exist or is not accessible! (" . get_class($this) . ')' . PHP_EOL);
    }

    /**
     * oxConfig instance getter
     *
     * @return oxConfig
     */
    public function getConfig()
    {
        if ($this->_oConfig == null) {
            $this->_oConfig = oxRegistry::getConfig();
        }

        return $this->_oConfig;
    }

    /**
     * oxConfig instance setter
     *
     * @param oxConfig $oConfig config object
     */
    public function setConfig($oConfig)
    {
        $this->_oConfig = $oConfig;
    }


    /**
     * Smarty instance getter, assigns this oxEmail instance to 'oEmailView' variable
     *
     * @return smarty
     */
    protected function _getSmarty()
    {
        if ($this->_oSmarty === null) {
            $this->_oSmarty = oxRegistry::get('oxUtilsView')->getSmarty();
        }

        //setting default view
        $this->_oSmarty->assign('oEmailView', $this);

        return $this->_oSmarty;
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

        if ($this->_getUseInlineImages()) {
            $this->_includeImages(
                $myConfig->getImageDir(), $myConfig->getImageUrl(false, false), $myConfig->getPictureUrl(null, false),
                $myConfig->getImageDir(), $myConfig->getPictureDir(false)
            );
        }

        $this->_makeOutputProcessing();

        // try to send mail via SMTP
        if ($this->getMailer() == 'smtp') {
            $blRet = $this->_sendMail();

            // if sending failed, try to send via mail()
            if (!$blRet) {
                // failed sending via SMTP, sending notification to shop owner
                $this->_sendMailErrorMsg();

                // trying to send using standard mailer
                $this->setMailer('mail');
                $blRet = $this->_sendMail();
            }
        } else {
            // sending mail via mail()
            $this->setMailer('mail');
            $blRet = $this->_sendMail();
        }

        if (!$blRet) {
            // failed sending, giving up, trying to send notification to shop owner
            $this->_sendMailErrorMsg();
        }

        return $blRet;
    }

    /**
     * Sets mailer additional settings and sends ordering mail to user.
     * Returns true on success.
     *
     * @param oxOrder $oOrder   Order object
     * @param string  $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendOrderEmailToUser($oOrder, $sSubject = null)
    {
        $myConfig = $this->getConfig();

        // add user defined stuff if there is any
        $oOrder = $this->_addUserInfoOrderEMail($oOrder);

        $oShop = $this->_getShop();

        $oUser = $oOrder->getOrderUser();
        $this->setUser($oUser);

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData('order', $oOrder);

        if ($myConfig->getConfigParam('bl_perfLoadReviews')) {
            $this->setViewData('blShowReviewLink', true);
        }

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($oSmarty->fetch($this->_sOrderUserTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sOrderUserPlainTemplate));

        // #586A
        if ($sSubject === null) {
            if ($oSmarty->template_exists($this->_sOrderUserSubjectTemplate)) {
                $sSubject = $oSmarty->fetch($this->_sOrderUserSubjectTemplate);
            } else {
                $sSubject = $oShop->oxshops__oxordersubject->getRawValue() . ' (#' . $oOrder->oxorder__oxordernr->value . ')';
            }
        }

        $this->setSubject($sSubject);

        $sFullName = $oUser->oxuser__oxfname->getRawValue() . ' ' . $oUser->oxuser__oxlname->getRawValue();

        $this->setRecipient($oUser->oxuser__oxusername->value, $sFullName);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        $blSuccess = $this->send();

        return $blSuccess;
    }

    /**
     * Sets mailer additional settings and sends ordering mail to shop owner.
     * Returns true on success.
     *
     * @param oxOrder $order   Order object
     * @param string  $subject user defined subject [optional]
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
        $this->mailer->setFromAddress($shop->oxshops__oxowneremail->value);

        $language = oxRegistry::getLang();
        $orderLanguage = $language->getObjectTplLanguage();

        // if running shop language is different from admin lang. set in config
        // we have to load shop in config language
        if ($shop->getLanguage() != $orderLanguage) {
            $shop = $this->_getShop($orderLanguage);
        }

        // create messages
        $smarty = $this->_getSmarty();
        $this->setViewData('order', $order);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setBody($smarty->fetch($config->getTemplatePath($this->_sOrderOwnerTemplate, false)));
        $this->setAltBody($smarty->fetch($config->getTemplatePath($this->_sOrderOwnerPlainTemplate, false)));

        //Sets subject to email
        // #586A
        if ($subject === null) {
            if ($smarty->template_exists($this->_sOrderOwnerSubjectTemplate)) {
                $subject = $smarty->fetch($this->_sOrderOwnerSubjectTemplate);
            } else {
                $subject = $shop->oxshops__oxordersubject->getRawValue() . ' (#' . $order->oxorder__oxordernr->value . ')';
            }
        }

        $this->setSubject($subject);
        $this->setRecipient($shop->oxshops__oxowneremail->value, $language->translateString('order'));

        if ($user->oxuser__oxusername->value != 'admin') {
            $fullName = $user->oxuser__oxfname->getRawValue() . ' ' . $user->oxuser__oxlname->getRawValue();
            $this->setReplyTo($user->oxuser__oxusername->value, $fullName);
        }

        $result = $this->send();

        $this->onOrderEmailToOwnerSent($user, $order);

        if ($config->getConfigParam('iDebug') == 6) {
            oxRegistry::getUtils()->showMessageAndExit('');
        }

        return $result;
    }

    /**
     * Method is called when order email is sent to owner.
     *
     * @param oxUser  $user
     * @param oxOrder $order
     */
    protected function onOrderEmailToOwnerSent($user, $order)
    {
        // add user history
        $remark = oxNew('oxRemark');
        $remark->oxremark__oxtext = new oxField($this->getAltBody(), oxField::T_RAW);
        $remark->oxremark__oxparentid = new oxField($user->getId(), oxField::T_RAW);
        $remark->oxremark__oxtype = new oxField('o', oxField::T_RAW);
        $remark->save();
    }

    /**
     * Sets mailer additional settings and sends registration mail to user.
     * Returns true on success.
     *
     * @param oxUser $oUser    user object
     * @param string $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendRegisterConfirmEmail($oUser, $sSubject = null)
    {
        // setting content ident

        $this->setViewData('contentident', 'oxregisteraltemail');
        $this->setViewData('contentplainident', 'oxregisterplainaltemail');

        // sending email
        return $this->sendRegisterEmail($oUser, $sSubject);
    }

    /**
     * Sets mailer additional settings and sends registration mail to user.
     * Returns true on success.
     *
     * @param oxUser $oUser    user object
     * @param string $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendRegisterEmail($oUser, $sSubject = null)
    {
        // add user defined stuff if there is any
        $oUser = $this->_addUserRegisterEmail($oUser);

        // shop info
        $oShop = $this->_getShop();


        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setUser($oUser);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($oSmarty->fetch($this->_sRegisterTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sRegisterTemplatePlain));

        $this->setSubject(($sSubject !== null) ? $sSubject : $oShop->oxshops__oxregistersubject->getRawValue());

        $sFullName = $oUser->oxuser__oxfname->getRawValue() . ' ' . $oUser->oxuser__oxlname->getRawValue();

        $this->setRecipient($oUser->oxuser__oxusername->value, $sFullName);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends 'forgot password' mail to user.
     * Returns true on success.
     *
     * @param string $sEmailAddress user email address
     * @param string $sSubject      user defined subject [optional]
     *
     * @return mixed true - success, false - user not found, -1 - could not send
     */
    public function sendForgotPwdEmail($sEmailAddress, $sSubject = null)
    {
        $myConfig = $this->getConfig();
        $oDb = oxDb::getDb();

        // shop info
        $oShop = $this->_getShop();

        // add user defined stuff if there is any
        $oShop = $this->_addForgotPwdEmail($oShop);


        // user
        $sWhere = 'oxuser.oxactive = 1 and oxuser.oxusername = ' . $oDb->quote($sEmailAddress) . ' and oxuser.oxpassword != ""';
        $sOrder = '';
        if ($myConfig->getConfigParam('blMallUsers')) {
            $sOrder = 'order by oxshopid = "' . $oShop->getId() . '" desc';
        } else {
            $sWhere .= ' and oxshopid = "' . $oShop->getId() . '"';
        }

        $sSelect = "select oxid from oxuser where $sWhere $sOrder";
        if (($sOxId = $oDb->getOne($sSelect))) {
            $oUser = oxNew('oxuser');
            if ($oUser->load($sOxId)) {
                // create messages
                $oSmarty = $this->_getSmarty();
                $this->setUser($oUser);

                // Process view data array through oxoutput processor
                $this->_processViewArray();

                $this->setBody($oSmarty->fetch($this->_sForgotPwdTemplate));

                $this->setAltBody($oSmarty->fetch($this->_sForgotPwdTemplatePlain));

                //sets subject of email
                $this->setSubject(($sSubject !== null) ? $sSubject : $oShop->oxshops__oxforgotpwdsubject->getRawValue());

                $sFullName = $oUser->oxuser__oxfname->getRawValue() . ' ' . $oUser->oxuser__oxlname->getRawValue();

                $this->setRecipient($sEmailAddress, $sFullName);
                $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

                if (!$this->send()) {
                    return -1; // failed to send
                }

                return true; // success
            }
        }

        return false; // user with this email not found
    }

    /**
     * Sets mailer additional settings and sends contact info mail to user.
     * Returns true on success.
     *
     * @param string $sEmailAddress Email address
     * @param string $sSubject      Email subject
     * @param string $sMessage      Email message text
     *
     * @return bool
     */
    public function sendContactMail($sEmailAddress = null, $sSubject = null, $sMessage = null)
    {

        // shop info
        $oShop = $this->_getShop();


        $this->setBody($sMessage);
        $this->setSubject($sSubject);

        $this->setRecipient($oShop->oxshops__oxinfoemail->value, '');
        $this->setFromAddress($oShop->oxshops__oxowneremail->value, $oShop->oxshops__oxname->getRawValue());
        $this->setReplyTo($sEmailAddress, '');

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends 'NewsletterDBOptInMail' mail to user.
     * Returns true on success.
     *
     * @param oxUser $oUser    user object
     * @param string $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendNewsletterDbOptInMail($oUser, $sSubject = null)
    {
        // add user defined stuff if there is any
        $oUser = $this->_addNewsletterDbOptInMail($oUser);

        // shop info
        $oShop = $this->_getShop();

        // create messages
        $oSmarty = $this->_getSmarty();
        $sConfirmCode = md5($oUser->oxuser__oxusername->value . $oUser->oxuser__oxpasssalt->value);
        $this->setViewData('subscribeLink', $this->_getNewsSubsLink($oUser->oxuser__oxid->value, $sConfirmCode));
        $this->setUser($oUser);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($oSmarty->fetch($this->_sNewsletterOptInTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sNewsletterOptInTemplatePlain));
        $this->setSubject(($sSubject !== null) ? $sSubject : oxRegistry::getLang()->translateString('NEWSLETTER') . ' ' . $oShop->oxshops__oxname->getRawValue());

        $sFullName = $oUser->oxuser__oxfname->getRawValue() . ' ' . $oUser->oxuser__oxlname->getRawValue();

        $this->setRecipient($oUser->oxuser__oxusername->value, $sFullName);
        $this->setFromAddress($oShop->oxshops__oxinfoemail->value, $oShop->oxshops__oxname->getRawValue());
        $this->setReplyTo($oShop->oxshops__oxinfoemail->value, $oShop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Returns newsletter subscription link
     *
     * @param string $sId          user id
     * @param string $sConfirmCode confirmation code
     *
     * @return string $sUrl
     */
    protected function _getNewsSubsLink($sId, $sConfirmCode = null)
    {
        $myConfig = $this->getConfig();
        $iActShopLang = $myConfig->getActiveShop()->getLanguage();

        $sUrl = $myConfig->getShopHomeURL() . 'cl=newsletter&amp;fnc=addme&amp;uid=' . $sId;
        $sUrl .= '&amp;lang=' . $iActShopLang;
        $sUrl .= ($sConfirmCode) ? '&amp;confirm=' . $sConfirmCode : '';

        return $sUrl;
    }

    /**
     * Sets mailer additional settings and sends 'newsletter' mail to user.
     * Returns true on success.
     *
     * @param oxNewsletter $oNewsLetter newsletter object
     * @param oxUser       $oUser       user object
     * @param string       $sSubject    user defined subject [optional]
     *
     * @return bool
     */
    public function sendNewsletterMail($oNewsLetter, $oUser, $sSubject = null)
    {
        // shop info
        $oShop = $this->_getShop();

        $sBody = $oNewsLetter->getHtmlText();

        if (!empty($sBody)) {
            $this->setBody($sBody);
            $this->setAltBody($oNewsLetter->getPlainText());
        } else {
            $this->mailer->isHtml(false);
            $this->setBody($oNewsLetter->getPlainText());
        }

        $this->setSubject(($sSubject !== null) ? $sSubject : $oNewsLetter->oxnewsletter__oxtitle->getRawValue());

        $sFullName = $oUser->oxuser__oxfname->getRawValue() . ' ' . $oUser->oxuser__oxlname->getRawValue();
        $this->setRecipient($oUser->oxuser__oxusername->value, $sFullName);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends 'SuggestMail' mail to user.
     * Returns true on success.
     *
     * @param object $oParams  Mailing parameters object
     * @param object $oProduct Product object
     *
     * @return bool
     */
    public function sendSuggestMail($oParams, $oProduct)
    {
        $myConfig = $this->getConfig();

        //sets language of shop
        $iCurrLang = $myConfig->getActiveShop()->getLanguage();

        // shop info
        $oShop = $this->_getShop($iCurrLang);

        //sets language to article
        if ($oProduct->getLanguage() != $iCurrLang) {
            $oProduct->setLanguage($iCurrLang);
            $oProduct->load($oProduct->getId());
        }

        // mailer stuff
        // send not pretending from suggesting user, as different email domain rise spam filters
        $this->mailer->setFromAddress($oShop->oxshops__oxinfoemail->value);

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData('product', $oProduct);
        $this->setUser($oParams);

        $sArticleUrl = $oProduct->getLink();

        //setting recommended user id
        if ($myConfig->getActiveView()->isActive('Invitations') && $oActiveUser = $oShop->getUser()) {
            $sArticleUrl = oxRegistry::get('oxUtilsUrl')->appendParamSeparator($sArticleUrl);
            $sArticleUrl .= 'su=' . $oActiveUser->getId();
        }

        $this->setViewData('sArticleUrl', $sArticleUrl);

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setBody($oSmarty->fetch($this->_sSuggestTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sSuggestTemplatePlain));
        $this->setSubject($oParams->send_subject);

        $this->setRecipient($oParams->rec_email, $oParams->rec_name);
        $this->setReplyTo($oParams->send_email, $oParams->send_name);

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends 'InviteMail' mail to user.
     * Returns true on success.
     *
     * @param object $oParams Mailing parameters object
     *
     * @return bool
     */
    public function sendInviteMail($oParams)
    {
        $myConfig = $this->getConfig();

        //sets language of shop
        $iCurrLang = $myConfig->getActiveShop()->getLanguage();

        // shop info
        $oShop = $this->_getShop($iCurrLang);

        // mailer stuff
        $this->mailer->setFromAddress($oParams->send_email, $oParams->send_name);

        // create messages
        $oSmarty = oxRegistry::get('oxUtilsView')->getSmarty();
        $this->setUser($oParams);

        $sHomeUrl = $this->getViewConfig()->getHomeLink();

        //setting recommended user id
        if ($myConfig->getActiveView()->isActive('Invitations') && $oActiveUser = $oShop->getUser()) {
            $sHomeUrl = oxRegistry::get('oxUtilsUrl')->appendParamSeparator($sHomeUrl);
            $sHomeUrl .= 'su=' . $oActiveUser->getId();
        }

        if (is_array($oParams->rec_email) && count($oParams->rec_email) > 0) {
            foreach ($oParams->rec_email as $sEmail) {
                if (!empty($sEmail)) {
                    $sRegisterUrl = oxRegistry::get('oxUtilsUrl')->appendParamSeparator($sHomeUrl);
                    //setting recipient user email
                    $sRegisterUrl .= 're=' . md5($sEmail);
                    $this->setViewData('sHomeUrl', $sRegisterUrl);

                    // Process view data array through oxoutput processor
                    $this->_processViewArray();

                    $this->setBody($oSmarty->fetch($this->_sInviteTemplate));

                    $this->setAltBody($oSmarty->fetch($this->_sInviteTemplatePlain));
                    $this->setSubject($oParams->send_subject);

                    $this->setRecipient($sEmail);
                    $this->setReplyTo($oParams->send_email, $oParams->send_name);
                    $this->send();
                    $this->clearAllRecipients();
                }
            }

            return true;
        }

        return false;
    }

    /**
     * Sets mailer additional settings and sends 'SendedNowMail' mail to user.
     * Returns true on success.
     *
     * @param oxOrder $oOrder   order object
     * @param string  $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendSendedNowMail($oOrder, $sSubject = null)
    {
        $myConfig = $this->getConfig();

        $iOrderLang = (int) (isset($oOrder->oxorder__oxlang->value) ? $oOrder->oxorder__oxlang->value : 0);

        // shop info
        $oShop = $this->_getShop($iOrderLang);

        //create messages
        $oLang = oxRegistry::getLang();
        $oSmarty = $this->_getSmarty();
        $this->setViewData('order', $oOrder);
        $this->setViewData('shopTemplateDir', $myConfig->getTemplateDir(false));

        if ($myConfig->getConfigParam('bl_perfLoadReviews')) {
            $this->setViewData('blShowReviewLink', true);
            $oUser = oxNew('oxuser');
            $this->setViewData('reviewuserhash', $oUser->getReviewUserHash($oOrder->oxorder__oxuserid->value));
        }

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        // #1469 - we need to patch security here as we do not use standard template dir, so smarty stops working
        $aStore['INCLUDE_ANY'] = $oSmarty->security_settings['INCLUDE_ANY'];
        //V send email in order language
        $iOldTplLang = $oLang->getTplLanguage();
        $iOldBaseLang = $oLang->getTplLanguage();
        $oLang->setTplLanguage($iOrderLang);
        $oLang->setBaseLanguage($iOrderLang);

        $oSmarty->security_settings['INCLUDE_ANY'] = true;
        // force non admin to get correct paths (tpl, img)
        $myConfig->setAdminMode(false);
        $this->setBody($oSmarty->fetch($this->_sSenedNowTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sSenedNowTemplatePlain));
        $myConfig->setAdminMode(true);
        $oLang->setTplLanguage($iOldTplLang);
        $oLang->setBaseLanguage($iOldBaseLang);
        // set it back
        $oSmarty->security_settings['INCLUDE_ANY'] = $aStore['INCLUDE_ANY'];

        //Sets subject to email
        $this->setSubject(($sSubject !== null) ? $sSubject : $oShop->oxshops__oxsendednowsubject->getRawValue());

        $sFullName = $oOrder->oxorder__oxbillfname->getRawValue() . ' ' . $oOrder->oxorder__oxbilllname->getRawValue();

        $this->setRecipient($oOrder->oxorder__oxbillemail->value, $sFullName);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends 'SendDownloadLinks' mail to user.
     * Returns true on success.
     *
     * @param oxOrder $oOrder   order object
     * @param string  $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendDownloadLinksMail($oOrder, $sSubject = null)
    {
        $myConfig = $this->getConfig();

        $iOrderLang = (int) (isset($oOrder->oxorder__oxlang->value) ? $oOrder->oxorder__oxlang->value : 0);

        // shop info
        $oShop = $this->_getShop($iOrderLang);

        //create messages
        $oLang = oxRegistry::getLang();
        $oSmarty = $this->_getSmarty();
        $this->setViewData('order', $oOrder);
        $this->setViewData('shopTemplateDir', $myConfig->getTemplateDir(false));

        $oUser = oxNew('oxuser');
        $this->setViewData('reviewuserhash', $oUser->getReviewUserHash($oOrder->oxorder__oxuserid->value));

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        // #1469 - we need to patch security here as we do not use standard template dir, so smarty stops working
        $aStore['INCLUDE_ANY'] = $oSmarty->security_settings['INCLUDE_ANY'];
        //V send email in order language
        $iOldTplLang = $oLang->getTplLanguage();
        $iOldBaseLang = $oLang->getTplLanguage();
        $oLang->setTplLanguage($iOrderLang);
        $oLang->setBaseLanguage($iOrderLang);

        $oSmarty->security_settings['INCLUDE_ANY'] = true;
        // force non admin to get correct paths (tpl, img)
        $myConfig->setAdminMode(false);
        $this->setBody($oSmarty->fetch($this->_sSendDownloadsTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sSendDownloadsTemplatePlain));
        $myConfig->setAdminMode(true);
        $oLang->setTplLanguage($iOldTplLang);
        $oLang->setBaseLanguage($iOldBaseLang);
        // set it back
        $oSmarty->security_settings['INCLUDE_ANY'] = $aStore['INCLUDE_ANY'];

        //Sets subject to email
        $this->setSubject(($sSubject !== null) ? $sSubject : $oLang->translateString('DOWNLOAD_LINKS', null, false));

        $sFullName = $oOrder->oxorder__oxbillfname->getRawValue() . ' ' . $oOrder->oxorder__oxbilllname->getRawValue();

        $this->setRecipient($oOrder->oxorder__oxbillemail->value, $sFullName);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        return $this->send();
    }

    /**
     * Sets mailer additional settings and sends backup data to user.
     * Returns true on success.
     *
     * @param array  $aAttFiles     Array of file names to attach
     * @param string $sAttPath      Path to files to attach
     * @param string $sEmailAddress Email address
     * @param string $sSubject      Email subject
     * @param string $sMessage      Email body message
     * @param array  $aStatus       Pointer to mailing status array
     * @param array  $aError        Pointer to error status array
     *
     * @return bool
     */
    public function sendBackupMail($aAttFiles, $sAttPath, $sEmailAddress, $sSubject, $sMessage, &$aStatus, &$aError)
    {
        // shop info
        $oShop = $this->_getShop();

        $this->setBody($sMessage);
        $this->setSubject($sSubject);

        $this->setRecipient($oShop->oxshops__oxinfoemail->value, '');
        $sEmailAddress = $sEmailAddress ? $sEmailAddress : $oShop->oxshops__oxowneremail->value;

        $this->mailer->setFromAddress($sEmailAddress, '');
        $this->setReplyTo($sEmailAddress, '');

        //attaching files
        $blAttashSucc = true;
        $sAttPath = oxRegistry::get('oxUtilsFile')->normalizeDir($sAttPath);
        foreach ($aAttFiles as $iNum => $sAttFile) {
            $sFullPath = $sAttPath . $sAttFile;
            if (@is_readable($sFullPath) && @is_file($sFullPath)) {
                $blAttashSucc = $this->addAttachment($sFullPath, $sAttFile);
            } else {
                $blAttashSucc = false;
                $aError[] = array(5, $sAttFile); //'Error: backup file $sAttFile not found';
            }
        }

        if (!$blAttashSucc) {
            $aError[] = array(4, ''); //'Error: backup files was not sent to email ...';
            $this->clearAttachments();

            return false;
        }

        $aStatus[] = 3; //'Mailing backup files ...';
        $blSend = $this->send();
        $this->clearAttachments();

        return $blSend;
    }

    /**
     * Basic wrapper for email message sending with default parameters from the oxBaseShop.
     * Returns true on success.
     *
     * @param mixed  $sTo      Recipient or an array of the recipients
     * @param string $sSubject Mail subject
     * @param string $sBody    Mail body
     *
     * @return bool
     */
    public function sendEmail($sTo, $sSubject, $sBody)
    {
        if (is_array($sTo)) {
            foreach ($sTo as $sAddress) {
                $this->setRecipient($sAddress, '');
                $this->setReplyTo($sAddress, '');
            }
        } else {
            $this->setRecipient($sTo, '');
            $this->setReplyTo($sTo, '');
        }

        //may be changed later
        $this->mailer->isHtml(false);

        $this->setSubject($sSubject);
        $this->setBody($sBody);

        return $this->send();
    }

    /**
     * Sends reminder email to shop owner.
     *
     * @param array  $aBasketContents array of objects to pass to template
     * @param string $sSubject        user defined subject [optional]
     *
     * @return bool
     */
    public function sendStockReminder($aBasketContents, $sSubject = null)
    {
        $blSend = false;

        $oArticleList = oxNew('oxArticleList');
        $oArticleList->loadStockRemindProducts($aBasketContents);

        // nothing to remind?
        if ($oArticleList->count()) {
            $oShop = $this->_getShop();

            $oLang = oxRegistry::getLang();

            $oSmarty = $this->_getSmarty();
            $this->setViewData('articles', $oArticleList);

            // Process view data array through oxOutput processor
            $this->_processViewArray();

            $this->setRecipient($oShop->oxshops__oxowneremail->value, $oShop->oxshops__oxname->getRawValue());
            $this->mailer->setFromAddress($oShop->oxshops__oxowneremail->value, $oShop->oxshops__oxname->getRawValue());
            $this->setBody($oSmarty->fetch($this->getConfig()->getTemplatePath($this->_sReminderMailTemplate, false)));
            $this->setAltBody('');
            $this->setSubject(($sSubject !== null) ? $sSubject : $oLang->translateString('STOCK_LOW'));

            $blSend = $this->send();
        }

        return $blSend;
    }

    /**
     * Sets mailer additional settings and sends 'WishlistMail' mail to user.
     * Returns true on success.
     *
     * @param oxUser|object $oParams Mailing parameters object
     *
     * @return bool
     */
    public function sendWishlistMail($oParams)
    {
        $this->_clearMailer();

        // mailer stuff
        $this->mailer->setFromAddress($oParams->send_email, $oParams->send_name);

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setUser($oParams);

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setBody($oSmarty->fetch($this->_sWishListTemplate));
        $this->setAltBody($oSmarty->fetch($this->_sWishListTemplatePlain));
        $this->setSubject($oParams->send_subject);

        $this->setRecipient($oParams->rec_email, $oParams->rec_name);
        $this->setReplyTo($oParams->send_email, $oParams->send_name);

        return $this->send();
    }

    /**
     * Sends a notification to the shop owner that price alarm was subscribed.
     * Returns true on success.
     *
     * @param array        $aParams  Parameters array
     * @param oxPriceAlarm $oAlarm   oxPriceAlarm object
     * @param string       $sSubject user defined subject [optional]
     *
     * @return bool
     */
    public function sendPriceAlarmNotification($aParams, $oAlarm, $sSubject = null)
    {
        $this->_clearMailer();
        $oShop = $this->_getShop();

        $iAlarmLang = $oAlarm->oxpricealarm__oxlang->value;

        $oArticle = oxNew('oxArticle');
        //$oArticle->setSkipAbPrice( true );
        $oArticle->loadInLang($iAlarmLang, $aParams['aid']);
        $oLang = oxRegistry::getLang();

        // create messages
        $oSmarty = $this->_getSmarty();
        $this->setViewData('product', $oArticle);
        $this->setViewData('email', $aParams['email']);
        $this->setViewData('bidprice', $oLang->formatCurrency($oAlarm->oxpricealarm__oxprice->value));

        // Process view data array through oxOutput processor
        $this->_processViewArray();

        $this->setRecipient($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
        $this->setSubject(($sSubject !== null) ? $sSubject : $oLang->translateString('PRICE_ALERT_FOR_PRODUCT', $iAlarmLang) . ' ' . $oArticle->oxarticles__oxtitle->getRawValue());
        $this->setBody($oSmarty->fetch($this->_sOwnerPricealarmTemplate));
        $this->mailer->setFromAddress($aParams['email'], '');
        $this->setReplyTo($aParams['email'], '');

        return $this->send();
    }

    /**
     * Sends price alarm to customer.
     * Returns true on success.
     *
     * @param string       $sRecipient      email
     * @param oxPriceAlarm $oAlarm          oxPriceAlarm object
     * @param string       $sBody           optional mail body
     * @param bool         $sReturnMailBody returns mail body instead of sending
     *
     * @return bool
     */
    public function sendPricealarmToCustomer($sRecipient, $oAlarm, $sBody = null, $sReturnMailBody = null)
    {
        $this->_clearMailer();

        $oShop = $this->_getShop();

        if ($oShop->getId() != $oAlarm->oxpricealarm__oxshopid->value) {
            $oShop = oxNew('oxshop');
            $oShop->load($oAlarm->oxpricealarm__oxshopid->value);
            $this->setShop($oShop);
        }

        // create messages
        $oSmarty = $this->_getSmarty();

        $this->setViewData('product', $oAlarm->getArticle());
        $this->setViewData('oPriceAlarm', $oAlarm);
        $this->setViewData('bidprice', $oAlarm->getFProposedPrice());
        $this->setViewData('currency', $oAlarm->getPriceAlarmCurrency());

        // Process view data array through oxoutput processor
        $this->_processViewArray();

        $this->setRecipient($sRecipient, $sRecipient);
        $this->setSubject($oShop->oxshops__oxname->value);

        if ($sBody === null) {
            $sBody = $oSmarty->fetch($this->_sPricealamrCustomerTemplate);
        }

        $this->setBody($sBody);

        $this->mailer->addAddress($sRecipient, $sRecipient);
        $this->setReplyTo($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());

        if ($sReturnMailBody) {
            return $this->getBody();
        } else {
            return $this->send();
        }
    }

    /**
     * Checks for external images and embeds them to email message if possible
     *
     * @param string $sImageDir       Images directory url
     * @param string $sImageDirNoSSL  Images directory url (no SSL)
     * @param string $sDynImageDir    Path to Dyn images
     * @param string $sAbsImageDir    Absolute path to images
     * @param string $sAbsDynImageDir Absolute path to Dyn images
     */
    protected function _includeImages($sImageDir = null, $sImageDirNoSSL = null, $sDynImageDir = null, $sAbsImageDir = null, $sAbsDynImageDir = null)
    {
        $sBody = $this->getBody();
        if (preg_match_all('/<\s*img\s+[^>]*?src[\s]*=[\s]*[\'"]?([^[\'">]]+|.*?)?[\'">]/i', $sBody, $matches, PREG_SET_ORDER)) {
            $oFileUtils = oxRegistry::get('oxUtilsFile');
            $blReSetBody = false;

            // preparing imput
            $sDynImageDir = $oFileUtils->normalizeDir($sDynImageDir);
            $sImageDir = $oFileUtils->normalizeDir($sImageDir);
            $sImageDirNoSSL = $oFileUtils->normalizeDir($sImageDirNoSSL);

            if (is_array($matches) && count($matches)) {
                $aImageCache = array();
                $myUtils = oxRegistry::getUtils();
                $myUtilsObject = oxUtilsObject::getInstance();
                $oImgGenerator = oxNew('oxDynImgGenerator');

                foreach ($matches as $aImage) {
                    $image = $aImage[1];
                    $sFileName = '';
                    if (strpos($image, $sDynImageDir) === 0) {
                        $sFileName = $oFileUtils->normalizeDir($sAbsDynImageDir) . str_replace($sDynImageDir, '', $image);
                    } elseif (strpos($image, $sImageDir) === 0) {
                        $sFileName = $oFileUtils->normalizeDir($sAbsImageDir) . str_replace($sImageDir, '', $image);
                    } elseif (strpos($image, $sImageDirNoSSL) === 0) {
                        $sFileName = $oFileUtils->normalizeDir($sAbsImageDir) . str_replace($sImageDirNoSSL, '', $image);
                    }

                    if ($sFileName && !@is_readable($sFileName)) {
                        $sFileName = $oImgGenerator->getImagePath($sFileName);
                    }

                    if ($sFileName) {
                        if (isset($aImageCache[$sFileName]) && $aImageCache[$sFileName]) {
                            $sCId = $aImageCache[$sFileName];
                        } else {
                            $sCId = $myUtilsObject->generateUID();
                            $sMIME = $myUtils->oxMimeContentType($sFileName);
                            if ($sMIME == 'image/jpeg' || $sMIME == 'image/gif' || $sMIME == 'image/png') {
                                if ($this->addEmbeddedImage($sFileName, $sCId, 'image', 'base64', $sMIME)) {
                                    $aImageCache[$sFileName] = $sCId;
                                } else {
                                    $sCId = '';
                                }
                            }
                        }
                        if ($sCId && $sCId == $aImageCache[$sFileName]) {
                            if ($sReplTag = str_replace($image, 'cid:' . $sCId, $aImage[0])) {
                                $sBody = str_replace($aImage[0], $sReplTag, $sBody);
                                $blReSetBody = true;
                            }
                        }
                    }
                }
            }

            if ($blReSetBody) {
                $this->setBody($sBody);
            }
        }
    }

    /**
     * Sets mail subject
     *
     * @param string $sSubject mail subject
     */
    public function setSubject($sSubject = null)
    {
        // A. HTML entities in subjects must be replaced
        $sSubject = str_replace(array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), array('&', '"', "'", '<', '>'), $sSubject);

        $this->mailer->set('Subject', $sSubject);
    }

    /**
     * Gets mail subject
     *
     * @return string
     */
    public function getSubject()
    {
        return $this->mailer->Subject;
    }

    /**
     * Set mail body. If second parameter (default value is true) is set to true,
     * performs search for 'sid', removes it and adds shop id to string.
     *
     * @param string $sBody      mail body
     * @param bool   $blClearSid clear sid in mail body
     */
    public function setBody($sBody = null, $blClearSid = true)
    {
        if ($blClearSid) {
            $sBody = $this->_clearSidFromBody($sBody);
        }

        $this->mailer->set('Body', $sBody);
    }

    /**
     * Gets mail body
     *
     * @return string
     */
    public function getBody()
    {
        return $this->mailer->Body;
    }

    /**
     * Sets text-only body of the message. If second parameter is set to true,
     * performs search for 'sid', removes it and adds shop id to string.
     *
     * @param string $sAltBody   mail subject
     * @param bool   $blClearSid clear sid in mail body (default value is true)
     */
    public function setAltBody($sAltBody = null, $blClearSid = true)
    {
        if ($blClearSid) {
            $sAltBody = $this->_clearSidFromBody($sAltBody);
        }

        // A. alt body is used for plain text emails so we should eliminate HTML entities
        $sAltBody = str_replace(array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), array('&', '"', "'", '<', '>'), $sAltBody);

        $this->mailer->set('AltBody', $sAltBody);
    }

    /**
     * Gets mail text-only body
     *
     * @return string
     */
    public function getAltBody()
    {
        return $this->mailer->AltBody;
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
            if ($this->getConfig()->isUtf() && function_exists('idn_to_ascii') ) {
                $address = idn_to_ascii($address);
            }

            $this->mailer->AddAddress($address, $name);

            // copying values as original class does not allow to access recipients array
            $this->_aRecipients[] = array($address, $name);
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
        $this->_aRecipients = array();
        //$this->mailer->clearAllRecipients();
    }

    /**
     * Sets user address and name to 'reply to' array.
     * On error (wrong email) default shop email is added as a reply address.
     * Returns array of recipients
     * f.e. array( array('mail1@mail1.com', 'user1Name'), array('mail2@mail2.com', 'user2Name') )
     *
     * @param string $sEmail email address
     * @param string $sName  user name
     */
    public function setReplyTo($sEmail = null, $sName = null)
    {
        if (!oxRegistry::getUtils()->isValidEmail($sEmail)) {
            $sEmail = $this->_getShop()->oxshops__oxorderemail->value;
        }

        $this->_aReplies[] = array($sEmail, $sName);

        try {
            $this->mailer->addReplyTo($sEmail, $sName);
        } catch (Exception $oEx) {
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
        $this->_aReplies = array();
        //$this->mailer->clearReplyTos();
    }

    /**
     * Gets mail 'from address' field.
     *
     * @return string
     */
    public function getFrom()
    {
        return $this->mailer->From;
    }

    /**
     * Gets mail 'from name' field.
     *
     * @return string
     */
    public function getFromName()
    {
        return $this->mailer->FromName;
    }

    /**
     * Sets mail mailer. Set to send mail via smtp, mail() or sendmail.
     *
     * @param string $sMailer email mailer
     */
    public function setMailer($sMailer = null)
    {
        $this->mailer->set('Mailer', $sMailer);
    }

    /**
     * Gets mail mailer.
     *
     * @return string
     */
    public function getMailer()
    {
        return $this->mailer->Mailer;
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

    /**
     * Sets use inline images. If true, images will be embedded into mail.
     *
     * @param bool $blUseImages embed or not images into mail
     */
    public function setUseInlineImages($blUseImages = null)
    {
        $this->_blInlineImgEmail = $blUseImages;
    }

    /**
     * Adds an attachment to mail from a path on the filesystem
     *
     * @param string $sAttPath  path to the attachment
     * @param string $sAttFile  attachment name
     * @param string $sEncoding attachment encoding
     * @param string $sType     attachment type
     *
     * @return bool
     */
    public function addAttachment($sAttPath, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream')
    {
        $this->_aAttachments[] = array($sAttPath, $sAttFile, $sEncoding, $sType);
        $blResult = false;

        try {
            $blResult = $this->mailer->addAttachment($sAttPath, $sAttFile, $sEncoding, $sType);
        } catch (Exception $oEx) {
        }

        return $blResult;
    }

    /**
     * Adds an embedded attachment (check phpmail documentation for more details)
     *
     * @param string $sFullPath Path to the attachment.
     * @param string $sCid      Content ID of the attachment. Use this to identify the Id for accessing the image in an HTML form.
     * @param string $sAttFile  Overrides the attachment name.
     * @param string $sEncoding File encoding (see $Encoding).
     * @param string $sType     File extension (MIME) type.
     *
     * @return bool
     */
    public function addEmbeddedImage($sFullPath, $sCid, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream')
    {
        $this->_aAttachments[] = array($sFullPath, basename($sFullPath), $sAttFile, $sEncoding, $sType, false, 'inline', $sCid);

        return $this->mailer->addEmbeddedImage($sFullPath, $sCid, $sAttFile, $sEncoding, $sType);
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
        $this->_aAttachments = array();
        //$this->mailer->clearAttachments();
    }

    /**
     * Inherited phpMailer function adding a header to email message.
     * We override it to skip X-Mailer header.
     *
     * @param string $sName  header name
     * @param string $sValue header value
     *
     * @return string|null
     */
    public function headerLine($sName, $sValue)
    {
        if (stripos($sName, 'X-') !== false) {
            return null;
        }

        return $this->mailer->headerLine($sName, $sValue);
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
        $aRecipients = $this->getRecipient();

        $sOwnerMessage = 'Error sending eMail(' . $this->getSubject() . ') to: \n\n';

        foreach ($aRecipients as $aEMail) {
            $sOwnerMessage .= $aEMail[0];
            $sOwnerMessage .= (!empty($aEMail[1])) ? ' (' . $aEMail[1] . ')' : '';
            $sOwnerMessage .= " \n ";
        }
        $sOwnerMessage .= "\n\nError : " . $this->getErrorInfo();

        // shop info
        $oShop = $this->_getShop();

        $blRet = @mail($oShop->oxshops__oxorderemail->value, 'eMail problem in shop!', $sOwnerMessage);

        return $blRet;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxEmail::sendOrderEMailToUser() to do
     * additional operation with order object before sending email
     *
     * @param oxOrder $oOrder Ordering object
     *
     * @return oxOrder
     */
    protected function _addUserInfoOrderEMail($oOrder)
    {
        return $oOrder;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxEmail::SendRegisterEMail() to do
     * additional operation with user object before sending email
     *
     * @param oxUser $oUser User object
     *
     * @return oxUser
     */
    protected function _addUserRegisterEmail($oUser)
    {
        return $oUser;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxemail::SendForgotPWDEMail() to do
     * additional operation with shop object before sending email
     *
     * @param oxShop $oShop Shop object
     *
     * @return oxShop
     */
    protected function _addForgotPwdEmail($oShop)
    {
        return $oShop;
    }

    /**
     * Does nothing, returns same object as passed to method.
     * This method is called from oxEmail::SendNewsletterDBOptInMail() to do
     * additional operation with user object before sending email
     *
     * @param oxUser $oUser User object
     *
     * @return oxUser
     */
    protected function _addNewsletterDbOptInMail($oUser)
    {
        return $oUser;
    }

    /**
     * Clears mailer settings (AllRecipients, ReplyTos, Attachments, Errors)
     */
    protected function _clearMailer()
    {
        $this->clearAllRecipients();
        $this->clearReplyTos();
        $this->clearAttachments();

        //$this->mailer->ErrorInfo = '';
    }

    /**
     * Set mail From, FromName, SMTP values
     *
     * @param oxShop $oShop Shop object
     */
    protected function _setMailParams($oShop = null)
    {
        $this->_clearMailer();

        if (!$oShop) {
            $oShop = $this->_getShop();
        }

        $this->mailer->setFromAddress($oShop->oxshops__oxorderemail->value, $oShop->oxshops__oxname->getRawValue());
    }

    /**
     * Get active shop and set global params for it
     * If is set language parameter, load shop in given language
     *
     * @param int $iLangId language id
     * @param int $iShopId shop id
     *
     * @return oxShop
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
     * Process email body and alt body thought oxOutput.
     * Calls oxOutput::processEmail() on class instance.
     */
    protected function _makeOutputProcessing()
    {
        $oOutput = oxNew('oxOutput');
        $this->setBody($oOutput->process($this->getBody(), 'oxemail'));
        $this->setAltBody($oOutput->process($this->getAltBody(), 'oxemail'));
        $oOutput->processEmail($this);
    }

    /**
     * Sends email via phpmailer.
     *
     * @return bool
     */
    protected function _sendMail()
    {
        $blResult = false;
        try {
            $blResult = $this->mailer->send();
        } catch (Exception $oException) {
            /* @var oxException $oEx */
            $oEx = oxNew('oxException');
            $oEx->setMessage($oException->getMessage());
            $oEx->debugOut();
            if ($this->getConfig()->getConfigParam('iDebug') != 0) {
                throw $oEx;
            }
        }

        return $blResult;
    }


    /**
     * Process view data array through oxOutput processor
     */
    protected function _processViewArray()
    {
        $oSmarty = $this->_getSmarty();
        $oOutputProcessor = oxNew('oxOutput');

        // processing all view data
        foreach ($this->_aViewData as $sKey => $sValue) {
            $oSmarty->assign($sKey, $sValue);
        }

        // processing assigned smarty variables
        $aNewSmartyArray = $oOutputProcessor->processViewArray($oSmarty->get_template_vars(), 'oxemail');

        foreach ($aNewSmartyArray as $key => $val) {
            $oSmarty->assign($key, $val);
        }
    }

    /**
     * Get mail charset
     *
     * @return string
     */
    public function getCharset()
    {
        return $this->mailer->CharSet;
    }

    /**
     * Get shop object
     *
     * @return oxShop
     */
    public function getShop()
    {
        return $this->_getShop();
    }

    /**
     * Set shop object
     *
     * @param oxShop $oShop shop object
     */
    public function setShop($oShop)
    {
        $this->_oShop = $oShop;
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
        $oConfig = oxRegistry::getConfig();

        return $oConfig->getActShopCurrencyObject();
    }

    /**
     * Set view data to email view.
     *
     * @param string $sKey   key value
     * @param mixed  $sValue item value
     */
    public function setViewData($sKey, $sValue)
    {
        $this->_aViewData[$sKey] = $sValue;
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
     * @param string $sKey view data array key
     *
     * @return mixed
     */
    public function getViewDataItem($sKey)
    {
        if (isset($this->_aViewData[$sKey])) {
            return $this->_aViewData;
        }
    }

    /**
     * Set user to view data
     *
     * @param oxUser $oUser user object
     */
    public function setUser($oUser)
    {
        $this->_aViewData['oUser'] = $oUser;
    }

    /**
     * Get user
     *
     * @return oxUser
     */
    public function getUser()
    {
        return $this->_aViewData['oUser'];
    }

    /**
     * Get order files
     *
     * @param string $sOrderId order id
     *
     * @return oxOrderFileList
     */
    public function getOrderFileList($sOrderId)
    {
        $oOrderList = oxNew('oxOrderFileList');
        $oOrderList->loadOrderFiles($sOrderId);

        if (count($oOrderList) > 0) {
            return $oOrderList;
        }

        return false;
    }

    /**
     * Performs search for 'sid', removes it and adds shop id to string.
     *
     * @param string $sAltBody Body.
     *
     * @return string
     */
    private function _clearSidFromBody($sAltBody)
    {
        return oxStr::getStr()->preg_replace('/(\?|&(amp;)?)(force_)?(admin_)?sid=[A-Z0-9\.]+/i', '\1shp=' . $this->getConfig()->getShopId(), $sAltBody);
    }
}
