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

use oxRegistry;
use oxDb;
use oxField;
use stdClass;
use oxList;
use oxBase;
use oxI18n;

/**
 * Admin selectlist list manager.
 */
class AdminListController extends \oxAdminView
{
    /**
     * Name of chosen object class (default null).
     *
     * @var string
     */
    protected $_sListClass = null;

    /**
     * Type of list.
     *
     * @var string
     */
    protected $_sListType = 'oxlist';

    /**
     * List of objects (default null).
     *
     * @var oxList
     */
    protected $_oList = null;

    /**
     * Position in list of objects (default 0).
     *
     * @var int
     */
    protected $_iCurrListPos = 0;

    /**
     * Size of object list (default 0).
     *
     * @var int
     */
    protected $_iListSize = 0;

    /**
     * Array of SQL query conditions (default null).
     *
     * @var array
     */
    protected $_aWhere = null;

    /**
     * Enable/disable sorting by DESC (SQL) (default false - disable).
     *
     * @var bool
     */
    protected $_blDesc = false;

    /**
     * Set to true to enable multi language
     *
     * @var bool
     */
    protected $_blEmployMultilanguage = null;

    /**
     * (default null).
     *
     * @var int
     */
    protected $_iOverPos = null;

    /**
     * Viewable list size
     *
     * @var int
     */
    protected $_iViewListSize = 0;

    /**
     * Viewable default list size (used in list_*.php views)
     *
     * @var int
     */
    protected $_iDefViewListSize = 50;

    /**
     * List sorting array
     *
     * @var array
     */
    protected $_aCurrSorting = null;

    /**
     * Default sorting field
     *
     * @var string
     */
    protected $_sDefSortField = null;

    /**
     * List filter array
     *
     * @var array
     */
    protected $_aListFilter = null;

    /**
     * Returns sorting fields array
     *
     * @return array
     */
    public function getListSorting()
    {
        if ($this->_aCurrSorting === null) {
            $this->_aCurrSorting = oxRegistry::getConfig()->getRequestParameter('sort');

            if (!$this->_aCurrSorting && $this->_sDefSortField && ($baseObject = $this->getItemListBaseObject())) {
                $this->_aCurrSorting[$baseObject->getCoreTableName()] = array($this->_sDefSortField => "asc");
            }
        }

        return $this->_aCurrSorting;
    }

    /**
     * Returns list filter array
     *
     * @return array
     */
    public function getListFilter()
    {
        if ($this->_aListFilter === null) {
            $this->_aListFilter = oxRegistry::getConfig()->getRequestParameter("where");
        }

        return $this->_aListFilter;
    }

    /**
     * Viewable list size getter
     *
     * @return int
     */
    protected function _getViewListSize()
    {
        if (!$this->_iViewListSize) {
            $config = $this->getConfig();
            if ($profile = oxRegistry::getSession()->getVariable('profile')) {
                if (isset($profile[1])) {
                    $config->setConfigParam('iAdminListSize', (int)$profile[1]);
                }
            }

            $this->_iViewListSize = (int)$config->getConfigParam('iAdminListSize');
            if (!$this->_iViewListSize) {
                $this->_iViewListSize = 10;
                $config->setConfigParam('iAdminListSize', $this->_iViewListSize);
            }
        }

        return $this->_iViewListSize;
    }

    /**
     * Returns view list size
     *
     * @return int
     */
    public function getViewListSize()
    {
        return $this->_getViewListSize();
    }

    /**
     * Viewable list size getter (used in list_*.php views)
     *
     * @return int
     */
    protected function _getUserDefListSize()
    {
        if (!$this->_iViewListSize) {
            if (!($viewListSize = (int)oxRegistry::getConfig()->getRequestParameter('viewListSize'))) {
                $viewListSize = $this->_iDefViewListSize;
            }
            $this->_iViewListSize = $viewListSize;
        }

        return $this->_iViewListSize;
    }

    /**
     * Executes parent::render(), sets back search keys to view, sets navigation params
     *
     * @return null
     */
    public function render()
    {
        $return = parent::render();

        // assign our list
        $this->_aViewData['mylist'] = $this->getItemList();

        // set navigation parameters
        $this->_setListNavigationParams();

        return $return;
    }

