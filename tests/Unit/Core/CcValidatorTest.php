<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

/**
 * Test oxCcValidator test class
 */
class CcValidatorTest extends \OxidTestCase
{

    /**
     * oxCcValidator::isValidCard() test case
     *
     * @return null
     */
    public function testIsValidCard()
    {
        $sDate = date("my");
        $sBadDate = date("my", mktime(0, 0, 0, date("m"), date("d"), date("Y") - 1));

        $oValidator = oxNew('oxCcValidator');

        // good numbers, good exp. date
        $this->assertTrue($oValidator->isValidCard("370000000000002", "amx", $sDate));
        $this->assertTrue($oValidator->isValidCard("346614479926751", "amx", $sDate));
        //$this->assertTrue( (bool) ccval( "370000000000002", "amx", $sDate ) );
        //$this->assertTrue( (bool) ccval( "346614479926751", "amx", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("4123204094732031", "dlt", $sDate));
        //$this->assertTrue( (bool) ccval( "4123204094732031", "dlt", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("30053271462494", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("30126817315200", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("30238864311402", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("30306447878880", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("30457734735174", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("30583853298031", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("36757306220628", "dnc", $sDate));
        $this->assertTrue($oValidator->isValidCard("38962939003601", "dnc", $sDate));
        //$this->assertTrue( (bool) ccval( "30053271462494", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "30126817315200", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "30238864311402", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "30306447878880", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "30457734735174", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "30583853298031", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "36757306220628", "dnc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "38962939003601", "dnc", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("6011000000000012", "dsc", $sDate));
        $this->assertTrue($oValidator->isValidCard("6011452897367709", "dsc", $sDate));
        //$this->assertTrue( (bool) ccval( "6011000000000012", "dsc", $sDate ) );
        //$this->assertTrue( (bool) ccval( "6011452897367709", "dsc", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("201443710703496", "enr", $sDate));
        $this->assertTrue($oValidator->isValidCard("214911470712837", "enr", $sDate));
        //$this->assertTrue( (bool) ccval( "201443710703496", "enr", $sDate ) );
        //$this->assertTrue( (bool) ccval( "214911470712837", "enr", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("3322036316146678", "jcb", $sDate));
        $this->assertTrue($oValidator->isValidCard("213186326125432", "jcb", $sDate));
        $this->assertTrue($oValidator->isValidCard("180097657874622", "jcb", $sDate));
        //$this->assertTrue( (bool) ccval( "3322036316146678", "jcb", $sDate ) );
        //$this->assertTrue( (bool) ccval( "213186326125432", "jcb", $sDate ) );
        //$this->assertTrue( (bool) ccval( "180097657874622", "jcb", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("5424000000000015", "mcd", $sDate));
        $this->assertTrue($oValidator->isValidCard("5149889753083309", "mcd", $sDate));
        $this->assertTrue($oValidator->isValidCard("5266463343974091", "mcd", $sDate));
        $this->assertTrue($oValidator->isValidCard("5390921666851369", "mcd", $sDate));
        $this->assertTrue($oValidator->isValidCard("5539678405471857", "mcd", $sDate));
        //$this->assertTrue( (bool) ccval( "5424000000000015", "mcd", $sDate ) );
        //$this->assertTrue( (bool) ccval( "5149889753083309", "mcd", $sDate ) );
        //$this->assertTrue( (bool) ccval( "5266463343974091", "mcd", $sDate ) );
        //$this->assertTrue( (bool) ccval( "5390921666851369", "mcd", $sDate ) );
        //$this->assertTrue( (bool) ccval( "5539678405471857", "mcd", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("5317719108904446776", "swi", $sDate));
        $this->assertTrue($oValidator->isValidCard("5017706301019430832", "swi", $sDate));
        //$this->assertTrue( (bool) ccval( "5317719108904446776", "swi", $sDate ) );
        //$this->assertTrue( (bool) ccval( "5017706301019430832", "swi", $sDate ) );

        $this->assertTrue($oValidator->isValidCard("4111111111111111", "vis", $sDate));
        $this->assertTrue($oValidator->isValidCard("4783703743729414", "vis", $sDate));
        $this->assertTrue($oValidator->isValidCard("4897123703188", "vis", $sDate));
        //$this->assertTrue( (bool) ccval( "4111111111111111", "vis", $sDate ) );
        //$this->assertTrue( (bool) ccval( "4783703743729414", "vis", $sDate ) );
        //$this->assertTrue( (bool) ccval( "4897123703188", "vis", $sDate ) );

        // faulty numbers
        $this->assertFalse($oValidator->isValidCard("570000000000002", "amx", $sDate));
        $this->assertFalse($oValidator->isValidCard("846614479926751", "amx", $sDate));
        //$this->assertFalse( (bool) ccval( "570000000000002", "amx", $sDate ) );
        //$this->assertFalse( (bool) ccval( "846614479926751", "amx", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("5123204094732031", "dlt", $sDate));
        //$this->assertFalse( (bool) ccval( "8123204094732031", "dlt", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("60053271462494", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("60126817315200", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("60238864311402", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("60306447878880", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("60457734735174", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("60583853298031", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("66757306220628", "dnc", $sDate));
        $this->assertFalse($oValidator->isValidCard("68962939003601", "dnc", $sDate));
        //$this->assertFalse( (bool) ccval( "60053271462494", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "60126817315200", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "60238864311402", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "60306447878880", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "60457734735174", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "60583853298031", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "66757306220628", "dnc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "68962939003601", "dnc", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("7011000000000012", "dsc", $sDate));
        $this->assertFalse($oValidator->isValidCard("7011452897367709", "dsc", $sDate));
        //$this->assertFalse( (bool) ccval( "7011000000000012", "dsc", $sDate ) );
        //$this->assertFalse( (bool) ccval( "7011452897367709", "dsc", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("301443710703496", "enr", $sDate));
        $this->assertFalse($oValidator->isValidCard("314911470712837", "enr", $sDate));
        //$this->assertFalse( (bool) ccval( "301443710703496", "enr", $sDate ) );
        //$this->assertFalse( (bool) ccval( "314911470712837", "enr", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("4322036316146678", "jcb", $sDate));
        $this->assertFalse($oValidator->isValidCard("413186326125432", "jcb", $sDate));
        $this->assertFalse($oValidator->isValidCard("480097657874622", "jcb", $sDate));
        //$this->assertFalse( (bool) ccval( "4322036316146678", "jcb", $sDate ) );
        //$this->assertFalse( (bool) ccval( "413186326125432", "jcb", $sDate ) );
        //$this->assertFalse( (bool) ccval( "480097657874622", "jcb", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("4424000000000015", "mcd", $sDate));
        $this->assertFalse($oValidator->isValidCard("4149889753083309", "mcd", $sDate));
        $this->assertFalse($oValidator->isValidCard("4266463343974091", "mcd", $sDate));
        $this->assertFalse($oValidator->isValidCard("4390921666851369", "mcd", $sDate));
        $this->assertFalse($oValidator->isValidCard("4539678405471857", "mcd", $sDate));
        //$this->assertFalse( (bool) ccval( "4424000000000015", "mcd", $sDate ) );
        //$this->assertFalse( (bool) ccval( "4149889753083309", "mcd", $sDate ) );
        //$this->assertFalse( (bool) ccval( "4266463343974091", "mcd", $sDate ) );
        //$this->assertFalse( (bool) ccval( "4390921666851369", "mcd", $sDate ) );
        //$this->assertFalse( (bool) ccval( "4539678405471857", "mcd", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("3317719108904446776", "swi", $sDate));
        $this->assertFalse($oValidator->isValidCard("3017706301019430832", "swi", $sDate));
        //$this->assertFalse( (bool) ccval( "3317719108904446776", "swi", $sDate ) );
        //$this->assertFalse( (bool) ccval( "3017706301019430832", "swi", $sDate ) );

        $this->assertFalse($oValidator->isValidCard("5111111111111111", "vis", $sDate));
        $this->assertFalse($oValidator->isValidCard("5783703743729414", "vis", $sDate));
        $this->assertFalse($oValidator->isValidCard("5897123703188", "vis", $sDate));
        //$this->assertFalse( (bool) ccval( "5111111111111111", "vis", $sDate ) );
        //$this->assertFalse( (bool) ccval( "5783703743729414", "vis", $sDate ) );
        //$this->assertFalse( (bool) ccval( "5897123703188", "vis", $sDate ) );

        // good numbers, bad exp. date
        $this->assertFalse($oValidator->isValidCard("370000000000002", "amx", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("346614479926751", "amx", $sBadDate));
        //$this->assertFalse( (bool) ccval( "370000000000002", "amx", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "346614479926751", "amx", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("4123204094732031", "dlt", $sBadDate));
        //$this->assertFalse( (bool) ccval( "4123204094732031",  "dlt", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("30053271462494", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("30126817315200", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("30238864311402", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("30306447878880", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("30457734735174", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("30583853298031", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("36757306220628", "dnc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("38962939003601", "dnc", $sBadDate));
        //$this->assertFalse( (bool) ccval( "30053271462494", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "30126817315200", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "30238864311402", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "30306447878880", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "30457734735174", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "30583853298031", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "36757306220628", "dnc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "38962939003601", "dnc", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("6011000000000012", "dsc", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("6011452897367709", "dsc", $sBadDate));
        //$this->assertFalse( (bool) ccval( "6011000000000012", "dsc", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "6011452897367709", "dsc", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("201443710703496", "enr", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("214911470712837", "enr", $sBadDate));
        //$this->assertFalse( (bool) ccval( "201443710703496", "enr", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "214911470712837", "enr", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("3322036316146678", "jcb", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("213186326125432", "jcb", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("180097657874622", "jcb", $sBadDate));
        //$this->assertFalse( (bool) ccval( "3322036316146678", "jcb", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "213186326125432", "jcb", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "180097657874622", "jcb", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("5424000000000015", "mcd", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("5149889753083309", "mcd", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("5266463343974091", "mcd", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("5390921666851369", "mcd", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("5539678405471857", "mcd", $sBadDate));
        //$this->assertFalse( (bool) ccval( "5424000000000015", "mcd", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "5149889753083309", "mcd", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "5266463343974091", "mcd", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "5390921666851369", "mcd", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "5539678405471857", "mcd", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("5317719108904446776", "swi", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("5017706301019430832", "swi", $sBadDate));
        //$this->assertFalse( (bool) ccval( "5317719108904446776", "swi", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "5017706301019430832", "swi", $sBadDate ) );

        $this->assertFalse($oValidator->isValidCard("4111111111111111", "vis", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("4783703743729414", "vis", $sBadDate));
        $this->assertFalse($oValidator->isValidCard("4897123703188", "vis", $sBadDate));
        //$this->assertFalse( (bool) ccval( "4111111111111111", "vis", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "4783703743729414", "vis", $sBadDate ) );
        //$this->assertFalse( (bool) ccval( "4897123703188", "vis", $sBadDate ) );
    }
}
