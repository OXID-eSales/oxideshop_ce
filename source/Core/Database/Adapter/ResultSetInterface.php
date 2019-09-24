<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Database\Adapter;

/**
 * Interface ResultSetInterface
 *
 * @deprecated since v6.5.0 (2019-09-24); Use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface
 */
interface ResultSetInterface extends \Traversable, \Countable
{

    /**
     * Closes the cursor, enabling the statement to be executed again.
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function close();

    /**
     * Returns an array containing all of the result set rows
     *
     * @return array
     */
    public function fetchAll();

    /**
     * Returns the next row from a result set.
     *
     * @return mixed The return value of this function on success depends on the fetch type.
     *               In all cases, FALSE is returned on failure.
     */
    public function fetchRow();

    /**
     * Returns the number of columns in the result set
     *
     * @return integer Returns the number of columns in the result set represented by the PDOStatement object.
     */
    public function fieldCount();
}
