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
require_once realpath( "." ).'/unit/test_config.inc.php';

class Unit_Core_oxnewsTest extends OxidTestCase
{
    private $_oNews = null;

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        parent::setUp();
        $oBaseNews = oxNew( 'oxbase' );
        $oBaseNews->init( 'oxnews' );
        $oBaseNews->oxnews__oxshortdesc = new oxField('Test', oxField::T_RAW);
        $oBaseNews->oxnews__oxshortdesc_1 = new oxField('Test_news_1', oxField::T_RAW);
        $oBaseNews->Save();

        $this->_oNews = oxNew( 'oxnews' );
        $this->_oNews->load( $oBaseNews->getId() );


        $oNewGroup = oxNew( 'oxobject2group' );
        $oNewGroup->oxobject2group__oxobjectid = new oxField($this->_oNews->getId(), oxField::T_RAW);
        $oNewGroup->oxobject2group__oxgroupsid = new oxField('oxidnewcustomer', oxField::T_RAW);
        $oNewGroup->Save();
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $oDB = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        $sDelete = "delete from oxnews where oxid='".$this->_oNews->oxnews__oxid->value."' or oxshortdesc='Test' ";
        $oDB->Execute( $sDelete );

        $sDelete = "delete from oxobject2group where oxobjectid='".$this->_oNews->oxnews__oxid->value."' ";
        $oDB->Execute( $sDelete );

