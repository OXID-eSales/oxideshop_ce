<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * Interface for selection list based objects
 *
 */
interface ISelectList
{

    /**
     * Returns selection list label
     *
     * @return string
     */
    public function getLabel();

    /**
     * Returns array of oxSelection's
     *
     * @return array
     */
    public function getSelections();

    /**
     * Returns active selection object
     *
     * @return oxSelection
     */
    public function getActiveSelection();
}
