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

namespace OxidEsales\EshopCommunity\Application\Controller;

use oxUBase;

/**
 * Handles adding article to recommendation list process.
 * Due to possibility of external modules we recommned to extend the vews from oxUBase view.
 * However expreimentally we extend RecommAdd from Details view here.
 *
 * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
 */
class RecommendationAddController extends \Details
{
    /**
     * Template name
     *
     * @var string
     */
    protected $_sThisTemplate = 'page/account/recommendationadd.tpl';

    /**
     * User recommendation lists
     *
     * @var array
     */
    protected $_aUserRecommList = null;

    /**
     * Renders the view
     *
     * @return unknown
     */
    public function render()
    {
        oxUBase::render();

        return $this->_sThisTemplate;
    }

    /**
     * Returns user recommlists
     *
     * @return array
     */
    public function getRecommLists()
    {
        if ($this->_aUserRecommList === null) {
            $oUser = $this->getUser();
            if ($oUser) {
                $this->_aUserRecommList = $oUser->getUserRecommLists();
            }
        }

        return $this->_aUserRecommList;
    }

    /**
     * Returns the title of the product added to the recommendation list.
     *
     * @return string
     */
    public function getTitle()
    {
        $oProduct = $this->getProduct();

        return $oProduct->oxarticles__oxtitle->value . ' ' . $oProduct->oxarticles__oxvarselect->value;
    }
}
