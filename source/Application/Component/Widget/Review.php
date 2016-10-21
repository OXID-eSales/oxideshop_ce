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
 * Product reviews widget
 */
class Review extends \oxWidget
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = array('oxcmp_user' => 1);

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/reviews/reviews.tpl';

    /**
     * Executes parent::render().
     * Returns name of template file to render.
     *
     * @return  string  current template file name
     */
    public function render()
    {
        parent::render();

        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns review type
     *
     * @return string
     */
    public function getReviewType()
    {
        return strtolower($this->getViewParameter('type'));
    }

    /**
     * Template variable getter. Returns article id
     *
     * @return string
     */
    public function getArticleId()
    {
        return $this->getViewParameter('aid');
    }

    /**
     * Template variable getter. Returns article nid
     *
     * @return string
     */
    public function getArticleNId()
    {
        return $this->getViewParameter('anid');
    }

    /**
     * Template variable getter. Returns recommlist id
     *
     * @deprecated since v5.3 (2016-06-17); Listmania will be moved to an own module.
     *
     * @return string
     */
    public function getRecommListId()
    {
        return $this->getViewParameter('recommid');
    }

    /**
     * Template variable getter. Returns whether user can rate
     *
     * @return string
     */
    public function canRate()
    {
        return $this->getViewParameter('canrate');
    }

    /**
     * Template variable getter. Returns review user id
     *
     * @return string
     */
    public function getReviewUserHash()
    {
        return $this->getViewParameter('reviewuserhash');
    }

    /**
     * Template variable getter. Returns active object's reviews from parent class
     *
     * @return array
     */
    public function getReviews()
    {
        $oReview = $this->getConfig()->getTopActiveView();

        return $oReview->getReviews();
    }
}
