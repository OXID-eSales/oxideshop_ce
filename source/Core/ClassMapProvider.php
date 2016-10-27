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

namespace OxidEsales\EshopCommunity\Core;

use OxidEsales\EshopCommunity\Core\Edition\EditionSelector;
use OxidEsales\EshopEnterprise\ClassMap as EnterpriseClassMap;
use OxidEsales\EshopProfessional\ClassMap as ProfessionalClassMap;
use OxidEsales\EshopCommunity\Core\ClassMap as CommunityClassMap;

/**
 * Class responsible for returning class map by edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://oxidforge.org/en/core-oxid-eshop-classes-must-not-be-extended.html
 */
class ClassMapProvider
{
    /** @var array */
    private $notOverridableClassMap = array();

    /** @var array */
    private $overridableClassMap = array();

    /** @var CommunityClassMap */
    private $communityClassMap;

    /** @var ProfessionalClassMap */
    private $professionalClassMap;

    /** @var EnterpriseClassMap */
    private $enterpriseClassMap;

    /**
     * Sets edition selector object.
     *
     * @param EditionSelector $editionSelector
     */
    public function __construct($editionSelector)
    {
        $this->editionSelector = $editionSelector;
    }

    /**
     * @param CommunityClassMap $classMap
     */
    public function setClassMapCommunity($classMap)
    {
        $this->communityClassMap = $classMap;
    }

    /**
     * @param ProfessionalClassMap $classMap
     */
    public function setClassMapProfessional($classMap)
    {
        $this->professionalClassMap = $classMap;
    }

    /**
     * @param EnterpriseClassMap $classMap
     */
    public function setClassMapEnterprise($classMap)
    {
        $this->enterpriseClassMap = $classMap;
    }

    /**
     * Method returns overridable classes class map according edition.
     *
     * @return array
     */
    public function getOverridableClassMap()
    {
        if (empty($this->overridableClassMap)) {
            $this->formClassMaps();
        }

        return $this->overridableClassMap;
    }

    /**
     * Method returns not overridable classes class map according edition.
     *
     * @return array
     */
    public function getNotOverridableClassMap()
    {
        if (empty($this->notOverridableClassMap)) {
            $this->formClassMaps();
        }

        return $this->notOverridableClassMap;
    }

    /**
     * Return a map of concrete classes to virtual namespaced classes depending on the shop edition.
     *
     * @return \OxidEsales\EshopCommunity\VirtualNameSpaceClassMap|
     *         \OxidEsales\EshopEnterprise\VirtualNameSpaceClassMap|
     *         \OxidEsales\EshopProfessional\VirtualNameSpaceClassMap Edition specific class map
     */
    public function getVirtualNamespaceClassMap()
    {

        $editionSelector = $this->getEditionSelector();
        switch ($editionSelector->getEdition()) {
            case EditionSelector::ENTERPRISE:
                $virtualNameSpaceClassMap = new \OxidEsales\EshopEnterprise\Core\VirtualNameSpaceClassMap();
                break;
            case EditionSelector::PROFESSIONAL:
                $virtualNameSpaceClassMap = new \OxidEsales\EshopProfessional\Core\VirtualNameSpaceClassMap();
                break;
            default:
            case EditionSelector::COMMUNITY:
                $virtualNameSpaceClassMap = new \OxidEsales\EshopCommunity\Core\VirtualNameSpaceClassMap();
                break;
        }

        return $virtualNameSpaceClassMap;
    }

    /**
     * Method forms overridable and not overridable class maps and sets them.
     */
    protected function formClassMaps()
    {
        $editionSelector = $this->getEditionSelector();
        $classMapCommunity = $this->getClassMapCommunity();
        $overridableMap = $classMapCommunity->getOverridableMap();
        $notOverridableMap = $classMapCommunity->getNotOverridableMap();

        if ($editionSelector->getEdition() === EditionSelector::ENTERPRISE
            || $editionSelector->getEdition() === EditionSelector::PROFESSIONAL
        ) {
            $classMapProfessional = $this->getClassMapProfessional();
            $overridableMap = array_merge($overridableMap, $classMapProfessional->getOverridableMap());
            $notOverridableMap = array_merge($notOverridableMap, $classMapProfessional->getNotOverridableMap());
        }

        if ($editionSelector->getEdition() === EditionSelector::ENTERPRISE) {
            $classMapEnterprise = $this->getClassMapEnterprise();
            $overridableMap = array_merge($overridableMap, $classMapEnterprise->getOverridableMap());
            $notOverridableMap = array_merge($notOverridableMap, $classMapEnterprise->getNotOverridableMap());
        }

        $this->overridableClassMap = $overridableMap;
        $this->notOverridableClassMap = $notOverridableMap;
    }

    /**
     * Method is responsible for providing class map object.
     *
     * @return CommunityClassMap
     */
    protected function getClassMapCommunity()
    {
        if (is_null($this->communityClassMap)) {
            $this->communityClassMap = new CommunityClassMap();
        }

        return $this->communityClassMap;
    }

    /**
     * Method is responsible for providing class map object.
     *
     * @return ProfessionalClassMap
     */
    protected function getClassMapProfessional()
    {
        if (is_null($this->professionalClassMap)) {
            $this->professionalClassMap = new ProfessionalClassMap();
        }

        return $this->professionalClassMap;
    }

    /**
     * Method is responsible for providing class map object.
     *
     * @return EnterpriseClassMap
     */
    protected function getClassMapEnterprise()
    {
        if (is_null($this->enterpriseClassMap)) {
            $this->enterpriseClassMap = new EnterpriseClassMap();
        }

        return $this->enterpriseClassMap;
    }

    /**
     * Getter for edition selector.
     *
     * @return EditionSelector
     */
    protected function getEditionSelector()
    {
        return $this->editionSelector;
    }
}
