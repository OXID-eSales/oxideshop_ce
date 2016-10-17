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

namespace OxidEsales\Eshop\Application\Controller\Admin;

use oxDb;

/**
 * Admin article main pricealarm manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer Info -> pricealarm -> Main.
 */
class PriceAlarmMail extends \oxAdminDetails
{

    /**
     * Executes parent method parent::render(), creates oxpricealarm object
     * and passes it's data to Smarty engine. Returns name of template file
     * "pricealarm_main.tpl".
     *
     * @return string
     */
    public function render()
    {
        $config = $this->getConfig();

        parent::render();

        $shopId = $config->getShopId();
        //articles price in subshop and baseshop can be different
        $this->_aViewData['iAllCnt'] = 0;
        $query = "
            SELECT oxprice, oxartid
            FROM oxpricealarm
            WHERE oxsended = '000-00-00 00:00:00' AND oxshopid = '$shopId' ";
        $result = oxDb::getDb()->select($query);
        if ($result != false && $result->count() > 0) {
            $simpleCache = array();
            while (!$result->EOF) {
                $price = $result->fields[0];
                $articleId = $result->fields[1];
                if (isset($simpleCache[$articleId])) {
                    if ($simpleCache[$articleId] <= $price) {
                        $this->_aViewData['iAllCnt'] += 1;
                    }
                } else {
                    $article = oxNew("oxArticle");
                    if ($article->load($articleId)) {
                        $articlePrice = $simpleCache[$articleId] = $article->getPrice()->getBruttoPrice();
                        if ($articlePrice <= $price) {
                            $this->_aViewData['iAllCnt'] += 1;
                        }
                    }
                }
                $result->fetchRow();
            }
        }

        return "pricealarm_mail.tpl";
    }
}
