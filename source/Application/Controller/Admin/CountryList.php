<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

/**
 * Admin selectlist list manager.
 */
class CountryList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
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

        if (array_keys($aListSorting['oxcountry']) === ['oxactive']) {
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
