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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */


/**
 * The interface methods should be implemented by classes which need a configuration object
 * (usually OxConfig) manually set.
 */
interface OxIConfigurable
{
    /**
     * Sets configuration object
     *
     * @param OxConfig $oConfig Configraution object
     *
     * @abstract
     *
     * @return mixed
     */
    public function setConfig(OxConfig $oConfig);

    /**
     * Returns active configuration object
     *
     * @abstract
     *
     * @return OxConfig
     */
    public function getConfig();
}