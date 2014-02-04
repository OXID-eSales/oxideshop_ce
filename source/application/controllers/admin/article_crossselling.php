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
 * Admin article crosselling/accesories manager.
 * Creates list of available articles, there is ability to assign or remove
 * assigning of article to crosselling/accesories with other products.
 * Admin Menu: Manage Products -> Articles -> Crosssell.
 * @package admin
 */
class Article_Crossselling extends oxAdminDetails
{
    /**
     * Collects article crosselling and attributes information, passes
     * them to Smarty engine and returns name or template file
     * "article_crossselling.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew( 'oxarticle' );

        // crossselling
        $this->_createCategoryTree( "artcattree");

        // accessoires
        $this->_createCategoryTree( "artcattree2");

        $soxId = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId ) ) {
            // load object
            $oArticle->load( $soxId);

            if ($oArticle->isDerived())
                $this->_aViewData['readonly'] = true;
        }

        $iAoc = oxConfig::getParameter("aoc");
        if ( $iAoc == 1 ) {            
            $oArticleCrossellingAjax = oxNew( 'article_crossselling_ajax' );
            $this->_aViewData['oxajax'] = $oArticleCrossellingAjax->getColumns();

            return "popups/article_crossselling.tpl";
        } elseif ( $iAoc == 2 ) {
            $oArticleAccessoriesAjax = oxNew( 'article_accessories_ajax' );
            $this->_aViewData['oxajax'] = $oArticleAccessoriesAjax->getColumns();

            return "popups/article_accessories.tpl";
        }
        return "article_crossselling.tpl";
    }
}
