<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use \DOMDocument;

class UtilsXmlTest extends \OxidTestCase
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
        $oUtilsXml = oxNew('oxUtilsXml');
        $this->assertEquals($blResult, $oUtilsXml->loadXml($sXml) != false);
    }

    /**
     * Check for valid response when passing normal DOM document
     */
    public function testLoadXmlWithDomDocumentInvalidXml()
    {
        $oUtilsXml = oxNew('oxUtilsXml');
        $oDom = new DOMDocument();
        $sInValidXml = '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><message>ACK</message>';
        $this->assertEquals(false, $oUtilsXml->loadXml($sInValidXml, $oDom) != false);
    }

    /**
     * Check for invalid response when passing normal DOM document
     */
    public function testLoadXmlWithDomDocumentValidXml()
    {
        $oUtilsXml = oxNew('oxUtilsXml');
        $oDom = new DOMDocument();
        $sValidXml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><ocl><message>ACK</message></ocl>";
        $this->assertEquals(true, $oUtilsXml->loadXml($sValidXml, $oDom) != false);
    }
}
