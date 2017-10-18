<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Application\Controller\Admin;

use oxRegistry;
use oxUser;

/**
 * Admin user list manager.
 * Performs collection and managing (such as filtering or deleting) function.
 * Admin Menu: User Administration -> Users.
 */
class UserList extends \OxidEsales\Eshop\Application\Controller\Admin\AdminListController
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = 'oxuser';

    /**
     * Default SQL sorting parameter (default null).
     *
     * @var string
     */
    protected $_sDefSortField = "oxusername";

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxuserlist';

    /**
     * Current class template name.
     *
     * @var string
     */
    protected $_sThisTemplate = 'user_list.tpl';

    /**
     * Executes parent::render(), sets blacklist and preventdelete flag
     *
     * @return null
     */
    public function render()
    {
        foreach ($this->getItemList() as $itemId => $user) {
            /** @var \OxidEsales\Eshop\Application\Model\User $user */
            if ($user->inGroup("oxidblacklist") || $user->inGroup("oxidblocked")) {
                $user->blacklist = "1";
            }
            $user->blPreventDelete = false;
            if (!$this->_allowAdminEdit($itemId)) {
                $user->blPreventDelete = true;
            }
        }

        return parent::render();
    }

    /**
     * Admin user is allowed to be deleted only by mall admin
     *
     * @return null
     */
    public function deleteEntry()
    {
        if ($this->_allowAdminEdit($this->getEditObjectId())) {
            $this->_oList = null;

            return parent::deleteEntry();
        }
    }

    /**
     * Prepares SQL where query according SQL condition array and attaches it to SQL end.
     * For each search value if german umlauts exist, adds them
     * and replaced by spec. char to query
     *
     * @param array  $whereQuery SQL condition array
     * @param string $fullQuery  SQL query string
     *
     * @return string
     */
    public function _prepareWhereQuery($whereQuery, $fullQuery)
    {
        $nameWhere = null;
        if (isset($whereQuery['oxuser.oxlname']) && ($name = $whereQuery['oxuser.oxlname'])) {
            // check if this is search string (contains % sign at begining and end of string)
            $isSearchValue = $this->_isSearchValue($name);
            $name = $this->_processFilter($name);
            $nameWhere['oxuser.oxfname'] = $nameWhere['oxuser.oxlname'] = $name;

            unset($whereQuery['oxuser.oxlname']);
        }
        $query = parent::_prepareWhereQuery($whereQuery, $fullQuery);

        if ($nameWhere) {
            $values = explode(' ', $name);
            $query .= ' and (';
            $queryBoolAction = '';
            $utilsString = \OxidEsales\Eshop\Core\Registry::getUtilsString();

            foreach ($nameWhere as $fieldName => $fieldValue) {
                //for each search field using AND action
                foreach ($values as $value) {
                    $query .= " {$queryBoolAction} {$fieldName} ";

                    //for search in same field for different values using AND
                    $queryBoolAction = ' or ';

                    $query .= $this->_buildFilter($value, $isSearchValue);

                    // trying to search spec chars in search value
                    // if found, add cleaned search value to search sql
                    $uml = $utilsString->prepareStrForSearch($value);
                    if ($uml) {
                        $query .= " or {$fieldName} ";
                        $query .= $this->_buildFilter($uml, $isSearchValue);
                    }
                }
            }

            // end for AND action
            $query .= ' ) ';
        }

        return $query;
    }
}
