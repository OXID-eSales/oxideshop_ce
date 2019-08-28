<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxDb;

/**
 * Admin article overview manager.
 * Collects and previews such article information as article creation date,
 * last modification date, sales rating and etc.
 * Admin Menu: Manage Products -> Articles -> Overview.
 */
class ArticleOverview extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{
    /**
     * Loads article overview data, passes to Smarty engine and returns name
     * of template file "article_overview.tpl".
     *
     * @return string
     */
    public function render()
    {
        $myConfig = $this->getConfig();

        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew(\OxidEsales\Eshop\Application\Model\Article::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != "-1") {
            $oDB = $this->getDatabase();

            // load object
            $this->updateArticle($oArticle, $soxId);

            $sShopID = $myConfig->getShopID();

            $sSelect = $this->formOrderAmountQuery($soxId);
            $this->_aViewData["totalordercnt"] = $iTotalOrderCnt = (float) $oDB->getOne($sSelect);

            $sSelect = $this->formSoldOutAmountQuery($soxId);
            $this->_aViewData["soldcnt"] = $iSoldCnt = (float) $oDB->getOne($sSelect);

            $sSelect = $this->formCanceledAmountQuery($soxId);
            $this->_aViewData["canceledcnt"] = $iCanceledCnt = (float) $oDB->getOne($sSelect);

            // not yet processed
            $this->_aViewData["leftordercnt"] = $iTotalOrderCnt - $iSoldCnt - $iCanceledCnt;

            // position in top ten
            $sSelect = "select oxartid,sum(oxamount) as cnt from oxorderarticles " .
                       "where oxordershopid = :oxordershopid group by oxartid order by cnt desc";

            $rs = $oDB->select($sSelect, [
                ':oxordershopid' => $sShopID
            ]);
            $iTopPos = 0;
            $iPos = 0;
            if ($rs != false && $rs->count() > 0) {
                while (!$rs->EOF) {
                    $iPos++;
                    if ($rs->fields[0] == $soxId) {
                        $iTopPos = $iPos;
                    }
                    $rs->fetchRow();
                }
            }

            $this->_aViewData["postopten"] = $iTopPos;
            $this->_aViewData["toptentotal"] = $iPos;
        }

        $this->_aViewData["afolder"] = $myConfig->getConfigParam('aProductfolder');
        $this->_aViewData["aSubclass"] = $myConfig->getConfigParam('aArticleClasses');

        return "article_overview.tpl";
    }

    /**
     * @return DatabaseInterface
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
        $article->loadInLang(\OxidEsales\Eshop\Core\Registry::getConfig()->getRequestParameter("editlanguage"), $oxId);

        return $article;
    }
}
