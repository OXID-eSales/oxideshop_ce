<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Model;

/**
 * List manager.
 * Collects list data (eg. from DB), performs list changes updating (to DB), etc.
 */
class ListModel extends \OxidEsales\Eshop\Core\Base implements \ArrayAccess, \Iterator, \Countable
{
    /**
     * Array of objects (some object list).
     *
     * @var array $_aArray
     */
    protected $_aArray = [];

    /**
     * Save the state, that active element was unset
     * needed for proper foreach iterator functionality
     *
     * @var bool $_blRemovedActive
     */
    protected $_blRemovedActive = false;

    /**
     * Template object used for some methods before the list is built.
     *
     * @var BaseModel
     */
    private $_oBaseObject = null;

    /**
     * Flag if array is ok or not
     *
     * @var boolean $_blValid
     */
    private $_blValid = true;

    /**
     * -----------------------------------------------------------------------------------------------------
     *
     * Implementation of SPL Array classes functions follows here
     *
     * -----------------------------------------------------------------------------------------------------
     */

    /**
     * implementation of abstract classes for ArrayAccess follow
     */
    /**
     * offsetExists for SPL
     *
     * @param mixed $offset SPL array offset
     *
     * @return boolean
     */
    public function offsetExists($offset)
    {
        return isset($this->_aArray[$offset]);
    }

    /**
     * offsetGet for SPL
     *
     * @param mixed $offset SPL array offset
     *
     * @return BaseModel
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->_aArray[$offset];
        }

        return false;
    }

    /**
     * offsetSet for SPL
     *
     * @param mixed     $offset SPL array offset
     * @param BaseModel $oBase  Array element
     */
    public function offsetSet($offset, $oBase)
    {
        if (isset($offset)) {
            $this->_aArray[$offset] = & $oBase;
        } else {
            $sLongFieldName = $this->_getFieldLongName('oxid');
            if (isset($oBase->$sLongFieldName->value)) {
                $sOxid = $oBase->$sLongFieldName->value;
                $this->_aArray[$sOxid] = & $oBase;
            } else {
                $this->_aArray[] = & $oBase;
            }
        }
    }

    /**
     * offsetUnset for SPL
     *
     * @param mixed $offset SPL array offset
     */
    public function offsetUnset($offset)
    {
        if (strcmp($offset, $this->key()) === 0) {
            // #0002184: active element removed, next element will be prev / first
            $this->_blRemovedActive = true;
        }

        unset($this->_aArray[$offset]);
    }

    /**
     * Returns SPL array keys
     *
     * @return array
     */
    public function arrayKeys()
    {
        return array_keys($this->_aArray);
    }

    /**
     * rewind for SPL
     */
    public function rewind()
    {
        $this->_blRemovedActive = false;
        $this->_blValid = (false !== reset($this->_aArray));
    }

    /**
     * current for SPL
     *
     * @return null;
     */
    public function current()
    {
        return current($this->_aArray);
    }

    /**
     * key for SPL
     *
     * @return mixed
     */
    public function key()
    {
        return key($this->_aArray);
    }

    /**
     * previous / first array element
     *
     * @return mixed
     */
    public function prev()
    {
        $oVar = prev($this->_aArray);
        if ($oVar === false) {
            // the first element, reset pointer
            $oVar = reset($this->_aArray);
        }
        $this->_blRemovedActive = false;

        return $oVar;
    }

    /**
     * next for SPL
     */
    public function next()
    {
        if ($this->_blRemovedActive === true && current($this->_aArray)) {
            $oVar = $this->prev();
        } else {
            $oVar = next($this->_aArray);
        }

        $this->_blValid = (false !== $oVar);
    }

    /**
     * valid for SPL
     *
     * @return boolean
     */
    public function valid()
    {
        return $this->_blValid;
    }

    /**
     * count for SPL
     *
     * @return integer
     */
    public function count()
    {
        return count($this->_aArray);
    }

    /**
     * clears/destroys list contents
     */
    public function clear()
    {
        /*
        foreach ( $this->_aArray as $key => $sValue) {
            unset( $this->_aArray[$key]);
        }
        reset( $this->_aArray);*/
        $this->_aArray = [];
    }

    /**
     * copies a given array over the objects internal array (something like old $myList->aList = $aArray)
     *
     * @param array $aArray array of list items
     */
    public function assign($aArray)
    {
        $this->_aArray = $aArray;
    }

    /**
     * returns the array reversed, the internal array remains untouched
     *
     * @return array
     */
    public function reverse()
    {
        return array_reverse($this->_aArray);
    }

    /**
     * -----------------------------------------------------------------------------------------------------
     * SPL implementation end
     * -----------------------------------------------------------------------------------------------------
     */

    /**
     * List Object class name
     *
     * @var string
     */
    protected $_sObjectsInListName = 'oxBase';

    /**
     * Core table name
     *
     * @var string
     */
    protected $_sCoreTable = null;

    /**
     * @var string ShopID
     */
    protected $_sShopID = null;

    /**
     * @var array SQL Limit, 0 => Start, 1 => Records
     */
    protected $_aSqlLimit = [];

    /**
     * Class Constructor
     *
     * @param string $sObjectName Associated list item object type
     */
    public function __construct($sObjectName = null)
    {
        $myConfig = $this->getConfig();
        $this->_aSqlLimit[0] = 0;
        $this->_aSqlLimit[1] = 0;
        $this->_sShopID = $myConfig->getShopId();

        if ($sObjectName) {
            $this->init($sObjectName);
        }
    }

