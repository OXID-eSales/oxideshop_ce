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
    protected $_aComponentNames = array('oxcmp_cur' => 1);

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
