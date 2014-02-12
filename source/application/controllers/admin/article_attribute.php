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
 * Admin article attributes/selections lists manager.
 * Collects available attributes/selections lists for chosen article, may add
 * or remove any of them to article, etc.
 * Admin Menu: Manage Products -> Articles -> Selection.
 * @package admin
 */
class Article_Attribute extends oxAdminDetails
{
    /**
     * Collects article attributes and selection lists, passes them to Smarty engine,
     * returns name of template file "article_attribute.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oArticle = oxNew( 'oxarticle' );

        $soxId = $this->getEditObjectId();
        if ( $soxId != "-1" && isset( $soxId)) {
            // load object
            $oArticle->load( $soxId);

            if ( $oArticle->isDerived() ) {
                $this->_aViewData["readonly"] = true;
            }
        }

        $iAoc = oxConfig::getParameter("aoc");
        if ( $iAoc == 1 ) {
            $oArticleAttributeAjax = oxNew( 'article_attribute_ajax' );
            $this->_aViewData['oxajax'] = $oArticleAttributeAjax->getColumns();

            return "popups/article_attribute.tpl";
        } elseif ( $iAoc == 2 ) {            
            $oArticleSelectionAjax = oxNew( 'article_selection_ajax' );
            $this->_aViewData['oxajax'] = $oArticleSelectionAjax->getColumns();

            return "popups/article_selection.tpl";
        }

        return "article_attribute.tpl";
    }
}
