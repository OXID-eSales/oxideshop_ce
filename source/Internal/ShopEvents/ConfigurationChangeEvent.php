<?php
declare(strict_types=1);

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Internal\ShopEvents;

use Symfony\Component\EventDispatcher\Event;

/**
 * Class ConfigurationChangeEvent
 *
 * @package OxidEsales\EshopCommunity\Internal\ShopEvents
 */
class ConfigurationChangeEvent extends Event
{
    const NAME = self::class;

    /**
     * Configuration variable that was changed.
     *
     * @var string
     */
    protected $configurationVariable = null;

    /**
     * Variable change affects all pages yes/no.
     *
     * @var bool
     */
    protected $affectsAllPages = false;

    /**
     * Variable change affects start page yes/no.
     *
     * @var bool
     */
    protected $affectsStartPage = false;

    /**
     * Variable change affects all details pages yes/no.
     *
     * @var bool
     */
    protected $affectsAllDetails = false;

    /**
     * Variable change affects all lists yes/no.
     *
     * @var bool
     */
    protected $affectsAllLists = false;

    /**
     * Setter for configuration variable name.
     *
     * @param string $configurationVariable Config varname.
     */
    public function setConfigurationVariable($configurationVariable)
    {
        $this->configurationVariable = $configurationVariable;
    }

    /**
     * Getter for configuration variable name.
     *
     * @return bool
     */
    public function getConfigurationVariable()
    {
        return $this->configurationVariable;
    }

    /**
     * Setter for affectsAllPages flag.
     *
     * @param bool $affectsAllPages Flag
     */
    public function setAffectsAllPages($affectsAllPages)
    {
        $this->affectsAllPages = (bool) $affectsAllPages;
    }

    /**
     * Setter for affectsAllDetails flag.
     *
     * @param bool $affectsAllDetails Flag
     */
    public function setAffectsAllDetails($affectsAllDetails)
    {
        $this->affectsAllDetails = (bool) $affectsAllDetails;
    }

    /**
     * Setter for affectsAllLists flag.
     *
     * @param bool $affectsAllLists Flag
     */
    public function setAffectsAllLists($affectsAllLists)
    {
        $this->affectsAllLists = (bool) $affectsAllLists;
    }

    /**
     * Setter for affectsStartPage flag.
     *
     * @param bool $affectsStartPage Flag
     */
    public function setAffectsStartPage($affectsStartPage)
    {
        $this->affectsStartPage = (bool) $affectsStartPage;
    }

    /**
     * Getter for affectsAllPAges flag.
     *
     * @return bool
     */
    public function affectsAllPages()
    {
        return $this->affectsAllPages;
    }

    /**
     * Getter for affectsAllDetails flag.
     *
     * @return bool
     */
    public function affectsAllDetails()
    {
        return $this->affectsAllDetails;
    }

    /**
     * Getter for affectsAllLists flag.
     *
     * @return bool
     */
    public function affectsAllLists()
    {
        return $this->affectsAllLists;
    }

    /**
     * Getter for affectsStartPage flag.
     *
     * @return bool
     */
    public function affectsStartPage()
    {
        return $this->affectsStartPage;
    }
}
