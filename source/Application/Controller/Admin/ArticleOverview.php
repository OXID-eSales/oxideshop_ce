<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use OxidEsales\Eshop\Core\Registry;

/**
 * Admin article overview manager.
 * Collects and previews such article information as article creation date,
 * last modification date, sales rating and etc.
 * Admin Menu: Manage Products -> Articles -> Overview.
 */
class ArticleOverview extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /** @inheritdoc */
    public function render()
    {
        $config = Registry::getConfig();

        parent::render();

        $this->_aViewData['edit'] = $product = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        $productEditId = $this->getEditObjectId();
        if (isset($productEditId) && $productEditId != "-1") {
            $database = $this->getDatabase();

            $this->updateArticle($product, $productEditId);

            $shopId = $config->getShopID();

            $query = $this->formOrderAmountQuery($productEditId);
            $this->_aViewData["totalordercnt"] = $iTotalOrderCnt = (float) $database->getOne($query);

            $query = $this->formSoldOutAmountQuery($productEditId);
            $this->_aViewData["soldcnt"] = $iSoldCnt = (float) $database->getOne($query);

            $query = $this->formCanceledAmountQuery($productEditId);
            $this->_aViewData["canceledcnt"] = $iCanceledCnt = (float) $database->getOne($query);

            $this->_aViewData["leftordercnt"] = $iTotalOrderCnt - $iSoldCnt - $iCanceledCnt;

            $query = "select oxartid,sum(oxamount) as cnt from oxorderarticles " .
                       "where oxordershopid = :oxordershopid group by oxartid order by cnt desc";

            $productIds = $database->getCol($query, [
                'oxordershopid' => $shopId
            ]);
            $topPosition = 0;
            $position = 0;

            foreach ($productIds as $productId) {
                $position++;
                if ($productId == $productEditId) {
                    $topPosition = $position;
                }
            }

            $this->_aViewData["postopten"] = $topPosition;
            $this->_aViewData["toptentotal"] = $position;
        }

        $this->_aViewData["afolder"] = $config->getConfigParam('aProductfolder');
        $this->_aViewData["aSubclass"] = $config->getConfigParam('aArticleClasses');

        return "article_overview";
    }

    /**
     * @return \OxidEsales\Eshop\Core\Database\Adapter\DatabaseInterface
     */
    protected function getDatabase()
    {
        return \OxidEsales\Eshop\Core\DatabaseProvider::getDb();
    }

    /**
     * Forms query to get total order count.
     *
     * @param string $oxId
     *
     * @return string
     */
    protected function formOrderAmountQuery($oxId)
    {
        $query = "select sum(oxamount) from oxorderarticles ";
        $query .= "where oxartid=" . $this->getDatabase()->quote($oxId);

        return $query;
    }

    /**
     * Forms query to get sold out amount count.
     *
     * @param string $oxId
     *
     * @return string
     */
    protected function formSoldOutAmountQuery($oxId)
    {
        return "select sum(oxorderarticles.oxamount) from  oxorderarticles, oxorder " .
            "where (oxorder.oxpaid>0 or oxorder.oxsenddate > 0) and oxorderarticles.oxstorno != '1' " .
            "and oxorderarticles.oxartid=" . $this->getDatabase()->quote($oxId) .
            "and oxorder.oxid =oxorderarticles.oxorderid";
    }

    /**
     * Forms query to get canceled amount count.
     *
     * @param string $soxId
     *
     * @return string
     */
    protected function formCanceledAmountQuery($soxId)
    {
        return "select sum(oxamount) from oxorderarticles where oxstorno = '1' " .
            "and oxartid=" . $this->getDatabase()->quote($soxId);
    }

    /**
     * Loads language for article object.
     *
     * @param \OxidEsales\Eshop\Application\Model\Article $article
     * @param string                                      $oxId
     *
     * @return \OxidEsales\Eshop\Application\Model\Article
     */
    protected function updateArticle($article, $oxId)
    {
        $article->loadInLang(Registry::getRequest()->getRequestEscapedParameter("editlanguage"), $oxId);

        return $article;
    }
}
