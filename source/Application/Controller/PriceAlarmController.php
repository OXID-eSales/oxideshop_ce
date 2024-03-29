<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller;

use OxidEsales\Eshop\Core\Field;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Core\Di\ContainerFacade;
use OxidEsales\EshopCommunity\Internal\Utility\Email\EmailValidatorServiceBridgeInterface;

/**
 * PriceAlarm window.
 * Arranges "pricealarm" window, by sending eMail and storing into Database (etc.)
 * submission. Result - "pricealarm"  template. After user correctly
 * fulfils all required fields all information is sent to shop owner by
 * email.
 * OXID eShop -> pricealarm.
 */
class PriceAlarmController extends \OxidEsales\Eshop\Application\Controller\FrontendController
{
    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'pricealarm';

    /**
     * Current article.
     *
     * @var object
     */
    protected $_oArticle = null;

    /**
     * Bid price.
     *
     * @var string
     */
    protected $_sBidPrice = null;

    /**
     * Price alarm status.
     *
     * @var integer
     */
    protected $_iPriceAlarmStatus = null;

    /**
     * Validates email
     * address. If email is wrong - returns false and exits. If email
     * address is OK - creates prcealarm object and saves it
     * (oxpricealarm::save()). Sends pricealarm notification mail
     * to shop owner.
     *
     * @return  bool    false on error
     */
    public function addme()
    {
        $myConfig = \OxidEsales\Eshop\Core\Registry::getConfig();
        $myUtils = \OxidEsales\Eshop\Core\Registry::getUtils();
        $emailValidator = ContainerFacade::get(EmailValidatorServiceBridgeInterface::class);

        $aParams = Registry::getRequest()->getRequestEscapedParameter('pa');
        if (!isset($aParams['email']) || !$emailValidator->isEmailValid($aParams['email'])) {
            $this->_iPriceAlarmStatus = 0;

            return;
        }

        $oCur = $myConfig->getActShopCurrencyObject();
        // convert currency to default
        $dPrice = $myUtils->currency2Float($aParams['price']);

        $oAlarm = oxNew(\OxidEsales\Eshop\Application\Model\PriceAlarm::class);
        $oAlarm->oxpricealarm__oxuserid = new Field(Registry::getSession()->getVariable('usr'));
        $oAlarm->oxpricealarm__oxemail = new Field($aParams['email']);
        $oAlarm->oxpricealarm__oxartid = new Field($aParams['aid']);
        $oAlarm->oxpricealarm__oxprice = new Field($myUtils->fRound($dPrice, $oCur));
        $oAlarm->oxpricealarm__oxshopid = new Field($myConfig->getShopId());
        $oAlarm->oxpricealarm__oxcurrency = new Field($oCur->name);

        $oAlarm->oxpricealarm__oxlang = new Field(Registry::getLang()->getBaseLanguage());

        $oAlarm->save();

        // Send Email
        $oEmail = oxNew(\OxidEsales\Eshop\Core\Email::class);
        $this->_iPriceAlarmStatus = (int) $oEmail->sendPricealarmNotification($aParams, $oAlarm);
    }

    /**
     * Template variable getter. Returns bid price
     *
     * @return string
     */
    public function getBidPrice()
    {
        if ($this->_sBidPrice === null) {
            $this->_sBidPrice = false;

            $aParams = $this->getParams();
            $oCur = \OxidEsales\Eshop\Core\Registry::getConfig()->getActShopCurrencyObject();
            $iPrice = \OxidEsales\Eshop\Core\Registry::getUtils()->currency2Float($aParams['price']);
            $this->_sBidPrice = \OxidEsales\Eshop\Core\Registry::getLang()->formatCurrency($iPrice, $oCur);
        }

        return $this->_sBidPrice;
    }

    /**
     * Template variable getter. Returns active article
     *
     * @return object
     */
    public function getProduct()
    {
        if ($this->_oArticle === null) {
            $this->_oArticle = false;
            $aParams = $this->getParams();
            $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $oArticle->load($aParams['aid']);
            $this->_oArticle = $oArticle;
        }

        return $this->_oArticle;
    }

    /**
     * Returns params (article id, bid price)
     *
     * @return array
     */
    private function getParams()
    {
        return Registry::getRequest()->getRequestEscapedParameter('pa');
    }

    /**
     * Return pricealarm status (if it was send)
     *
     * @return integer
     */
    public function getPriceAlarmStatus()
    {
        return $this->_iPriceAlarmStatus;
    }
}
