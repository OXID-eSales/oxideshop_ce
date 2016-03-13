<?php
namespace OxidEsales\Eshop\Application\Model\Article\ArticleList;

use OxidEsales\Eshop\Application\Model\Article\ListArticle;

class History extends AbstractList
{
    public function getById($sArtId)
    {
        $iCnt = 4;

        $aHistoryArticles = $this->getHistoryArticles();
        $aHistoryArticles[] = $sArtId;

        // removing duplicates
        $aHistoryArticles = array_unique($aHistoryArticles);
        if (count($aHistoryArticles) > ($iCnt + 1)) {
            array_shift($aHistoryArticles);
        }

        $this->setHistoryArticles($aHistoryArticles);

        //remove current article and return array
        if (($iCurrentArt = array_search($sArtId, $aHistoryArticles)) !== false) {
            unset($aHistoryArticles[$iCurrentArt]);
        }

        $ids = array_values($aHistoryArticles);
        return $this->yieldByIds($ids);
    }

    /**
     * Get history article id's from session or cookie.
     *
     * @return array
     */
    private function getHistoryArticles()
    {
        if ($sArticlesIds = \oxRegistry::get("oxUtilsServer")->getOxCookie('aHistoryArticles')) {
            return explode('|', $sArticlesIds);
        }
    }

    private function setHistoryArticles($aArticlesIds)
    {
        \oxRegistry::get("oxUtilsServer")->setOxCookie('aHistoryArticles', implode('|', $aArticlesIds));
    }
}