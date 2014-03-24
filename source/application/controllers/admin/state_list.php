<?php


/**
 * Admin state manager.
 * Returns template, that arranges two other templates ("state_list.tpl"
 * and "state_main.tpl") to frame.
 * @package admin
 */
class state_list extends oxAdminList
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxstate';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = 'oxcountryid';

    /**
     * Enable/disable sorting by DESC (SQL) (default false - disable).
     *
     * @var bool
     */
    protected $_blDesc = false;

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'state_list.tpl';

    /**
     * Load category list, mark active category;
     *
     * @return oxCountryList
     */
    public function getCountryList()
    {
        $aWhere  = oxRegistry::getConfig()->getRequestParameter("where");
        $sActCountry = $aWhere['oxstates']['oxcountryid'];

        /** @var oxCountryList $oCountryList */
        $oCountryList = oxNew("oxCountryList");
        $oCountryList->loadList();
        foreach ($oCountryList as $oCountry) {
            if ($oCountry->oxcountry__oxid->value == $sActCountry) {
                $oCountry->selected = 1;
                break;
            }
        }
        return $oCountryList;
    }
}