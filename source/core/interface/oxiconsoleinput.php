<?php
/**
 * This file is part of OXID Console.
 *
 * OXID Console is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID Console is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID Console.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @author    OXID Professional services
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2014
 */

/**
 * Input interface is implemented by all console input classes
 */
interface oxIConsoleInput
{

    /**
     * Get first argument
     *
     * @return string|null
     */
    public function getFirstArgument();

    /**
     * Get all options
     *
     * @return array
     */
    public function getOptions();

    /**
     * Returns all set arguments
     *
     * @return array
     */
    public function getArguments();

    /**
     * @param array|string $mOption In array it returns first found option
     *
     * @return mixed|null
     */
    public function getOption($mOption);

    /**
     * Has option
     *
     * @param array|string $mOption
     *
     * @return bool
     */
    public function hasOption($mOption);

    /**
     * Get argument at given offset
     *
     * @param integer $iOffset starts at 0
     *
     * @return mixed|null
     */
    public function getArgument($iOffset);

    /**
     * Prompt user for an input
     *
     * @param string $sTitle
     *
     * @return string
     */
    public function prompt($sTitle = null);
}