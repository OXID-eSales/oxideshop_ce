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
 * Admin user articles setting manager.
 * Collects user articles settings, updates it on user submit, etc.
 * Admin Menu: User Administration -> Users -> Articles.
 * @package admin
 */
class User_Article extends oxAdminDetails
{
    /**
     * Executes parent method parent::render(), creates oxlist object and returns name
     * of template file "user_article.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $soxId = $this->getEditObjectId();
        if ( $soxId && $soxId != '-1' ) {
            // load object
            $oArticlelist = oxNew( 'oxorderarticlelist' );
            $oArticlelist->loadOrderArticlesForUser($soxId);

            $this->_aViewData['oArticlelist'] =  $oArticlelist;
        }

        return 'user_article.tpl';
    }
}
