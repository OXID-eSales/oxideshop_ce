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
 * @copyright (C) OXID eSales AG 2003-2014
 * @version   OXID eShop CE
 */

require_once realpath( "." ).'/unit/OxidTestCase.php';

/**
 * Testing oxXml class.
 */
class Unit_Core_oxSimpleXmlTest extends OxidTestCase
{
    public function testObjectToXml()
    {
        $oXml = new oxSimpleXml();

        $oTestObject = new oxStdClass();
        $oTestObject->title = "TestTitle";
        $oTestObject->keys = new oxStdClass();
        $oTestObject->keys->key = array("testKey1", "testKey2");

        $sTestResult = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<testXml><title>TestTitle</title><keys><key>testKey1</key><key>testKey2</key></keys></testXml>\n";

        $this->assertEquals($sTestResult, $oXml->objectToXml( $oTestObject, "testXml" ));
    }

    public function testObjectToXmlWithObjectsInArray()
    {
        $oXml = new oxSimpleXml();

        $oModule1 = new stdClass();
        $oModule1->id = "id1";
        $oModule1->active = true;

        $oModule2 = new stdClass();
        $oModule2->id = "id2";
        $oModule2->active = false;

        $oTestObject = new oxStdClass();
        $oTestObject->title = "TestTitle";
        $oTestObject->modules = new oxStdClass();
        $oTestObject->modules->module = array($oModule1, $oModule2);

        $sTestResult = "<?xml version=\"1.0\" encoding=\"utf-8\"?>\n<testXml><title>TestTitle</title><modules><module><id>$oModule1->id</id><active>$oModule1->active</active></module><module><id>$oModule2->id</id><active>$oModule2->active</active></module></modules></testXml>\n";

        $this->assertEquals($sTestResult, $oXml->objectToXml( $oTestObject, "testXml" ));
    }

    public function testXmlToObject()
    {
        $oXml = new oxSimpleXml();

        $sTestXml = '<?xml version="1.0"?><testXml><title>TestTitle</title><keys><key>testKey1</key><key>testKey2</key></keys></testXml>';

        $oRes = $oXml->xmlToObject( $sTestXml );

        $this->assertEquals((string) $oRes->title,         "TestTitle");
        $this->assertEquals((string) $oRes->keys->key[0],  "testKey1");
        $this->assertEquals((string) $oRes->keys->key[1],  "testKey2");
    }
}