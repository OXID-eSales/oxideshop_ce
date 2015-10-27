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

namespace OxidEsales\Eshop\Core\GenericImport\ImportObject;

use Exception;
use oxArticle;
use oxBase;
use oxField;
use oxUtilsObject;
use OxidEsales\Eshop\Core\GenericImport\GenericImport;

$sArticleClass = oxUtilsObject::getInstance()->getClassName('oxArticle');
class_alias($sArticleClass, 'oxArticle_parent');

/**
 * Article class extension, used for import.
 * Disables variants loading functionality.
 * Adds hot fix for article long description saving.
 *
 * @mixin oxArticle
 */
class ArticleExtension extends \oxArticle_parent
{
    /**
     * Disable variant loading
     *
     * @var bool
     */
    protected $_blLoadVariants = false;

    /**
     * Sets article parameter
     *
     * @param string $sName  name of parameter to set
     * @param mixed  $sValue parameter value
     *
     * @return null
     */
    public function __set($sName, $sValue)
    {
        if (strpos($sName, 'oxarticles__oxlongdesc') === 0) {
            if ($this->_blEmployMultilanguage) {
                return parent::__set($sName, $sValue);
            }
            $this->$sName = $sValue;
        } else {
            parent::__set($sName, $sValue);
        }
    }

    /**
     * Inserts article long description to artextends table
     */
    protected function _saveArtLongDesc()
    {
        if ($this->_blEmployMultilanguage) {
            return parent::_saveArtLongDesc();
        }


        $artExtends = oxNew('oxi18n');
        $artExtends->setEnableMultilang(false);
        $artExtends->init('oxartextends');

        $artExtendsFields = $artExtends->_getAllFields(true);
        if (!$artExtends->load($this->getId())) {
            $artExtends->setId($this->getId());
        }

        foreach ($artExtendsFields as $key => $value) {
            if (preg_match('/^oxlongdesc(_(\d{1,2}))?$/', $key)) {
                $fieldName = $this->_getFieldLongName($key);
                if (isset($this->$fieldName)) {
                    $longDesc = null;
                    if ($this->$fieldName instanceof oxField) {
                        $longDesc = $this->$fieldName->getRawValue();
                    } elseif (is_object($this->$fieldName)) {
                        $longDesc = $this->$fieldName->value;
                    }
                    if (isset($longDesc)) {
                        $sAEField = $artExtends->_getFieldLongName($key);
                        $artExtends->$sAEField = new oxField($longDesc, oxField::T_RAW);
                    }
                }
            }
        }

        $artExtends->save();
    }
}

/**
 * Import object for Articles.
 */
class Article extends ImportObject
{
    /** @var string Database table name. */
    protected $tableName = 'oxarticles';

    /** @var string Shop object name. */
    protected $shopObjectName = '\OxidEsales\Eshop\Core\GenericImport\ImportObject\ArticleExtension';

    /**
     * Imports article. Returns import status
     *
     * @param array $data db row array
     *
     * @return string $oxid on success, bool FALSE on failure
     */
    public function import($data)
    {
        if (isset($data['OXID'])) {
            $this->checkIdField($data['OXID']);
        }

        return parent::import($data);
    }

    /**
     * Issued before saving an object.
     * Can modify $data array before saving.
     * Set default value of OXSTOCKFLAG to 1 according to eShop admin functionality.
     *
     * @param oxBase $shopObject        shop object
     * @param array  $data              data to prepare
     * @param bool   $allowCustomShopId if allow custom shop id
     *
     * @return array
     */
    protected function preAssignObject($shopObject, $data, $allowCustomShopId)
    {
        if (!isset($data['OXSTOCKFLAG'])) {
            if (!$data['OXID'] || !$shopObject->exists($data['OXID'])) {
                $data['OXSTOCKFLAG'] = 1;
            }
        }

        $data = parent::preAssignObject($shopObject, $data, $allowCustomShopId);

        return $data;
    }

    /**
     * Post saving hook. can finish transactions if needed or ajust related data
     *
     * @param oxArticle $shopObject shop object
     * @param array     $data       data to save
     *
     * @return mixed data to return
     */
    protected function postSaveObject($shopObject, $data)
    {
        $articleId = $shopObject->getId();
        $shopObject->onChange(null, $articleId, $articleId);

        return $articleId;
    }

    /**
     * Basic access check for writing data. For oxArticle we allow super admin to change
     * subshop oxArticle fields described in config option aMultishopArticleFields.
     *
     * @param oxArticle $shopObject Loaded shop object
     * @param array     $data       Fields to be written, null for default
     *
     * @throws Exception on now access
     *
     * @return null
     */
    public function checkWriteAccess($shopObject, $data = null)
    {
        return;

    }
}
