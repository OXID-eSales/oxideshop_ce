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

/**
 * pricealarm sending manager.
 * Performs sending of pricealarm to selected iAllCnt groups.
 */
class PriceAlarm_Send extends oxAdminList
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
        $database = oxDb::getDb(oxDb::FETCH_MODE_ASSOC);

        ini_set("session.gc_maxlifetime", 36000);

        $start = oxRegistry::getConfig()->getRequestParameter("iStart");
        $count = oxRegistry::getConfig()->getRequestParameter("iAllCnt");
        // #1140 R
        $alarmsQuery =
            "SELECT oxpricealarm.oxid, oxpricealarm.oxemail, oxpricealarm.oxartid, oxpricealarm.oxprice
            FROM oxpricealarm, oxarticles
            WHERE oxarticles.oxid = oxpricealarm.oxartid AND oxpricealarm.oxsended = '0000-00-00 00:00:00'";
        if (isset($start)) {
            $result = $database->selectLimit($alarmsQuery, $config->getConfigParam('iCntofMails'), $start);
        } else {
            $result = $database->execute($alarmsQuery);
        }

        $temporaryCount = 0;

        if ($result != false && $result->recordCount() > 0) {
            while (!$result->EOF) {
                $article = oxNew("oxArticle");
                $article->load($result->fields['oxid']);
                if ($article->getPrice()->getBruttoPrice() <= $result->fields['oxprice']) {
                    $this->sendeMail(
                        $result->fields['oxemail'],
                        $result->fields['oxartid'],
                        $result->fields['oxid'],
                        $result->fields['oxprice']
                    );
                    $temporaryCount++;
                }
                $result->moveNext();
            }
        }
        if (!isset($start)) {
            // first call
            $start = 0;
            $count = $temporaryCount;
        }

        // Advance mail pointer and set parameter
        $start += $config->getConfigParam('iCntofMails');

        $this->_aViewData["iStart"] = $start;
        $this->_aViewData["iAllCnt"] = $count;
        $this->_aViewData["actlang"] = oxRegistry::getLang()->getBaseLanguage();

        // end ?
        if ($start < $count) {
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
     * Creates and sends email with price alarm information.
     *
     * @param string $emailAddress Email address
     * @param string $productID    Product id
     * @param string $priceAlarmId Price alarm id
     * @param string $bidPrice     Bid price
     */
    public function sendeMail($emailAddress, $productID, $priceAlarmId, $bidPrice)
    {
        $alarm = oxNew("oxPriceAlarm");
        $alarm->load($priceAlarmId);

        $language = oxRegistry::getLang();
        $languageId = (int) $alarm->oxpricealarm__oxlang->value;

        $oldLanguageId = $language->getTplLanguage();
        $language->setTplLanguage($languageId);

        $email = oxNew('oxEmail');
        $success = (int) $email->sendPricealarmToCustomer($emailAddress, $alarm);

        $language->setTplLanguage($oldLanguageId);

        if ($success) {
            $alarm->oxpricealarm__oxsended = new oxField(date("Y-m-d H:i:s"));
            $alarm->save();
        }
    }
}
