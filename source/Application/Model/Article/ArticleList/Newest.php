<?php
namespace OxidEsales\Eshop\Application\Model\Article\ArticleList;

use OxidEsales\Eshop\Application\Model\Article\ListArticle;

class Newest
{
    public function getAll()
    {

        /*
         *         if (!$myConfig->getConfigParam('bl_perfLoadPriceForAddList')) {
            $this->getBaseObject()->disablePriceLoad();
        }

        $this->_aArray = array();
        switch ($myConfig->getConfigParam('iNewestArticlesMode')) {
            case 0:
                // switched off, do nothing
                break;
            case 1:
                // manually entered
                $this->loadActionArticles('oxnewest', $iLimit);
                break;
            case 2:
                $sArticleTable = getViewName('oxarticles');
                if ($myConfig->getConfigParam('blNewArtByInsert')) {
                    $sType = 'oxinsert';
                } else {
                    $sType = 'oxtimestamp';
                }
         */

        $iLimit = 5;

        $article = new ListArticle();

        $sArticleTable = getViewName('oxarticles');
        $sType = 'oxinsert';

        $sSelect = "select oxid from $sArticleTable ";
        $sSelect .= "where oxparentid = '' and " . $article->getSqlActiveSnippet() . " and oxissearch = 1 order by $sType desc ";
        $sSelect .= "limit " . $iLimit;

        $oDb = \oxDb::getDb(\oxDb::FETCH_MODE_ASSOC);
        $ids = $oDb->getAll($sSelect);

        foreach ($ids as $id) {
            $article = new ListArticle();
            $article->load(current($id));
            yield $article;
        }
    }
}