    /**
     * Backward compatibility method
     *
     * @param string $sName Variable name
     *
     * @return mixed
     */
    public function __get($sName)
    {
        if ($sName == 'aList') {
            return $this->_aArray;
        }
    }

    /**
     * Returns list items array
     *
     * @return array
     */
    public function getArray()
    {
        return $this->_aArray;
    }

    /**
     * Inits list table name and object name.
     *
     * @param string $sObjectName List item object type
     * @param string $sCoreTable  Db table name this list s selected from
     */
    public function init($sObjectName, $sCoreTable = null)
    {
        $this->_sObjectsInListName = $sObjectName;
        if ($sCoreTable) {
            $this->_sCoreTable = $sCoreTable;
        }
    }

    /**
     * Initializes or returns existing list template object.
     *
     * @return BaseModel
     */
    public function getBaseObject()
    {
        if (!$this->_oBaseObject) {
            $this->_oBaseObject = oxNew($this->_sObjectsInListName);
            $this->_oBaseObject->setInList();
            $this->_oBaseObject->init($this->_sCoreTable);
        }

        return $this->_oBaseObject;
    }

    /**
     * Sets base object for list.
     *
     * @param object $oObject Base object
     */
    public function setBaseObject($oObject)
    {
        $this->_oBaseObject = $oObject;
    }

    /**
     * Convert results of the given select statement into objects and store them in _aArray.
     *
     * Developers are encouraged to use prepared statements like this:
     * $sql = 'SELECT * FROM `mytable` WHERE oxid = ?';
     * $parameters = ['MYOXID']
     * selectString($sql, $parameters)
     *
     * @param string $sql        SQL select statement or prepared statement
     * @param array  $parameters Parameters to be used in a prepared statement
     */
    public function selectString($sql, array $parameters = [])
    {
        $this->clear();

        $oDb = \OxidEsales\Eshop\Core\DatabaseProvider::getDb(\OxidEsales\Eshop\Core\DatabaseProvider::FETCH_MODE_ASSOC);
        if ($this->_aSqlLimit[0] || $this->_aSqlLimit[1]) {
            $rs = $oDb->selectLimit($sql, $this->_aSqlLimit[1], max(0, $this->_aSqlLimit[0]), $parameters);
        } else {
            $rs = $oDb->select($sql, $parameters);
        }

        if ($rs != false && $rs->count() > 0) {
            $oSaved = clone $this->getBaseObject();

            while (!$rs->EOF) {
                $oListObject = clone $oSaved;

                $this->_assignElement($oListObject, $rs->fields);

                $this->add($oListObject);

                $rs->fetchRow();
            }
        }
    }

    /**
     * Add an entry to object array.
     *
     * @param object $oObject Object to be added.
     */
    public function add($oObject)
    {
        if ($oObject->getId()) {
            $this->_aArray[$oObject->getId()] = $oObject;
        } else {
            $this->_aArray[] = $oObject;
        }
    }

    /**
     * Assign data from array to list
     *
     * @param array $aData data for list
     */
    public function assignArray($aData)
    {
        $this->clear();
        if (count($aData)) {
            $oSaved = clone $this->getBaseObject();

            foreach ($aData as $aItem) {
                $oListObject = clone $oSaved;
                $this->_assignElement($oListObject, $aItem);
                if ($oListObject->getId()) {
                    $this->_aArray[$oListObject->getId()] = $oListObject;
                } else {
                    $this->_aArray[] = $oListObject;
                }
            }
        }
    }


    /**
     * Sets SQL Limit
     *
     * @param integer $iStart   Start e.g. limit Start,xxxx
     * @param integer $iRecords Nr of Records e.g. limit xxx,Records
     */
    public function setSqlLimit($iStart, $iRecords)
    {
        $this->_aSqlLimit[0] = $iStart;
        $this->_aSqlLimit[1] = $iRecords;
    }

    /**
     * Function checks if there is at least one object in the list which has the given value in the given field
     *
     * @param mixed  $oVal       The searched value
     * @param string $sFieldName The name of the field, give "oxid" will access the classname__oxid field
     *
     * @return boolean
     */
    public function containsFieldValue($oVal, $sFieldName)
    {
        $sFieldName = $this->_getFieldLongName($sFieldName);
        foreach ($this->_aArray as $obj) {
            if ($obj->{$sFieldName}->value == $oVal) {
                return true;
            }
        }

        return false;
    }

    /**
     * Generic function for loading the list
     *
     * @return null;
     */
    public function getList()
    {
        $oListObject = $this->getBaseObject();
        $sFieldList = $oListObject->getSelectFields();
        $sQ = "select $sFieldList from " . $oListObject->getViewName();
        if ($sActiveSnippet = $oListObject->getSqlActiveSnippet()) {
            $sQ .= " where $sActiveSnippet ";
        }
        $this->selectString($sQ);

        return $this;
    }

    /**
     * Executes assign() method on list object. This method is called in loop in oxList::selectString().
     * It is if you want to execute any functionality on every list ELEMENT after it is fully loaded (assigned).
     *
     * @param BaseModel $oListObject List object (the one derived from BaseModel)
     * @param array     $aDbFields   An array holding db field values (normally the result of \OxidEsales\Eshop\Core\DatabaseProvider::Execute())
     */
    protected function _assignElement($oListObject, $aDbFields)
    {
        $oListObject->assign($aDbFields);
    }

    /**
     * Returns field long name
     *
     * @param string $sFieldName Field name
     *
     * @return string
     */
    protected function _getFieldLongName($sFieldName)
    {
        if ($this->_sCoreTable) {
            return $this->_sCoreTable . '__' . $sFieldName;
        }

        return $sFieldName;
    }
}
