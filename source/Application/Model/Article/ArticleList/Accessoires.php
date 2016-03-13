<?php
namespace OxidEsales\Eshop\Application\Model\Article\ArticleList;

use OxidEsales\Eshop\Application\Model\Article\ListArticle;

class Accessoires
{
    public function getById($sArticleId)
    {
        $article = new ListArticle();
        $sArticleId = \oxDb::getDb()->quote($sArticleId);

        $sArticleTable = $article->getViewName();

        $sSelect = "select $sArticleTable.oxid from oxaccessoire2article left join $sArticleTable on oxaccessoire2article.oxobjectid=$sArticleTable.oxid ";
        $sSelect .= "where oxaccessoire2article.oxarticlenid = $sArticleId ";
        $sSelect .= " and $sArticleTable.oxid is not null and " . $article->getSqlActiveSnippet();
        $sSelect .= " order by oxaccessoire2article.oxsort";

        $oDb = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC);
        $ids = $oDb->getAll($sSelect);

        foreach ($ids as $id) {
            $article = new ListArticle();
            $article->load(current($id));
            yield $article;
        }
    }
}