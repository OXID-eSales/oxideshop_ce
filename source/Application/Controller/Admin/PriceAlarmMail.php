<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxDb;

/**
 * Admin article main pricealarm manager.
 * Performs collection and updatind (on user submit) main item information.
 * Admin Menu: Customer Info -> pricealarm -> Main.
 */
class PriceAlarmMail extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
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
            WHERE oxsended = '000-00-00 00:00:00' AND oxshopid = :oxshopid";
        $result = \OxidEsales\Eshop\Core\DatabaseProvider::getDb()->select($query, [
            ':oxshopid' => $shopId
        ]);
        if ($result != false && $result->count() > 0) {
            $simpleCache = [];
            while (!$result->EOF) {
                $price = $result->fields[0];
                $articleId = $result->fields[1];
                if (isset($simpleCache[$articleId])) {
                    if ($simpleCache[$articleId] <= $price) {
                        $this->_aViewData['iAllCnt'] += 1;
                    }
                } else {
                    $article = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);
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