    /**
     * Deletes this entry from the database
     *
     * @return null
     */
    public function deleteEntry()
    {
        $delete = oxNew($this->_sListClass);

        //disabling deletion for derived items
        if ($delete->isDerived()) {
            return;
        }

        $blDelete = $delete->delete($this->getEditObjectId());

        // #A - we must reset object ID
        if ($blDelete && isset($_POST['oxid'])) {
            $_POST['oxid'] = -1;
        }

        $this->resetContentCache();

        $this->init();
    }

    /**
     * Calculates list items count
     *
     * @param string $sql SQL query used co select list items
     */
    protected function _calcListItemsCount($sql)
    {
        $stringModifier = getStr();

        // count SQL
        $sql = $stringModifier->preg_replace('/select .* from/i', 'select count(*) from ', $sql);

        // removing order by
        $sql = $stringModifier->preg_replace('/order by .*$/i', '', $sql);

        // We force reading from master to prevent issues with slow replications or open transactions (see ESDEV-3804).
        // con of list items which fits current search conditions
        $this->_iListSize = oxDb::getMaster()->getOne($sql);

        // set it into session that other frames know about size of DB
        oxRegistry::getSession()->setVariable('iArtCnt', $this->_iListSize);
    }

    /**
     * Set current list position
     *
     * @param string $page jump page string
     */
    protected function _setCurrentListPosition($page = null)
    {
        $adminListSize = $this->_getViewListSize();

        $jumpToPage = $page ? ((int)$page) : ((int)((int)oxRegistry::getConfig()->getRequestParameter('lstrt')) / $adminListSize);
        $jumpToPage = ($page && $jumpToPage) ? ($jumpToPage - 1) : $jumpToPage;

        $jumpToPage = $jumpToPage * $adminListSize;
        if ($jumpToPage < 1) {
            $jumpToPage = 0;
        } elseif ($jumpToPage >= $this->_iListSize) {
            $jumpToPage = floor($this->_iListSize / $adminListSize - 1) * $adminListSize;
        }

        $this->_iCurrListPos = $this->_iOverPos = (int)$jumpToPage;
    }

    /**
     * Adds order by to SQL query string.
     *
     * @param string $query sql string
     *
     * @return string
     */
    protected function _prepareOrderByQuery($query = null)
    {
        // sorting
        $sortFields = $this->getListSorting();

        if (is_array($sortFields) && count($sortFields)) {
            // only add order by at full sql not for count(*)
            $query .= ' order by ';
            $addSeparator = false;

            $listItem = $this->getItemListBaseObject();
            $languageId = $listItem->isMultilang() ? $listItem->getLanguage() : oxRegistry::getLang()->getBaseLanguage();

            $descending = oxRegistry::getConfig()->getRequestParameter('adminorder');
            $descending = $descending !== null ? (bool)$descending : $this->_blDesc;

            foreach ($sortFields as $table => $fieldData) {
                $table = $table ? (getViewName($table, $languageId) . '.') : '';
                foreach ($fieldData as $column => $sortDirectory) {
                    $field = $table . $column;

                    //add table name to column name if no table name found attached to column name
                    $query .= ((($addSeparator) ? ', ' : '')) . oxDb::getDb()->quoteIdentifier($field);

                    //V oxActive field search always DESC
                    if ($descending || $column == "oxactive" || strcasecmp($sortDirectory, 'desc') == 0) {
                        $query .= ' desc ';
                    }

                    $addSeparator = true;
                }
            }
        }

        return $query;
    }

    /**
     * Builds and returns SQL query string.
     *
     * @param object $listObject list main object
     *
     * @return string
     */
    protected function _buildSelectString($listObject = null)
    {
        return $listObject !== null ? $listObject->buildSelectString(null) : "";
    }


    /**
     * Prepares SQL where query according SQL condition array and attaches it to SQL end.
     * For each search value if german umlauts exist, adds them
     * and replaced by spec. char to query
     *
     * @param string $fieldValue Filters
     *
     * @return string
     */
    protected function _processFilter($fieldValue)
    {
        $stringModifier = getStr();

        //removing % symbols
        $fieldValue = $stringModifier->preg_replace("/^%|%$/", "", trim($fieldValue));

        return $stringModifier->preg_replace("/\s+/", " ", $fieldValue);
    }

    /**
     * Builds part of SQL query
     *
     * @param string $value         filter value
     * @param bool   $isSearchValue filter value type, true means surrount search key with '%'
     *
     * @return string
     */
    protected function _buildFilter($value, $isSearchValue)
    {
        if ($isSearchValue) {
            //is search string, using LIKE
            $query = " like " . oxDb::getDb()->quote('%' . $value . '%') . " ";
        } else {
            //not search string, values must be equal
            $query = " = " . oxDb::getDb()->quote($value) . " ";
        }

        return $query;
    }