        parent::tearDown();
    }

    /**
     * Testing if update really updates and does not harm date field
     */
    public function testUpdate()
    {
        $oTestNews = oxNew( 'oxnews' );
        $oTestNews->Load( $this->_oNews->getId() );
        $oTestNews->oxnews__oxshortdesc = new oxField('Test_news', oxField::T_RAW);
        $oTestNews->UNITupdate();

        $oNews = oxNew( 'oxnews' );
        $oNews->Load( $this->_oNews->getId() );
        $this->assertEquals( $oNews->oxnews__oxshortdesc->value, 'Test_news' );
        $this->assertEquals( $oNews->oxnews__oxdate->value, $oTestNews->oxnews__oxdate->value );
    }

    /**
     * getLongDesc() test case
     * test getted long description with smarty tags
     *
     * @return null
     */
    public function testGetLongDescTags()
    {
        $oNews = oxNew( 'oxnews' );
        $oNews->oxnews__oxlongdesc = new oxField( "[{* *}]parsed" );
        $this->assertEquals('parsed', $oNews->getLongDesc());
    }

    /**
     * getLongDesc() test case
     * test returned long description with smarty tags when template regeneration is disabled
     * and template is saved twice.
     *
     * @return null
     */
    public function testGetLongDescTagsWhenTemplateAlreadyGeneratedAndRegenerationDisabled()
    {
        $this->getConfig()->setConfigParam('blCheckTemplates', false);

        $oNews = oxNew( 'oxnews' );
        $oNews->oxnews__oxlongdesc = new oxField( "[{* *}]generated" );
        $oNews->getLongDesc();
        $oNews->oxnews__oxlongdesc = new oxField( "[{* *}]regenerated" );
        $this->assertEquals('regenerated', $oNews->getLongDesc());
    }

    /**
     * Testing multilanguage and date
     */
    public function testAssign()
    {
        $oDB = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        $oTestNews = oxNew( 'oxnews' );
        $oTestNews->loadInLang(1, $this->_oNews->getId() );
        $this->assertEquals( $oTestNews->oxnews__oxshortdesc->value, 'Test_news_1' );
        $sQ = "select oxdate from oxnews where oxid='".$this->_oNews->getId()."'";
        $this->assertEquals( $oTestNews->oxnews__oxdate->value, oxUtilsDate::getInstance()->formatDBDate( $oDB->GetOne( $sQ ) ) );
    }

    /**
     * Testing date
     * FS#2519
     */
    public function testAssignAndSaveSpecDate()
    {
        $aParams['oxnews__oxdate'] = '20081212';

        $oTestNews = oxNew( 'oxnews' );
        $oTestNews->load( $this->_oNews->getId() );
        $oTestNews->setLanguage(0);
        $oTestNews->assign( $aParams );
        $oTestNews->save();

        $sQ = "select oxdate from oxnews where oxid='".$this->_oNews->getId()."'";

        $this->assertEquals( $oTestNews->oxnews__oxshortdesc->value, 'Test' );
        $this->assertEquals( $oTestNews->oxnews__oxdate->value, oxDb::getDb()->getOne( $sQ ) );
    }


    /**
     * Testing existing group
     */
    public function testGetGroups()
    {
        $oTestNews = oxNew( 'oxnews' );
        $oTestNews->load( $this->_oNews->getId() );
        $aGroups = $oTestNews->getGroups();
        $this->assertTrue( count( $aGroups ) == 1 );
        $oGroup = $aGroups->current();
        $this->assertEquals( 'oxidnewcustomer', $oGroup->oxgroups__oxid->value );
    }

    /**
     * Testing not existing groups
     */
    public function testGetGroupsNoGroups()
    {
        $oTestNews = oxNew( 'oxnews' );
        $this->assertNull( $oTestNews->getGroups() );
    }

    /**
     * Testing existing group
     */
    public function testIsInGroup()
    {
        $iErrorReporting = error_reporting( E_ALL ^ E_NOTICE );
        $e = null;
        try {
            $oTestNews = oxNew( 'oxnews' );
            $oTestNews->load( $this->_oNews->getId() );
            $this->assertTrue( $oTestNews->inGroup( 'oxidnewcustomer' ) );
        } catch (Exception $e){
        }
        error_reporting( $iErrorReporting );
        if ($e) {
            throw $e;
        }
    }

    /**
     * Testing not existing group
     */
    public function testIsNotInGroup()
    {
        $iErrorReporting = error_reporting( E_ALL ^ E_NOTICE );
        $e = null;
        try {
            $oTestNews = oxNew( 'oxnews' );
            $oTestNews->load( $this->_oNews->getId() );
            $this->assertFalse( $oTestNews->inGroup( 'xxx' ) );
        } catch (Exception $e){
        }
        error_reporting( $iErrorReporting );
        if ($e) {
            throw $e;
        }
    }

    /**
     * Testing if insert works at all and if date is set correctly
     */
    public function testInsert()
    {
        $iErrorReporting = error_reporting( E_ALL ^ E_NOTICE );
        $e = null;
        try {
            $oTestNews = oxNew( 'oxnews' );
            $oTestNews->oxnews__oxdate = new oxField( "2009-05-17" );
            $oTestNews->UNITinsert();

            $oNews = oxNew( 'oxnews' );
            if ( !$oNews->load( $oTestNews->getId() ) ) {
                $this->fail( 'insert failed' );
            }

            $this->assertEquals( "17.05.2009", $oNews->oxnews__oxdate->value );
        } catch ( Exception $e ){
        }

        error_reporting( $iErrorReporting );
        if ( $e ) {
            throw $e;
        }
    }

    /**
     * Testing if insert works at all and if date is set correctly
     */
    public function testInsert_dateIsZero()
    {
        $iErrorReporting = error_reporting( E_ALL ^ E_NOTICE );
        $e = null;
        try {
            $oTestNews = oxNew( 'oxnews' );
            $oTestNews->oxnews__oxdate = new oxField( "0000-00-00" );
            $oTestNews->UNITinsert();

            $oNews = oxNew( 'oxnews' );
            if ( !$oNews->load( $oTestNews->getId() ) ) {
                $this->fail( 'insert failed' );
            }

            $this->assertEquals( date( "d.m.Y" ), $oNews->oxnews__oxdate->value );
        } catch ( Exception $e ){
        }

        error_reporting( $iErrorReporting );
        if ( $e ) {
            throw $e;
        }
    }

    /**
     * Testing if insert works at all and if date is set correctly
     */
    public function testInsert_dateNotEntered()
    {
        $iErrorReporting = error_reporting( E_ALL ^ E_NOTICE );
        $e = null;
        try {
            $oTestNews = oxNew( 'oxnews' );
            $oTestNews->UNITinsert();

            $oNews = oxNew( 'oxnews' );
            if ( !$oNews->load( $oTestNews->getId() ) ) {
                $this->fail( 'insert failed' );
            }

            $this->assertEquals( date( "d.m.Y" ), $oNews->oxnews__oxdate->value );
        } catch ( Exception $e ){
        }

        error_reporting( $iErrorReporting );
    }

    /**
     * Testing if deletion does not leave trash in DB
     */
    public function testDelete()
    {
        $oTestNews = oxNew( 'oxnews' );
        $this->assertTrue( $oTestNews->delete( $this->_oNews->getId() ));

        $oDB = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );

        $sSelect = "select * from oxobject2group where oxobjectid='".$this->_oNews->getId()."' ";
        $this->assertFalse( $oDB->GetOne( $sSelect ) );

        $sSelect = "select * from oxnews where oxid='".$this->_oNews->getId()."' ";
        $this->assertFalse( $oDB->GetOne( $sSelect ) );
    }
    // deleting non existing
    public function testDeleteNonExisting()
    {
        $oTestNews = new oxnews();
        $this->assertFalse( $oTestNews->delete() );
    }

    /**
     * Testing if deletion of non existing object really returns false
     */
    public function testDeleteNotExistingNews()
    {
        $oTestNews = oxNew( 'oxnews' );
        $this->assertFalse( $oTestNews->delete( 'xxx' ) );
    }
    public function test_setFieldData()
    {
        $oObj = $this->getProxyClass('oxnews');
        $oObj->disableLazyLoading();
        $oObj->UNITsetFieldData("oxid", "asd< as");
        $oObj->UNITsetFieldData("oxshortdeSc", "asd< as");
        $oObj->UNITsetFieldData("oxlongDesc", "asd< as");
        $this->assertEquals( 'asd&lt; as', $oObj->oxnews__oxid->value );
        $this->assertEquals( 'asd&lt; as', $oObj->oxnews__oxshortdesc->value );
        $this->assertEquals( 'asd< as', $oObj->oxnews__oxlongdesc->value );
    }

    /**
     * Testing assign of oxlongdesc field
     * M#265
     */
    public function testAssignLongDescription()
    {
        $sSql = "update oxnews set oxlongdesc = '<p>test text</p>' where oxid='".$this->_oNews->getId()."' ";
        $oDB = oxDb::getDb( oxDB::FETCH_MODE_ASSOC );
        $oDB->execute( $sSql );

        oxTestModules::addFunction( "oxutilsview", "parseThroughSmarty", "{return '<p>test text</p>';}" );

        $oNews = oxNew( 'oxnews' );
        $oNews->load( $this->_oNews->getId() );

        $this->assertEquals( '<p>test text</p>', $oNews->oxnews__oxlongdesc->value );
    }

}
