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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

/**
 * Product Ratings widget.
 * Forms product ratings.
 */
class oxwRating extends oxWidget
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * User component used in template.
     * @var array
     */
    protected $_aComponentNames = array( 'oxcmp_user' => 1 );

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/reviews/rating.tpl';

    /**
     * Rating value
     *
     * @var double
     */
    protected $_dRatingValue = null;

    /**
     * Rating count
     *
     * @var integer
     */
    protected $_iRatingCnt = null;


    /**
     * Executes parent::render().
     * Returns name of template file to render.
     *
     * @return string current template file name
     */
    public function render()
    {
        parent::render();
        return $this->_sThisTemplate;
    }

    /**
     * Template variable getter. Returns rating value
     *
     * @return double
     */
    public function getRatingValue()
    {
        if ( $this->_dRatingValue === null ) {
            $this->_dRatingValue = (double)0;
            $dValue = $this->getViewParameter( "dRatingValue" );
            if ( $dValue ) {
                $this->_dRatingValue = round( $dValue, 1);
            }
        }
        return (double) $this->_dRatingValue;
    }

    /**
     * Template variable getter. Returns rating count
     *
     * @return integer
     */
    public function getRatingCount()
    {
        return $dCount = $this->getViewParameter( "dRatingCount" );
    }

    /**
     * Template variable getter. Returns rating url
     *
     * @return string
     */
    public function getRateUrl()
    {
        return $this->getViewParameter( "sRateUrl" );
    }

    /**
     * Template variable getter. Returns rating count
     *
     * @return integer
     */
    public function canRate()
    {
        return $this->getViewParameter( "blCanRate" );
    }

    /**
     * Template variable getter. Returns article nid
     *
     * @return string
     */
    public function getArticleNId()
    {
        return $this->getViewParameter( 'anid' );
    }
}
