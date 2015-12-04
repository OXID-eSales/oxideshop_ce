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

namespace OxidEsales\Eshop\Core\Edition;

/**
 * Class is responsible for returning edition.
 *
 * @internal Do not make a module extension for this class.
 * @see      http://wiki.oxidforge.org/Tutorials/Core_OXID_eShop_classes:_must_not_be_extended
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
        $this->edition = $edition;
    }

    /**
     * Method returns edition.
     *
     * @return string
     */
    public function getEdition()
    {
        if (!is_null($this->edition)) {
            return $this->edition;
        }

        $edition = static::COMMUNITY;

        if (OXID_VERSION_EE) {
            $edition = static::ENTERPRISE;
        }
        if (OXID_VERSION_PE_PE) {
            $edition = static::PROFESSIONAL;
        }

        return $edition;
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
}
