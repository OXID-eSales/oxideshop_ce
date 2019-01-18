<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Edition;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class is responsible for returning edition directory path.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @deprecated since v6.0.0-rc.2 (2017-08-24); Use \OxidEsales\Facts\Facts instead.
 */
class EditionRootPathProvider
{
    const EDITIONS_DIRECTORY = 'oxid-esales';

    const ENTERPRISE_DIRECTORY = 'oxideshop-ee';

    const PROFESSIONAL_DIRECTORY = 'oxideshop-pe';

    /** @var EditionSelector */
    private $editionSelector;

    /**
     * @param EditionSelector $editionSelector
     */
    public function __construct($editionSelector)
    {
        $this->editionSelector = $editionSelector;
    }

    /**
     * Returns path to edition directory. If no additional editions are found, returns base path.
     *
     * @return string
     */
    public function getDirectoryPath()
    {
        $editionsPath = VENDOR_PATH . static::EDITIONS_DIRECTORY;
        $path = getShopBasePath();
        if ($this->getEditionSelector()->isEnterprise()) {
            $path = $editionsPath  .'/'. static::ENTERPRISE_DIRECTORY;
        } elseif ($this->getEditionSelector()->isProfessional()) {
            $path = $editionsPath .'/'.  static::PROFESSIONAL_DIRECTORY;
        }

        return realpath($path) . DIRECTORY_SEPARATOR;
    }

    /**
     * @return EditionSelector
     */
    protected function getEditionSelector()
    {
        return $this->editionSelector;
    }
}
