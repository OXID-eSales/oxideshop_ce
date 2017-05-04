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
 * @copyright (C) OXID eSales AG 2003-2017
 * @version   OXID eShop CE
 */

class Unit_Core_oxUtilsXmlTest extends OxidTestCase
{

    public function xmlProviderNoDomDocument()
    {
        return array(
            array('<?xml version="1.0" encoding="utf-8"?><message>ACK</message>', true),
            array('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><message>ACK</message>', false),
        );
    }

    public function xmlProviderWithDomDocument()
    {
        $oDom = new DOMDocument();

        return array(
            array('<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html>ACK</html>', $oDom, false),
            array('<?xml version="1.0" encoding="utf-8"?><message>ACK</message>', $oDom, true),
        );
    }

    /**
     * Check if loadXml returns valid XML or response
     *
     * @dataProvider xmlProviderNoDomDocument
     */
    public function testLoadXmlNoDocument($sXml, $blResult)
    {
        $oUtilsXml = new oxUtilsXml();
        $this->assertEquals($blResult, $oUtilsXml->loadXml($sXml) != false);
    }

    /**
     * Check for valid response when passing normal DOM document
     */
    public function testLoadXmlWithDomDocumentInvalidXml()
    {
        $oUtilsXml = new oxUtilsXml();
        $oDom = new DOMDocument();
        $sInValidXml = '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><message>ACK</message>';
        $this->assertEquals(false, $oUtilsXml->loadXml($sInValidXml, $oDom) != false);
    }

    /**
     * Check for invalid response when passing normal DOM document
     */
    public function testLoadXmlWithDomDocumentValidXml()
    {
        $oUtilsXml = new oxUtilsXml();
        $oDom = new DOMDocument();
        $sValidXml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><ocl><message>ACK</message></ocl>";
        $this->assertEquals(true, $oUtilsXml->loadXml($sValidXml, $oDom) != false);
    }
}