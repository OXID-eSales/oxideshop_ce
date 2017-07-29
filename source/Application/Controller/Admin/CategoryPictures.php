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

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin article categories thumbnail manager.
 * Category thumbnail manager (Previews assigned pictures).
 * Admin Menu: Manage Products -> Categories -> Thumbnail.
 */
class CategoryPictures extends \OxidEsales\Eshop\Application\Controller\Admin\AdminDetailsController
{

    /**
     * Loads category object, passes it to Smarty engine and returns name
     * of template file "category_pictures.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        $this->_aViewData['edit'] = $oCategory = oxNew(\OxidEsales\Eshop\Application\Model\Category::class);

        $soxId = $this->getEditObjectId();
        if (isset($soxId) && $soxId != '-1') {
            // load object
            $oCategory->load($soxId);
        }

        return "category_pictures.tpl";
    }
}
