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

namespace OxidEsales\Core;

use OxidEsales\EshopEnterprise\ClassMap as EnterpriseClassMap;
use OxidEsales\Professional\ClassMap as ProfessionalClassMap;

/**
 * Class responsible for returning class map by edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
 */
class ClassMapProvider
{
    /** @var array */
    private $notOverridableClassMap = array();

    /** @var array */
    private $overridableClassMap = array();

    /** @var EnterpriseClassMap */
    private $enterpriseClassMap;

    /** @var ProfessionalClassMap */
    private $professionalClassMap;

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
     * Method forms overridable and not overridable class maps and sets them.
     */
    protected function formClassMaps()
    {
        $overridableMap = array();
        $notOverridableMap = array();
        $editionSelector = $this->getEditionSelector();
        if ($editionSelector->getEdition() === EditionSelector::ENTERPRISE
            || $editionSelector->getEdition() === EditionSelector::PROFESSIONAL
        ) {
            $classMapProfessional = $this->getClassMapProfessional();
            $overridableMap = $classMapProfessional->getOverridableMap();
            $notOverridableMap = $classMapProfessional->getNotOverridableMap();
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
