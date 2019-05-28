<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Integration\Timestamp;

use oxDb;
use oxField;
use OxidEsales\Eshop\Application\Model\Order;
use OxidEsales\Eshop\Core\Field;

/**
 * Integration test testing corect timestamp setting on update and insert in all tables
 * directly from sql query or with object save() call
 */
class TimestampTest extends \OxidTestCase
{
    /**
     * Tear down the fixture.
     */
    protected function tearDown()
    {
        $aTables = $this->objectNames();
        foreach ($aTables as $aTable) {
            $this->cleanUpTable($aTable[1]);
        }

        oxDb::getDb()->execute("DELETE FROM `oxshops` WHERE `oxid` = 1");

        parent::tearDown();
    }

    /**
     * Data provider: object name, object db table, update field
     */
    public function objectNames()
    {
        $aNames = array(
            array('oxActions', 'oxactions', 'oxtitle'),
            array('oxAddress', 'oxaddress', 'oxcompany'),
            array('oxArticle', 'oxarticles', 'oxtitle'),
            array('oxAttribute', 'oxattribute', 'oxtitle'),
            array('oxCategory', 'oxcategories', 'oxtitle'),
            array('oxContent', 'oxcontents', 'oxtitle'),
            array('oxCountry', 'oxcountry', 'oxtitle'),
            array('oxDelivery', 'oxdelivery', 'oxtitle'),
            array('oxDeliverySet', 'oxdeliveryset', 'oxtitle'),
            array('oxDiscount', 'oxdiscount', 'oxtitle'),
            array('oxFile', 'oxfiles', 'oxfilename'),
            array('oxGroups', 'oxgroups', 'oxtitle'),
            array('oxLinks', 'oxlinks', 'oxurl'),
            array('oxManufacturer', 'oxmanufacturers', 'oxtitle'),
            array('oxMediaUrl', 'oxmediaurls', 'oxurl'),
            array('oxNews', 'oxnews', 'oxshortdesc'),
            array('oxOrder', 'oxorder', 'oxdelstreet'),
            array('oxOrderArticle', 'oxorderarticles', 'oxtitle'),
            array('oxOrderFile', 'oxorderfiles', 'oxfilename'),
            array('oxPayment', 'oxpayments', 'oxdesc'),
            array('oxPriceAlarm', 'oxpricealarm', 'oxemail'),
            array('oxRating', 'oxratings', 'oxobjectid'),
            array('oxRecommList', 'oxrecommlists', 'oxtitle'),
            array('oxRemark', 'oxremark', 'oxtext'),
            array('oxReview', 'oxreviews', 'oxtext'),
            array('oxSelectList', 'oxselectlist', 'oxtitle'),
            array('oxShop', 'oxshops', 'oxname'),
            array('oxState', 'oxstates', 'oxtitle'),
            array('oxUser', 'oxuser', 'oxusername'),
            array('oxUserBasketItem', 'oxuserbasketitems', 'oxsellist'),
            array('oxUserBasket', 'oxuserbaskets', 'oxtitle'),
            array('oxUserPayment', 'oxuserpayments', 'oxuserid'),
            array('oxVendor', 'oxvendor', 'oxtitle'),
            array('oxVoucher', 'oxvouchers', 'oxvouchernr'),
            array('oxVoucherSerie', 'oxvoucherseries', 'oxserienr'),
        );

        if ($this->getTestConfig()->getShopEdition() == 'EE') {
            $aNames[] = array('oxRole', 'oxroles', 'oxtitle');
        }

        return $aNames;
    }

    /**
     * oxtimestamp field must have been setted with creation date on direct db insert
     *
     * @dataProvider objectNames
     */
    public function testOnInsertDb($objectName, $tableName)
    {
        $sInsertSql = "INSERT INTO `$tableName` SET `oxid` = '".$this->formTestIdByTable($tableName)."'";
        $sSelectSql = "SELECT `oxtimestamp` FROM `$tableName` WHERE `oxid` = '".$this->formTestIdByTable($tableName)."'";

        $oDb = oxDb::getDb();

        $oDb->Execute($sInsertSql);
        $sTimeStamp = $oDb->getOne($sSelectSql);

        $this->assertTrue($sTimeStamp != '0000-00-00 00:00:00');
    }

