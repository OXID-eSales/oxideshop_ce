<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Edition;

/**
 * Class is responsible for returning directories paths according edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @deprecated since v6.0.0-rc.2 (2017-08-24); Use \OxidEsales\Facts\Facts instead.
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
