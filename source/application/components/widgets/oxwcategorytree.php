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
 * Category tree widget.
 * Forms category tree.
 */
class oxwCategoryTree extends oxWidget
{
    /**
     * Names of components (classes) that are initiated and executed
     * before any other regular operation.
     * Cartegory component used in template.
     *
     * @var array
     */
    protected $_aComponentNames = array( 'oxcmp_categories' => 1 );

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'widget/sidebar/categorytree.tpl';

    /**
     * Executes parent::render(), assigns template name and returns it
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        if ($sTpl = $this->getViewParameter( "sWidgetType" ) ) {
            $sTemplateName = 'widget/' . basename($sTpl) . '/categorylist.tpl';
            if ($this->getConfig()->getTemplatePath($sTemplateName, $this->isAdmin())) {
                $this->_sThisTemplate = $sTemplateName;
            }
        }
        return $this->_sThisTemplate;
    }

    /**
     * Returns the deep level of category tree
     *
     * @return null
     */
    public function getDeepLevel()
    {
        return $this->getViewParameter( "deepLevel" );
    }

}
