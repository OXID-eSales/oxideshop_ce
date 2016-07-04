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
 * @link          http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version       OXID eShop CE
 */
namespace OxidEsales\Eshop\Core\Database\Adapter;

/**
 * The doctrine statement wrapper, to support the old adodblite interface.
 * The empty result set is here, cause it was in adodb lite. We will remove it later on.
 *
 * @package OxidEsales\Eshop\Core\Database
 */
class DoctrineEmptyResultSet implements \IteratorAggregate, ResultSetInterface
{

    /**
     * @var bool Did we reach the end of the results?
     */
    public $EOF = true;

    /**
     * @var array Holds the retrieved fields of the resultSet row on the current cursor position
     */
    public $fields = array();

    /**
     * @inheritdoc
     */
    public function fetchAll()
    {
        throw new \LogicException('You cannot call this method on a empty result set');
    }

    /**
     * @inheritdoc
     */
    public function fetchRow()
    {
        throw new \LogicException('You cannot call this method on a empty result set');
    }

    /**
     * @inheritdoc
     */
    function Close()
    {
        throw new \LogicException('You cannot call this method on a empty result set');
    }

    /**
     * @inheritdoc
     */
    public function getIterator(){
        throw new \LogicException('You cannot call this method on a empty result set');
    }

    /**
     * @inheritdoc
     */
    function FieldCount()
    {
        throw new \LogicException('You cannot call this method on a empty result set');
    }

    /**
     * @inheritdoc
     */
    public function count()
    {
        throw new \LogicException('You cannot call this method on a empty result set');
    }
   
}
