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
 * @copyright (C) OXID eSales AG 2003-2015
 * @version   OXID eShop CE
 */

/**
 * Shop language manager.
 * Performs language manager function: changes template settings, modifies URL's.
 *
 * @subpackage oxcmp
 */
class oxcmp_lang extends oxView
{

    /**
     * Marking object as component
     *
     * @var bool
     */
    protected $_blIsComponent = true;

    /**
     * Executes parent::render() and returns array with languages.
     *
     * @return array $this->aLanguages languages
     */
    public function render()
    {
        parent::render();

        // Performance
        if ($this->getConfig()->getConfigParam('bl_perfLoadLanguages')) {
            $aLanguages = oxRegistry::getLang()->getLanguageArray(null, true, true);
            reset($aLanguages);
            while (list($sKey, $oVal) = each($aLanguages)) {
                $aLanguages[$sKey]->link = $this->getConfig()->getTopActiveView()->getLink($oVal->id);
            }

            return $aLanguages;
        }
    }
}
