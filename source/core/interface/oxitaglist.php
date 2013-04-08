<?php
/**
 *    This file is part of OXID eShop Community Edition.
 *
 *    OXID eShop Community Edition is free software: you can redistribute it and/or modify
 *    it under the terms of the GNU General Public License as published by
 *    the Free Software Foundation, either version 3 of the License, or
 *    (at your option) any later version.
 *
 *    OXID eShop Community Edition is distributed in the hope that it will be useful,
 *    but WITHOUT ANY WARRANTY; without even the implied warranty of
 *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *    GNU General Public License for more details.
 *
 *    You should have received a copy of the GNU General Public License
 *    along with OXID eShop Community Edition.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @package   core
 * @copyright (C) OXID eSales AG 2003-2013
 * @version OXID eShop CE
 * @version   SVN: $Id: oxbasketitem.php 16608 2009-02-19 13:31:39Z vilma $
 */

/**
 * oxTagCloud set interface
 *
 * @package core
 */
interface oxITagList
{
    /**
     * Returns cache id, on which tagcloud should cache content.
     * If null is returned, content will not be cached.
     *
     * @return string
     */
    public function getCacheId();

    /**
     * Loads tagcloud set
     *
     * @return boolean
     */
    public function loadList();

    /**
     * Returns tagcloud set
     *
     * @return oxTagSet
     */
    public function get();
}
