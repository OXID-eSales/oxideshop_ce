<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 *
 * @link      http://www.oxid-esales.com
 * @package   tests
 * @copyright (c) OXID eSales AG 2003-#OXID_VERSION_YEAR#
 * @version   SVN: $Id: oxutilsxmlTest.php 56456 13.8.7 13.26Z tadas.rimkus $
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxUtilsXmlTest extends OxidTestCase
{

    public function xmlProviderNoDomDocument()
    {
        return array(
            array( '<?xml version="1.0" encoding="utf-8"?><message>ACK</message>', true ),
            array( '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><message>ACK</message>', false ),
        );
    }

    public function xmlProviderWithDomDocument()
    {
        $oDom = new DOMDocument();
        return array(
            array( '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><html>ACK</html>', $oDom, false ),
            array( '<?xml version="1.0" encoding="utf-8"?><message>ACK</message>', $oDom, true ),
        );
    }
    /**
     * Check if loadXml returns valid XML or response
     *
     * @dataProvider xmlProviderNoDomDocument
     */
    public function testLoadXmlNoDocument( $sXml, $blResult )
    {
        $oUtilsXml = new oxUtilsXml();
        $this->assertEquals( $blResult, $oUtilsXml->loadXml( $sXml ) != false );
    }

    /**
     * Check for valid response when passing normal DOM document
     */
    public function testLoadXmlWithDomDocumentInvalidXml(  )
    {
        $oUtilsXml = new oxUtilsXml();
        $oDom = new DOMDocument();
        $sInValidXml = '<!DOCTYPE HTML PUBLIC "-//IETF//DTD HTML 2.0//EN"><message>ACK</message>';
        $this->assertEquals( false, $oUtilsXml->loadXml( $sInValidXml, $oDom ) != false );
    }

    /**
     * Check for invalid response when passing normal DOM document
     */
    public function testLoadXmlWithDomDocumentValidXml()
    {
        $oUtilsXml = new oxUtilsXml();
        $oDom = new DOMDocument();
        $sValidXml = "<?xml version=\"1.0\" encoding=\"utf-8\"?><ocl><message>ACK</message></ocl>";
        $this->assertEquals( true, $oUtilsXml->loadXml( $sValidXml, $oDom ) != false );
    }
}