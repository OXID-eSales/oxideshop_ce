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

namespace OxidEsales\EshopCommunity\Core\Edition;

/**
 * Class is responsible for returning directories paths according edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class EditionPathProvider
{
    const SETUP_DIRECTORY = 'Setup';

    const DATABASE_SQL_DIRECTORY = 'Sql';

    /** @var EditionRootPathProvider */
    private $editionRootPathProvider;

    /**
     * @param EditionRootPathProvider $editionRootPathProvider
     */
    public function __construct($editionRootPathProvider)
    {
        $this->editionRootPathProvider = $editionRootPathProvider;
    }

    /**
     * Method forms path to corresponding edition setup directory.
     *
     * @return string
     */
    public function getSetupDirectory()
    {
        return $this->getEditionRootPathProvider()->getDirectoryPath()
        . static::SETUP_DIRECTORY . DIRECTORY_SEPARATOR;
    }

    /**
     * Method forms path to corresponding edition database sql files directory.
     *
     * @return string
     */
    public function getDatabaseSqlDirectory()
    {
        return $this->getSetupDirectory() . static::DATABASE_SQL_DIRECTORY . DIRECTORY_SEPARATOR;
    }

    /**
     * Method forms path to corresponding edition views directory.
     *
     * @return string
     */
    public function getViewsDirectory()
    {
        return $this->getEditionRootPathProvider()->getDirectoryPath()
        . 'Application' . DIRECTORY_SEPARATOR
        . 'views' . DIRECTORY_SEPARATOR;
    }

    /**
     * Method forms path to corresponding smarty plugins directory.
     *
     * @return string
     */
    public function getSmartyPluginsDirectory()
    {
        return $this->getEditionRootPathProvider()->getDirectoryPath() . 'Core/Smarty/Plugin/';
    }

    /**
     * @return EditionRootPathProvider
     */
    protected function getEditionRootPathProvider()
    {
        return $this->editionRootPathProvider;
    }
}
