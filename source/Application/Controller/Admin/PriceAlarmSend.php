<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;
use oxField;

/**
 * pricealarm sending manager.
 * Performs sending of pricealarm to selected iAllCnt groups.
 */
class PriceAlarmSend extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Default tab number
     *
     * @var int
     */
    protected $_iDefEdit = 1;

    /**
     * Executes parent method parent::render(), creates oxpricealarm object,
     * sends pricealarm to iAllCnts of chosen groups and returns name of template
     * file "pricealarm_send.tpl"/"pricealarm_done.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $config = $this->getConfig();

        ini_set("session.gc_maxlifetime", 36000);

        $start = (int) $config->getRequestParameter("iStart");
        $limit = $config->getConfigParam('iCntofMails');
        $activeAlertsAmount = $config->getRequestParameter("iAllCnt");
        if (!isset($activeAlertsAmount)) {
            $activeAlertsAmount = $this->countActivePriceAlerts();
        }

        $this->sendPriceChangeNotifications($start, $limit);

        // Advance mail pointer and set parameter
        $start += $limit;

        $this->_aViewData["iStart"] = $start;
        $this->_aViewData["iAllCnt"] = $activeAlertsAmount;
        $this->_aViewData["actlang"] = \OxidEsales\Eshop\Core\Registry::getLang()->getBaseLanguage();

        if ($start < $activeAlertsAmount) {
            $template = "pricealarm_send.tpl";
        } else {
            $template = "pricealarm_done.tpl";
        }

        return $template;
    }

    /**
     * Overrides parent method to pass referred id.
     *
     * @param string $sId Class name
     */
    protected function _setupNavigation($sId)
    {
        parent::_setupNavigation('pricealarm_list');
    }

    /**
     * Counts active price alerts and returns this number.
     *
     * @return int
     */
    protected function countActivePriceAlerts()
    {
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $config = $this->getConfig();
        $shopId = $config->getShopId();

        $activeAlarmsQuery =
            "SELECT oxprice, oxartid FROM oxpricealarm
                    WHERE oxsended = '000-00-00 00:00:00' AND oxshopid = :oxshopid";
        $result = $database->select($activeAlarmsQuery, [
            ':oxshopid' => $shopId
        ]);
        $count = 0;
        while ($result != false && !$result->EOF) {
            $alarmPrice = $result->fields['oxprice'];
            $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $article->load($result->fields['oxartid']);
            if ($article->getPrice()->getBruttoPrice() <= $alarmPrice) {
                $count++;
            }
            $result->fetchRow();
        }

        return $count;
    }

    /**
     * Sends price alert notifications about changed article prices.
     *
     * @param int $start How much price alerts was already sent.
     * @param int $limit How much price alerts to send.
     */
    protected function sendPriceChangeNotifications($start, $limit)
    {
        $config = $this->getConfig();
        $database = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        $shopId = $config->getShopId();

        $alarmsQuery =
            "SELECT oxid, oxemail, oxartid, oxprice FROM oxpricealarm
            WHERE oxsended = '000-00-00 00:00:00' AND oxshopid = :oxshopid";
        $result = $database->selectLimit($alarmsQuery, $limit, $start, [
            ':oxshopid' => $shopId
        ]);
        while ($result != false && !$result->EOF) {
            $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
            $article->load($result->fields['oxartid']);
            if ($article->getPrice()->getBruttoPrice() <= $result->fields['oxprice']) {
                $this->sendeMail(
                    $result->fields['oxemail'],
                    $result->fields['oxartid'],
                    $result->fields['oxid'],
                    $result->fields['oxprice']
                );
            }
            $result->fetchRow();
        }
    }

    /**
     * Creates and sends email with price alarm information.
     *
     * @param string $emailAddress Email address
     * @param string $productID    Product id
     * @param string $priceAlarmId Price alarm id
     * @param string $bidPrice     Bid price
     */
    public function sendeMail($emailAddress, $productID, $priceAlarmId, $bidPrice)
    {
        $alarm = oxNew(\OxidEsales\Eshop\Application\Model\PriceAlarm::class);
        $alarm->load($priceAlarmId);

        $language = \OxidEsales\Eshop\Core\Registry::getLang();
        $languageId = (int) $alarm->oxpricealarm__oxlang->value;

        $oldLanguageId = $language->getTplLanguage();
        $language->setTplLanguage($languageId);

        $email = oxNew(\OxidEsales\Eshop\Core\Email::class);
        $success = (int) $email->sendPricealarmToCustomer($emailAddress, $alarm);

        $language->setTplLanguage($oldLanguageId);

        if ($success) {
            $alarm->oxpricealarm__oxsended = new \OxidEsales\Eshop\Core\Field(date("Y-m-d H:i:s"));
            $alarm->save();
        }
    }
}