    /**
     * oxtimestamp field must have been setted with modification date on direct db update
     *
     * @dataProvider objectNames
     */
    public function testOnUpdateDb($objectName, $tableName, $modifyField)
    {
        $sInsertSql = "INSERT INTO `$tableName` SET `oxid` = '".$this->formTestIdByTable($tableName)."', `oxtimestamp` = '0000-00-00 00:00:00' ";
        $sUpdateSql = "UPDATE `$tableName` SET `$modifyField` = '_testmodified' WHERE `oxid` = '".$this->formTestIdByTable($tableName)."'";
        $sSelectSql = "SELECT `oxtimestamp` FROM `$tableName` WHERE `oxid` = '".$this->formTestIdByTable($tableName)."'";

        $oDb = oxDb::getDb();

        $oDb->Execute($sInsertSql);
        $oDb->Execute($sUpdateSql);

        $sTimeStamp = $oDb->getOne($sSelectSql);

        $this->assertTrue($sTimeStamp != '0000-00-00 00:00:00');
    }

    /**
     * oxtimestamp field must have been setted creation date on object insert
     *
     * @dataProvider objectNames
     */
    public function testOnInsert($objectName, $tableName, $modifyField)
    {
        $attNameMod = $tableName . '__' . $modifyField;

        $oObject = oxNew($objectName);
        if ('oxorderarticles' == $tableName) {
            $order = oxNew(Order::class);
            $order->setId('100');
            $order->save();
            $oObject->oxorderarticles__oxorderid = new Field('100');
        }
        $oObject->setId($this->formTestIdByTable($tableName));
        $oObject->$attNameMod = new oxField('test');
        $oObject->save();

        $oObject = oxNew($objectName);
        $oObject->load($this->formTestIdByTable($tableName));

        $attName = $tableName . '__oxtimestamp';

        $this->assertTrue($oObject->$attName->value != '0000-00-00 00:00:00');
    }

    /**
     * oxtimestamp field must have been setted modification date on object update
     *
     * @dataProvider objectNames
     */
    public function testOnUpdate($objectName, $tableName, $modifyField)
    {
        $attName = $tableName . '__oxtimestamp';
        $attNameMod = $tableName . '__' . $modifyField;

        $oObject = oxNew($objectName);
        $oObject->setId($this->formTestIdByTable($tableName));
        $oObject->$attName = new oxField('0000-00-00 00:00:00');
        $oObject->$attNameMod = new oxField('test');
        if ('oxdiscount' == $tableName) {
            $oObject->oxdiscount__oxsort = new oxField(9999);
        }
        if ('oxorderarticles' == $tableName) {
            $order = oxNew(Order::class);
            $order->setId('100');
            $order->save();
            $oObject->oxorderarticles__oxorderid = new Field('100');
        }
        $oObject->save();

        $oObject = oxNew($objectName);
        $oObject->load($this->formTestIdByTable($tableName));
        $oObject->$attNameMod = new oxField('testmodyfied');
        $oObject->save();

        $oObject = oxNew($objectName);
        $oObject->load($this->formTestIdByTable($tableName));

        $this->assertTrue($oObject->$attName->value != '0000-00-00 00:00:00');
    }

    /**
     * Test to check if every DB table has oxtimestamp field
     *
     */
    public function testAllTablesHasOxTimestamp()
    {
        $oDb = oxDb::getDb();
        $sQ = "SELECT TABLE_NAME FROM information_schema.tables
          WHERE TABLE_TYPE='BASE TABLE'
            AND TABLE_NAME NOT LIKE 'oxmigrations%'
            AND TABLE_SCHEMA = DATABASE()";
        $aTableNames = $oDb->getAll($sQ);
        foreach ($aTableNames as $sKey => $aTable) {
            $sTableName = $aTable[0];
            $sSelectSql = "SHOW COLUMNS FROM `$sTableName` LIKE 'oxtimestamp'";
            $this->assertEquals("OXTIMESTAMP", $oDb->getOne($sSelectSql), "No OXTIMESTAMP field in TABLE: $sTableName");
        }
    }

    /**
     * @param string $tableName
     *
     * @return string
     */
    protected function formTestIdByTable($tableName)
    {
        $id = '_testId';
        if ($tableName === 'oxshops') {
            $id = 1;
        }

        return $id;
    }
}
