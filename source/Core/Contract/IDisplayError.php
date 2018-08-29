<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Contract;

/**
 * DisplayError interface
 *
 */
interface IDisplayError
{

    /**
     * This method should return a localized message for displaying
     *
     * @return string A string to display to the user
     */
    public function getOxMessage();

    /**
     * Returns a type of the error, e.g. the class of the exception or whatever class
     * implemented this interface
     *
     * @return string The error type
     */
    public function getErrorClassType();

    /**
     * Possibility to access additional values
     *
     * @param string $sName Value name
     *
     * @return string An additional value (string) by its name
     */
    public function getValue($sName);
}
