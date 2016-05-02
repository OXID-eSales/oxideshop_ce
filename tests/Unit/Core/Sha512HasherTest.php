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
namespace Unit\Core;

class Sha512HasherTest extends \OxidTestCase
{

    public function testEncrypt()
    {
        $sHash = 'b32e441399b4601e11846563bea5c6597b7fbeeb8d443a05cdaf0c5615f6bd9c168eac63856945c2b188f933db330f8202bbd4a2a4abadef0ed96f6247970622';
        $oHasher = oxNew('oxSha512Hasher');

        $this->assertSame($sHash, $oHasher->hash('somestring05853e9aba10b9c25a3b8af5618ec9fa'));
    }
} 