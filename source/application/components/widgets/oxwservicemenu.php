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
 * Recomendation list.
 * Forms recomendation list.
 */
class oxwServiceMenu extends oxWidget
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
     * @var string
     */
    protected $_sThisTemplate = 'widget/header/servicemenu.tpl';

    /**
     * Template variable getter. Returns article list count in comparison.
     *
     * @return integer
     */
    public function getCompareItemsCnt()
    {
        $oCompare = oxNew( "compare" );
        $iCompItemsCnt = $oCompare->getCompareItemsCnt();
        return $iCompItemsCnt;
    }

    /**
     * Template variable getter. Returns comparison article list.
     *
     * @param bool $blJson return json encoded array
     *
     * @return array
     */
    public function getCompareItems($blJson = false)
    {
        $oCompare = oxNew( "compare" );
        $aCompareItems = $oCompare->getCompareItems();

        if ($blJson) {
            $aCompareItems = json_encode($aCompareItems);
        }

        return $aCompareItems;
    }

}
