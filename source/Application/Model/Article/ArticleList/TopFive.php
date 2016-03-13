<?php
namespace OxidEsales\Eshop\Application\Model\Article\ArticleList;

use OxidEsales\Eshop\Application\Model\Article\ListArticle;

class TopFive
{
    public function getAll()
    {
        /*
         *         //has module?
        $myConfig = $this->getConfig();

        if (!$myConfig->getConfigParam('bl_perfLoadPriceForAddList')) {
            $this->getBaseObject()->disablePriceLoad();
        }

        switch ($myConfig->getConfigParam('iTop5Mode')) {
            case 0:
                // switched off, do nothing
                break;
            case 1:
                // manually entered
                $this->loadActionArticles('oxtop5', $iLimit);
                break;
            case 2:
                $sArticleTable = getViewName('oxarticles');

                //by default limit 5
                $sLimit = ($iLimit > 0) ? "limit " . $iLimit : 'limit 5';

                $sSelect = "select * from $sArticleTable ";
                $sSelect .= "where " . $this->getBaseObject()->getSqlActiveSnippet() . " and $sArticleTable.oxissearch = 1 ";
                $sSelect .= "and $sArticleTable.oxparentid = '' and $sArticleTable.oxsoldamount>0 ";
                $sSelect .= "order by $sArticleTable.oxsoldamount desc $sLimit";

                $this->selectString($sSelect);
                break;
        }
         */

        $iLimit = 5;

        $article = new ListArticle();

        $sArticleTable = getViewName('oxarticles');

        $sSelect = "select oxid from $sArticleTable ";
        $sSelect .= "where " . $article->getSqlActiveSnippet() . " and $sArticleTable.oxissearch = 1 ";
        $sSelect .= "and $sArticleTable.oxparentid = '' and $sArticleTable.oxsoldamount>0 ";
        $sSelect .= "order by $sArticleTable.oxsoldamount desc limit $iLimit";

        $oDb = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC);
        $ids = $oDb->getAll($sSelect);

        foreach ($ids as $id) {
            $article = new ListArticle();
            $article->load(current($id));
            yield $article;
        }
    }
}