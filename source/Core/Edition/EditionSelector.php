<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Core\Edition;

use OxidEsales\Eshop\Core\ConfigFile;
use OxidEsales\Eshop\Core\Registry;

/**
 * Class is responsible for returning edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      https://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 *
 * @deprecated since v6.0.0-rc.2 (2017-08-24); Use \OxidEsales\Facts\Facts instead.
 */
class EditionSelector
{
    const ENTERPRISE = 'EE';

    const PROFESSIONAL = 'PE';

    const COMMUNITY = 'CE';

    /** @var string Edition abbreviation  */
    private $edition = null;

    /**
     * EditionSelector constructor.
     *
     * @param string|null $edition to force edition.
     */
    public function __construct($edition = null)
    {
        $this->edition = $edition ?: $this->findEdition();
    }

    /**
     * Method returns edition.
     *
     * @return string
     */
    public function getEdition()
    {
        return $this->edition;
    }

    /**
     * @return bool
     */
    public function isEnterprise()
    {
        return $this->getEdition() === static::ENTERPRISE;
    }

    /**
     * @return bool
     */
    public function isProfessional()
    {
        return $this->getEdition() === static::PROFESSIONAL;
    }

    /**
     * @return bool
     */
    public function isCommunity()
    {
        return $this->getEdition() === static::COMMUNITY;
    }

    /**
     * Check for forced edition in config file. If edition is not specified,
     * determine it by ClassMap existence.
     *
     * @return string
     */
    protected function findEdition()
    {
        if (!class_exists('OxidEsales\EshopCommunity\Core\Registry') || !Registry::instanceExists('oxConfigFile')) {
            $configFile = new ConfigFile(OX_BASE_PATH . DIRECTORY_SEPARATOR . "config.inc.php");
        }
        $configFile = isset($configFile) ? $configFile : Registry::get(\OxidEsales\Eshop\Core\ConfigFile::class);
        $edition = $configFile->getVar('edition') ?: $this->getEditionByExistingClasses();
        $configFile->setVar('edition', $edition);

        return strtoupper($edition);
    }

    /**
     * Determine shop edition by existence of edition specific classes.
     *
     * @return string
     */
    protected function getEditionByExistingClasses()
    {
        return static::COMMUNITY;
    }
}
