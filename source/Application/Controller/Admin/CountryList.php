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
 * Admin selectlist list manager.
 */
class CountryList extends \oxAdminList
{

    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxcountry';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxactive';

    /**
     * Default second SQL sorting parameter.
     *
     * @var string
     */
    protected $sSecondDefSortField = 'oxtitle';

    /**
     * Enable/disable sorting by DESC (SQL) (default false - disable).
     *
     * @var bool
     */
    protected $_blDesc = false;

    /**
     * Executes parent method parent::render() and returns name of template
     * file "selectlist_list.tpl".
     *
     * @return string
     */
    public function render()
    {
        parent::render();

        return "country_list.tpl";
    }

    /**
     * Returns sorting fields array. We extend this method for getting a second order by, which will give us not the
     * undefined order behind the "active" countries.
     *
     * @return array
     */
    public function getListSorting()
    {
        $aListSorting = parent::getListSorting();

        if (array_keys($aListSorting['oxcountry']) === array('oxactive')) {
            $aListSorting['oxcountry'][$this->_getSecondSortFieldName()] = 'asc';
        }

        return $aListSorting;
    }

    /**
     * Getter for the second sort field name (for getting the expected oreder out of the databse).
     *
     * @return string The name of the field we want to be the second order by argument.
     */
    protected function _getSecondSortFieldName()
    {
        return $this->sSecondDefSortField;
    }

}
