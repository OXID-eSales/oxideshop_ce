<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Component\Widget;

/**
 * Recomendation list.
 * Forms recomendation list.
 *
 * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
 */
class Recommendation extends \OxidEsales\Eshop\Application\Component\Widget\WidgetController
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = ['oxcmp_cur' => 1];

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/sidebar/recommendation.tpl';

    /**
     * Returns similar recommendation list.
     *
     * @return array
     */
    public function getSimilarRecommLists()
    {
        $aArticleIds = $this->getViewParameter("aArticleIds");

        $oRecommList = oxNew(\OxidEsales\Eshop\Application\Model\RecommendationList::class);

        return $oRecommList->getRecommListsByIds($aArticleIds);
    }

    /**
     * Return recomm list object.
     *
     * @return object
     */
    public function getRecommList()
    {
        return oxNew(\OxidEsales\Eshop\Application\Controller\RecommListController::class);
    }
}
