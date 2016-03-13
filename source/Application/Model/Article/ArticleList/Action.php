<?php
namespace OxidEsales\Eshop\Application\Model\Article\ArticleList;

use OxidEsales\Eshop\Application\Model\Article\ListArticle;

class Action
{
    public function getById($sActionID)
    {
        $article = new ListArticle();
        $iLimit = 5;

        $sShopID = \oxRegistry::getConfig()->getShopId();
        $sActionID = \oxDb::getDb()->quote(strtolower($sActionID));

        //echo $sSelect;
        $sArticleTable = $article->getViewName();

        $oBase = oxNew("oxActions");
        $sActiveSql = $oBase->getSqlActiveSnippet();
        $sViewName = $oBase->getViewName();

        $sLimit = ($iLimit > 0) ? "limit " . $iLimit : '';

        $sSelect = "select $sArticleTable.oxid from oxactions2article
                      left join $sArticleTable on $sArticleTable.oxid = oxactions2article.oxartid
                      left join $sViewName on $sViewName.oxid = oxactions2article.oxactionid
                      where oxactions2article.oxshopid = '$sShopID' and oxactions2article.oxactionid = $sActionID and $sActiveSql
                      and $sArticleTable.oxid is not null and " . $article->getSqlActiveSnippet() . "
                      order by oxactions2article.oxsort $sLimit";

        $oDb = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC);
        $ids = $oDb->getAll($sSelect);

        foreach ($ids as $id) {
            $article = new ListArticle();
            $article->load(current($id));
            yield $article;
        }
    }
}