    /**
     * Checks if filter contains wildcards like %
     *
     * @param string $fieldValue filter value
     *
     * @return bool
     */
    protected function _isSearchValue($fieldValue)
    {
        return (getStr()->preg_match('/^%/', $fieldValue) && getStr()->preg_match('/%$/', $fieldValue));
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
    protected function _prepareWhereQuery($whereQuery, $fullQuery)
    {
        if (count($whereQuery)) {
            $myUtilsString = oxRegistry::get("oxUtilsString");
            while (list($identifierName, $fieldValue) = each($whereQuery)) {
                $fieldValue = trim($fieldValue);

                //check if this is search string (contains % sign at beginning and end of string)
                $isSearchValue = $this->_isSearchValue($fieldValue);

                //removing % symbols
                $fieldValue = $this->_processFilter($fieldValue);

                if (strlen($fieldValue)) {
                    $values = explode(' ', $fieldValue);

                    //for each search field using AND action
                    $queryBoolAction = ' and (';

                    foreach ($values as $value) {
                        // trying to search spec chars in search value
                        // if found, add cleaned search value to search sql
                        $uml = $myUtilsString->prepareStrForSearch($value);
                        if ($uml) {
                            $queryBoolAction .= '(';
                        }

                        $quotedIdentifierName = oxDb::getDb()->quoteIdentifier($identifierName);
                        $fullQuery .= " {$queryBoolAction} {$quotedIdentifierName} ";

                        //for search in same field for different values using AND
                        $queryBoolAction = ' and ';

                        $fullQuery .= $this->_buildFilter($value, $isSearchValue);

                        if ($uml) {
                            $fullQuery .= " or {$quotedIdentifierName} ";

                            $fullQuery .= $this->_buildFilter($uml, $isSearchValue);
                            $fullQuery .= ')'; // end of OR section
                        }
                    }

                    // end for AND action
                    $fullQuery .= ' ) ';
                }
            }
        }

        return $fullQuery;
    }

    /**
     * Override this for individual search in admin.
     *
     * @param string $query SQL select to change
     *
     * @return string
     */
    protected function _changeselect($query)
    {
        return $query;
    }

    /**
     * Builds and returns array of SQL WHERE conditions.
     *
     * @return array
     */
    public function buildWhere()
    {
        if ($this->_aWhere === null && ($list = $this->getItemList())) {
            $this->_aWhere = array();
            $filter = $this->getListFilter();
            if (is_array($filter)) {
                $listItem = $this->getItemListBaseObject();
                $languageId = $listItem->isMultilang() ? $listItem->getLanguage() : oxRegistry::getLang()->getBaseLanguage();
                $localDateFormat = $this->getConfig()->getConfigParam('sLocalDateFormat');

                foreach ($filter as $table => $filterData) {
                    foreach ($filterData as $name => $value) {
                        if ($value || '0' === ( string )$value) {
                            $field = "{$table}__{$name}";

                            // if no table name attached to field name, add it
                            $name = $table ? getViewName($table, $languageId) . ".{$name}" : $name;

                            // #M1260: if field is date
                            if ($localDateFormat && $localDateFormat != 'ISO' && isset($listItem->$field)) {
                                $fieldType = $listItem->{$field}->fldtype;
                                if ("datetime" == $fieldType || "date" == $fieldType) {
                                    $value = $this->_convertToDBDate($value, $fieldType);
                                }
                            }

                            $this->_aWhere[$name] = "%{$value}%";
                        }
                    }
                }
            }
        }

        return $this->_aWhere;
    }

    /**
     * Converts date/datetime values to DB scheme (#M1260)
     *
     * @param string $value     Field value
     * @param string $fieldType Field type
     *
     * @return string
     */
    protected function _convertToDBDate($value, $fieldType)
    {
        $convertedObject = new oxField();
        $convertedObject->setValue($value);
        if ($fieldType == "datetime") {
            if (strlen($value) == 10 || strlen($value) == 22 || (strlen($value) == 19 && !stripos($value, "m"))) {
                oxRegistry::get("oxUtilsDate")->convertDBDateTime($convertedObject, true);
            } else {
                if (strlen($value) > 10) {
                    return $this->_convertTime($value);
                } else {
                    return $this->_convertDate($value);
                }
            }
        } elseif ($fieldType == "date") {
            if (strlen($value) == 10) {
                oxRegistry::get("oxUtilsDate")->convertDBDate($convertedObject, true);
            } else {
                return $this->_convertDate($value);
            }
        }

        return $convertedObject->value;
    }

    /**
     * Converter for date field search. If not full date will be searched.
     *
     * @param string $date searched date
     *
     * @return string
     */
    protected function _convertDate($date)
    {
        // regexps to validate input
        $datePatterns = array(
            "/^([0-9]{2})\.([0-9]{4})/" => "EUR2", // MM.YYYY
            "/^([0-9]{2})\.([0-9]{2})/" => "EUR1", // DD.MM
            "/^([0-9]{2})\/([0-9]{4})/" => "USA2", // MM.YYYY
            "/^([0-9]{2})\/([0-9]{2})/" => "USA1" // DD.MM
        );

        // date/time formatting rules
        $dateFormats = array(
            "EUR1" => array(2, 1),
            "EUR2" => array(2, 1),
            "USA1" => array(1, 2),
            "USA2" => array(2, 1)
        );

        // looking for date field
        $dateMatches = array();
        $stringModifier = getStr();
        foreach ($datePatterns as $pattern => $type) {
            if ($stringModifier->preg_match($pattern, $date, $dateMatches)) {
                $date = $dateMatches[$dateFormats[$type][0]] . "-" . $dateMatches[$dateFormats[$type][1]];
                break;
            }
        }

        return $date;
    }

    /**
     * Converter for datetime field search. If not full time will be searched.
     *
     * @param string $fullDate searched date
     *
     * @return string
     */
    protected function _convertTime($fullDate)
    {
        $date = substr($fullDate, 0, 10);
        $convertedObject = new oxField();
        $convertedObject->setValue($date);
        oxRegistry::get("oxUtilsDate")->convertDBDate($convertedObject, true);
        $stringModifier = getStr();

        // looking for time field
        $time = substr($fullDate, 11);
        if ($stringModifier->preg_match("/([0-9]{2}):([0-9]{2}) ([AP]{1}[M]{1})$/", $time, $timeMatches)) {
            if ($timeMatches[3] == "PM") {
                $intVal = (int)$timeMatches[1];
                if ($intVal < 13) {
                    $time = ($intVal + 12) . ":" . $timeMatches[2];
                }
            } else {
                $time = $timeMatches[1] . ":" . $timeMatches[2];
            }
        } elseif ($stringModifier->preg_match("/([0-9]{2}) ([AP]{1}[M]{1})$/", $time, $timeMatches)) {
            if ($timeMatches[2] == "PM") {
                $intVal = (int)$timeMatches[1];
                if ($intVal < 13) {
                    $time = ($intVal + 12);
                }
            } else {
                $time = $timeMatches[1];
            }
        } else {
            $time = str_replace(".", ":", $time);
        }

        return $convertedObject->value . " " . $time;
    }

    /**
     * Set parameters needed for list navigation
     */
    protected function _setListNavigationParams()
    {
        // list navigation
        $showNavigation = false;
        $adminListSize = $this->_getViewListSize();
        if ($this->_iListSize > $adminListSize) {
            // yes, we need to build the navigation object
            $pageNavigation = new stdClass();
            $pageNavigation->pages = round((($this->_iListSize - 1) / $adminListSize) + 0.5, 0);
            $pageNavigation->actpage = ($pageNavigation->actpage > $pageNavigation->pages) ? $pageNavigation->pages : round(
                ($this->_iCurrListPos / $adminListSize) + 0.5,
                0
            );
            $pageNavigation->lastlink = ($pageNavigation->pages - 1) * $adminListSize;
            $pageNavigation->nextlink = null;
            $pageNavigation->backlink = null;

            $position = $this->_iCurrListPos + $adminListSize;
            if ($position < $this->_iListSize) {
                $pageNavigation->nextlink = $position = $this->_iCurrListPos + $adminListSize;
            }

            if (($this->_iCurrListPos - $adminListSize) >= 0) {
                $pageNavigation->backlink = $position = $this->_iCurrListPos - $adminListSize;
            }

            // calculating list start position
            $start = $pageNavigation->actpage - 5;
            $start = ($start <= 0) ? 1 : $start;

            // calculating list end position
            $end = $pageNavigation->actpage + 5;
            $end = ($end < $start + 10) ? $start + 10 : $end;
            $end = ($end > $pageNavigation->pages) ? $pageNavigation->pages : $end;

            // once again adjusting start pos ..
            $start = ($end - 10 > 0) ? $end - 10 : $start;
            $start = ($pageNavigation->pages <= 11) ? 1 : $start;

            // navigation urls
            for ($i = $start; $i <= $end; $i++) {
                $page = new stdclass();
                $page->selected = 0;
                if ($i == $pageNavigation->actpage) {
                    $page->selected = 1;
                }
                $pageNavigation->changePage[$i] = $page;
            }

            $this->_aViewData['pagenavi'] = $pageNavigation;

            if (isset($this->_iOverPos)) {
                $position = $this->_iOverPos;
                $this->_iOverPos = null;
            } else {
                $position = oxRegistry::getConfig()->getRequestParameter('lstrt');
            }

            if (!$position) {
                $position = 0;
            }

            $this->_aViewData['lstrt'] = $position;
            $this->_aViewData['listsize'] = $this->_iListSize;
            $showNavigation = true;
        }

        // determine not used space in List
        $listSizeToShow = $this->_iListSize - $this->_iCurrListPos;
        $adminListSize = $this->_getViewListSize();
        $notUsed = $adminListSize - min($listSizeToShow, $adminListSize);
        $space = $notUsed * 15;

        if (!$showNavigation) {
            $space += 20;
        }

        $this->_aViewData['iListFillsize'] = $space;
    }

    /**
     * Sets-up navigation parameters
     *
     * @param string $node active view id
     */
    protected function _setupNavigation($node)
    {
        // navigation according to class
        if ($node) {
            $adminNavigation = $this->getNavigation();

            $objectId = $this->getEditObjectId();

            if ($objectId == -1) {
                //on first call or when pressed creating new item button, resetting active tab
                $activeTab = $this->_iDefEdit;
            } else {
                // active tab
                $activeTab = oxRegistry::getConfig()->getRequestParameter('actedit');
                $activeTab = $activeTab ? $activeTab : $this->_iDefEdit;
            }

            // tabs
            $this->_aViewData['editnavi'] = $adminNavigation->getTabs($node, $activeTab);

            // active tab
            $this->_aViewData['actlocation'] = $adminNavigation->getActiveTab($node, $activeTab);

            // default tab
            $this->_aViewData['default_edit'] = $adminNavigation->getActiveTab($node, $this->_iDefEdit);

            // assign active tab number
            $this->_aViewData['actedit'] = $activeTab;
        }
    }

    /**
     * Returns items list
     *
     * @return oxList
     */
    public function getItemList()
    {
        if ($this->_oList === null && $this->_sListClass) {
            $this->_oList = oxNew($this->_sListType);
            $this->_oList->clear();
            $this->_oList->init($this->_sListClass);

            $where = $this->buildWhere();

            $listObject = $this->_oList->getBaseObject();

            oxRegistry::getSession()->setVariable('tabelle', $this->_sListClass);
            $this->_aViewData['listTable'] = getViewName($listObject->getCoreTableName());
            $this->getConfig()->setGlobalParameter('ListCoreTable', $listObject->getCoreTableName());

            if ($listObject->isMultilang()) {
                // is the object multilingual?
                /** @var oxI18n $listObject */
                $listObject->setLanguage(oxRegistry::getLang()->getBaseLanguage());

                if (isset($this->_blEmployMultilanguage)) {
                    $listObject->setEnableMultilang($this->_blEmployMultilanguage);
                }
            }

            $query = $this->_buildSelectString($listObject);
            $query = $this->_prepareWhereQuery($where, $query);
            $query = $this->_prepareOrderByQuery($query);
            $query = $this->_changeselect($query);

            // calculates count of list items
            $this->_calcListItemsCount($query);

            // setting current list position (page)
            $this->_setCurrentListPosition(oxRegistry::getConfig()->getRequestParameter('jumppage'));

            // setting addition params for list: current list size
            $this->_oList->setSqlLimit($this->_iCurrListPos, $this->_getViewListSize());

            $this->_oList->selectString($query);
        }

        return $this->_oList;
    }

    /**
     * Clear items list
     */
    public function clearItemList()
    {
        $this->_oList = null;
    }

    /**
     * Returns item list base object
     *
     * @return oxBase|null
     */
    public function getItemListBaseObject()
    {
        $baseObject = null;
        if (($itemsList = $this->getItemList())) {
            $baseObject = $itemsList->getBaseObject();
        }

        return $baseObject;
    }
}
