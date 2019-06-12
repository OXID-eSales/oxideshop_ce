<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Model;

use \oxDb;
use OxidEsales\EshopCommunity\Core\ShopIdCalculator;
use oxVoucherException;

/**
 * Testing oxVoucher class
 */
class VoucherExcludeTest extends \OxidTestCase
{

    /**
     * Initialize the fixture.
     *
     * @return null
     */
    protected function setUp()
    {
        /*

         TEST TABLE

         --------------------------------------------------------------------------
         |           | Vouchers     | Descriptions                                |
         |------------------------------------------------------------------------|
         | discount  | 10 20 30 40  | Discount value [$|%]                        |
         |-----------|--------------|---------------------------------------------|
         | percent % | S5 S6 S7 S8  | S[0..8] - voucher series                    |
         | amount  $ | S1 S2 S3 S4  | A[0..5] - articles                          |
         |--------------------------| C[0..2] - categories                        |
         |    C0| A0 | -- -- -- --  | $$$     - fixed discount                    |
         |    C0| A1 | @@ ## -- --  | %%%     - percent discount                  |
         |    C0| A2 | @@ ## -- --  | @@      - voucher                           |
         | C1 C0| A3 | -- -- @@ @@  | ##      - individual voucher                |
         | C1 C2| A4 | -- -- XX @@  | --      - not assigned                      |
         |    C2| A5 | -- -- @@ XX  | XX      - not added to basket               |
         --------------------------------------------------------------------------

         S0 - voucher 50$  Regular
         X1 - voucher 1000$ Product  (A5)
         X2 - voucher 1000$ Category (C2)
         X3 - voucher 1000$ Individual Product (A5)

         TEST CASES

         Product Voucher S1  (10$)               Product Voucher S5  (10%)

         | ID | Price | Amount | Discount |      | ID | Price | Amount | Discount |
         |----|-------|--------|----------|      |----|-------|--------|----------|
         | A0 | 1     | 1      |          |      | A0 | 1     | 1      |          |
         | A1 | 10    | 5      | 10       |      | A1 | 10    | 5      | 1 (10%)  |
         | A2 | 20    | 5      | 10       |      | A2 | 20    | 5      | 2 (10%)  |
         | A3 | 30    | 5      |          |      | A3 | 30    | 5      |          |
         | A4 | 40    | 5      |          |      | A4 | 40    | 5      |          |
         | A5 | 50    | 5      |          |      | A5 | 50    | 5      |          |

         Items: 1+50+100+150+200+250 = 751       Items: 1+50+100+150+200+250 = 751
         Valid: 50+100               = 150       Valid: 50+100               = 150
         Coupon: (10*5)+(10*5)       = 100       Coupon: (1*5)+(2*5)         = 15
         Apply: (Coupon)             = 100       Apply: (Coupon)             = 15
         Total: 751-100              = 651       Total: 751-15               = 736


         Product Voucher S2  (20$)                        Product Voucher S6  (20%)

         | ID | Price | Amount | Discount |      | ID | Price | Amount | Discount |
         |----|-------|--------|----------|      |----|-------|--------|----------|
         | A0 | 1     | 1      |          |      | A0 | 1     | 1      |          |
         | A1 | 10    | 5      | 20       |      | A1 | 10    | 5      | 2 (20%)  |
         | A2 | 20    | 5      | 20       |      | A2 | 20    | 5      | 4 (20%)  |
         | A3 | 30    | 5      |          |      | A3 | 30    | 5      |          |
         | A4 | 40    | 5      |          |      | A4 | 40    | 5      |          |
         | A5 | 50    | 5      |          |      | A5 | 50    | 5      |          |

         Items: 1+50+100+150+200+250 = 751       Items: 1+50+100+150+200+250 = 751
         Valid: 10+20                = 30        Valid: 10+20                = 30
         Coupon: (20*1)              = 20        Coupon: (2*1)               = 2
         Apply: (Valid)              = 10        Apply: (Coupon)             = 2
         Total: 751-30               = 721       Total: 751-6                = 745


         Category Voucher S3  (30$)              Category Voucher S7  (30%)

         | ID | Price | Amount | Discount |      | ID | Price | Amount | Discount |
         |----|-------|--------|----------|      |----|-------|--------|----------|
         | A0 | 1     | 1      |          |      | A0 | 1     | 1      |          |
         | A1 | 10    | 5      |          |      | A1 | 10    | 5      |          |
         | A2 | 20    | 5      |          |      | A2 | 20    | 5      |          |
         | A3 | 30    | 5      | 30       |      | A3 | 30    | 5      | 9  (30%) |
         | A4 | 40    | 0      |          |      | A4 | 40    | 0      |          |
         | A5 | 50    | 5      | 30       |      | A5 | 50    | 5      | 15 (30%) |

         Items: 1+50+100+150+0+250   = 551       Items: 1+50+100+150+0+250   = 551
         Valid: 150+250              = 400       Valid: 150+250              = 400
         Coupon: 30                  = 30        Coupon: (50+30)*30%         = 24
         Apply: (Coupon)             = 30        Apply: (Coupon)             = 24
         Total: 551-30               = 521       Total: 551-24               = 527


         Category Voucher S4  (40$)              Category Voucher S8  (40%)

         | ID | Price | Amount | Discount |      | ID | Price | Amount | Discount |
         |----|-------|--------|----------|      |----|-------|--------|----------|
         | A0 | 1     | 1      |          |      | A0 | 1     | 1      |          |
         | A1 | 10    | 5      |          |      | A1 | 10    | 5      |          |
         | A2 | 20    | 5      |          |      | A2 | 20    | 5      |          |
         | A3 | 30    | 5      | 40       |      | A3 | 30    | 5      | 12 (40%) |
         | A4 | 40    | 5      | 40       |      | A4 | 40    | 5      | 16 (40%) |
         | A5 | 50    | 0      |          |      | A5 | 50    | 0      |          |

         Items: 1+50+100+150+200+0   = 501       Items: 1+50+100+150+200+0   = 501
         Valid: 150+200              = 350       Valid: 150+200              = 350
         Coupon: 40                  = 40        Coupon: (30+40)*40%         = 28
         Apply: (Coupon)             = 40        Apply: (Coupon)             = 28
         Total: 501-40               = 461       Total: 501-28               = 473




         SPECIAL CASES ( Valid < Coupon )

         Product Voucher X1  (1000$)             Category Voucher X2  (1000$)            Individual Product Voucher X3 (1000$)

         | ID | Price | Amount | Discount |      | ID | Price | Amount | Discount |      | ID | Price | Amount | Discount |
         |----|-------|--------|----------|      |----|-------|--------|----------|      |----|-------|--------|----------|
         | A0 | 1     | 1      |          |      | A0 | 1     | 1      |          |      | A0 | 1     | 1      |          |
         | A1 | 10    | 5      |          |      | A1 | 10    | 5      |          |      | A1 | 10    | 5      |          |
         | A2 | 20    | 5      |          |      | A2 | 20    | 5      |          |      | A2 | 20    | 5      |          |
         | A3 | 30    | 5      |          |      | A3 | 30    | 5      |          |      | A3 | 30    | 5      |          |
         | A4 | 40    | 5      |          |      | A4 | 40    | 5      | 1000     |      | A4 | 40    | 5      |          |
         | A5 | 50    | 5      | 1000     |      | A5 | 50    | 5      | 1000     |      | A5 | 50    | 5      | 1000     |

         Items: 1+50+100+150+200+250 = 751       Items: 1+50+100+150+200+250 = 751       Items: 1+50+100+150+200+250 = 751
         Valid: 250                  = 250       Valid: 200+250              = 450       Valid: 50                   = 50
         Coupon: 1000*5              = 5000      Coupon: 1000                = 1000      Coupon: 1000*1              = 1000
         Apply: (Valid)              = 250       Apply: (Valid)              = 250       Apply: (Valid)              = 50
         Total: 751-250              = 501       Total: 751-450              = 301       Total: 751-50               = 701

        */
        $sShopFields = "`OXSHOPID`";
        $sShopId = ShopIdCalculator::BASE_SHOP_ID;
        $sShopValues = $sShopId;

        $sInsertSeriesPart = "
        INSERT INTO `oxvoucherseries`
        (`OXID`, {$sShopFields}, `OXSERIENR`, `OXSERIEDESCRIPTION`, `OXDISCOUNT`, `OXDISCOUNTTYPE`, `OXMINIMUMVALUE`, `OXCALCULATEONCE`)
        VALUES ";
        $sValuesPart = "('test_s0',$sShopValues,'s0','$$$ A','50','absolute','0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_x1',$sShopValues,'x1','$$$ A @@','1000','absolute','0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_x2',$sShopValues,'x2','$$$ C @@','1000','absolute','0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_x3',$sShopValues,'x3','$$$ A ##','1000','absolute','0',1);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');

        $sValuesPart = "('test_s1',$sShopValues,'s1','$$$ A @@','10','absolute','0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_s2',$sShopValues,'s2','$$$ A ##','20','absolute','0',1);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_s3',$sShopValues,'s3','$$$ C @@','30','absolute','0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_s4',$sShopValues,'s4','$$$ C @@','40','absolute','0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');

        $sValuesPart = "('test_s5',$sShopValues,'s5','%%% A @@','10','percent' ,'0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_s6',$sShopValues,'s6','%%% A ##','20','percent' ,'0',1);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_s7',$sShopValues,'s7','%%% C @@','30','percent' ,'0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');
        $sValuesPart = "('test_s8',$sShopValues,'s8','%%% C @@','40','percent' ,'0',0);";
        $this->addToDatabase($sInsertSeriesPart . $sValuesPart, 'oxvoucherseries');

        $sInsertVouchers = "
        INSERT INTO `oxvouchers`
        (`OXVOUCHERSERIEID`,`OXID`, `OXDATEUSED`, `OXORDERID`, `OXUSERID`, `OXRESERVED`, `OXVOUCHERNR`, `OXDISCOUNT`)
        VALUES
        ('test_s0','test_000',NULL ,'','',0,'000',NULL),

        ('test_x1','test_100',NULL ,'','',0,'000',NULL),
        ('test_x2','test_200',NULL ,'','',0,'000',NULL),
        ('test_x3','test_300',NULL ,'','',0,'000',NULL),

        ('test_s1','test_111',NULL ,'','',0,'111',NULL),   ('test_s1','test_111111',NOW() ,'test_o1','oxdefaultadmin',0,'111111','10'),
        ('test_s2','test_222',NULL ,'','',0,'222',NULL),   ('test_s2','test_222222',NOW() ,'test_o2','oxdefaultadmin',0,'222222','20'),
        ('test_s3','test_333',NULL ,'','',0,'333',NULL),   ('test_s3','test_333333',NOW() ,'test_o3','oxdefaultadmin',0,'333333','30'),
        ('test_s4','test_444',NULL ,'','',0,'444',NULL),   ('test_s4','test_444444',NOW() ,'test_o4','oxdefaultadmin',0,'444444','40'),

        ('test_s5','test_555',NULL ,'','',0,'555',NULL),   ('test_s5','test_555555',NOW() ,'test_o5','oxdefaultadmin',0,'111111','10'),
        ('test_s6','test_666',NULL ,'','',0,'666',NULL),   ('test_s6','test_666666',NOW() ,'test_o6','oxdefaultadmin',0,'111111','20'),
        ('test_s7','test_777',NULL ,'','',0,'777',NULL),   ('test_s7','test_777777',NOW() ,'test_o7','oxdefaultadmin',0,'111111','30'),
        ('test_s8','test_888',NULL ,'','',0,'888',NULL),   ('test_s8','test_888888',NOW() ,'test_o8','oxdefaultadmin',0,'111111','40');";

        $sInsertArticlesPart = "
        INSERT INTO `oxarticles`
        (`OXID`, {$sShopFields}, `OXACTIVE`, `OXTITLE`, `OXPRICE`, `OXSTOCK`)
        VALUES ";
        $sValuesPart = "('test_a0',$sShopValues,'1','a0','1' ,100);";
        $this->addToDatabase($sInsertArticlesPart . $sValuesPart, 'oxarticles');
        $sValuesPart = "('test_a1',$sShopValues,'1','a1','10',100);";
        $this->addToDatabase($sInsertArticlesPart . $sValuesPart, 'oxarticles');
        $sValuesPart = "('test_a2',$sShopValues,'1','a2','20',100);";
        $this->addToDatabase($sInsertArticlesPart . $sValuesPart, 'oxarticles');
        $sValuesPart = "('test_a3',$sShopValues,'1','a3','30',100);";
        $this->addToDatabase($sInsertArticlesPart . $sValuesPart, 'oxarticles');
        $sValuesPart = "('test_a4',$sShopValues,'1','a4','40',100);";
        $this->addToDatabase($sInsertArticlesPart . $sValuesPart, 'oxarticles');
        $sValuesPart = "('test_a5',$sShopValues,'1','a5','50',100);";
        $this->addToDatabase($sInsertArticlesPart . $sValuesPart, 'oxarticles');

        $sInsertCategoriesPart = "
        INSERT INTO `oxcategories`
        (`OXID`, {$sShopFields}, `OXACTIVE`, `OXTITLE`)
        VALUES ";
        $sValuesPart = "('test_c0',$sShopValues,'1','c0');";
        $this->addToDatabase($sInsertCategoriesPart . $sValuesPart, 'oxcategories');
        $sValuesPart = "('test_c1',$sShopValues,'1','c1');";
        $this->addToDatabase($sInsertCategoriesPart . $sValuesPart, 'oxcategories');
        $sValuesPart = "('test_c2',$sShopValues,'1','c2');";
        $this->addToDatabase($sInsertCategoriesPart . $sValuesPart, 'oxcategories');

        $sInsertCategoryRelationsPart = "
        INSERT INTO `oxobject2category`
        (`OXID`, `OXCATNID`, `OXOBJECTID`)
        VALUES ";

        $sValuesPart = "('test_r00','test_c0','test_a0')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');
        $sValuesPart = "('test_r01','test_c0','test_a1')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');
        $sValuesPart = "('test_r02','test_c0','test_a2')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');
        $sValuesPart = "('test_r03','test_c0','test_a3')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');

        $sValuesPart = "('test_r11','test_c1','test_a3')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');
        $sValuesPart = "('test_r12','test_c1','test_a4')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');

        $sValuesPart = "('test_r21','test_c2','test_a4')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');
        $sValuesPart = "('test_r22','test_c2','test_a5')";
        $this->addToDatabase($sInsertCategoryRelationsPart . $sValuesPart, 'oxobject2category');

        $sInsertVoucherReleations = "
        INSERT INTO `oxobject2discount`
        (`OXID`, `OXDISCOUNTID`, `OXOBJECTID`, `OXTYPE`)
        VALUES

        ('test_r01','test_x1','test_a5','oxarticles'  ),
        ('test_r02','test_x2','test_c2','oxcategories'),
        ('test_r03','test_x3','test_a5','oxarticles'  ),

        ('test_r11','test_s1','test_a1','oxarticles'  ),  ('test_r12','test_s1','test_a2','oxarticles'  ),
        ('test_r21','test_s2','test_a1','oxarticles'  ),  ('test_r22','test_s2','test_a2','oxarticles'  ),
        ('test_r31','test_s3','test_c1','oxcategories'),  ('test_r32','test_s3','test_c2','oxcategories'),
        ('test_r41','test_s4','test_c1','oxcategories'),  ('test_r42','test_s4','test_c2','oxcategories'),

        ('test_r51','test_s5','test_a1','oxarticles'  ),  ('test_r52','test_s5','test_a2','oxarticles'  ),
        ('test_r61','test_s6','test_a1','oxarticles'  ),  ('test_r62','test_s6','test_a2','oxarticles'  ),
        ('test_r71','test_s7','test_c1','oxcategories'),  ('test_r72','test_s7','test_c2','oxcategories'),
        ('test_r81','test_s8','test_c1','oxcategories'),  ('test_r82','test_s8','test_c2','oxcategories');";

        $sInsertOrder = "
        INSERT INTO `oxorder`
        (`OXID`, `OXSHOPID`, `OXUSERID`, `OXORDERDATE`,`OXORDERNR`, `OXTOTALNETSUM`,`OXTOTALBRUTSUM`,`OXVOUCHERDISCOUNT`,`OXTOTALORDERSUM`, `OXCURRENCY`, `OXCURRATE`, `OXLANG`)
        VALUES
        ('test_o1',$sShopId,'oxdefaultadmin',NOW(),1001,'751','751','100','651','EUR','1', '0'),
        ('test_o2',$sShopId,'oxdefaultadmin',NOW(),1002,'751','751','30' ,'721','EUR','1', '0'),
        ('test_o3',$sShopId,'oxdefaultadmin',NOW(),1003,'551','551','30' ,'521','EUR','1', '0'),
        ('test_o4',$sShopId,'oxdefaultadmin',NOW(),1004,'501','501','40' ,'461','EUR','1', '0'),
        ('test_o5',$sShopId,'oxdefaultadmin',NOW(),1005,'751','751','15' ,'736','EUR','1', '0'),
        ('test_o6',$sShopId,'oxdefaultadmin',NOW(),1006,'751','751','6'  ,'745','EUR','1', '0'),
        ('test_o7',$sShopId,'oxdefaultadmin',NOW(),1007,'551','551','24' ,'527','EUR','1', '0'),
        ('test_o8',$sShopId,'oxdefaultadmin',NOW(),1008,'501','501','28' ,'473','EUR','1', '0');";

        $sInsertOrderArticles = "
        INSERT INTO `oxorderarticles`
        (`OXID`, `OXORDERID`,`OXARTID`,`OXAMOUNT`, `OXNETPRICE`,`OXBRUTPRICE`, `OXVATPRICE`, `OXVAT`, `OXPRICE`,`OXBPRICE`,`OXNPRICE`, `OXORDERSHOPID`)
        VALUES
        ('test_i10','test_o1','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i11','test_o1','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i12','test_o1','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i13','test_o1','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i14','test_o1','test_a4','5','200','200','0','0','40','40','40',$sShopId),
        ('test_i15','test_o1','test_a5','5','250','250','0','0','50','50','50',$sShopId),

        ('test_i20','test_o2','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i21','test_o2','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i22','test_o2','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i23','test_o2','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i24','test_o2','test_a4','5','200','200','0','0','40','40','40',$sShopId),
        ('test_i25','test_o2','test_a5','5','250','250','0','0','50','50','50',$sShopId),

        ('test_i30','test_o3','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i31','test_o3','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i32','test_o3','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i33','test_o3','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i35','test_o3','test_a5','5','250','250','0','0','50','50','50',$sShopId),

        ('test_i40','test_o4','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i41','test_o4','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i42','test_o4','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i43','test_o4','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i44','test_o4','test_a4','5','200','200','0','0','40','40','40',$sShopId),

        ('test_i50','test_o5','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i51','test_o5','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i52','test_o5','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i53','test_o5','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i54','test_o5','test_a4','5','200','200','0','0','40','40','40',$sShopId),
        ('test_i55','test_o5','test_a5','5','250','250','0','0','50','50','50',$sShopId),

        ('test_i60','test_o6','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i61','test_o6','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i62','test_o6','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i63','test_o6','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i64','test_o6','test_a4','5','200','200','0','0','40','40','40',$sShopId),
        ('test_i65','test_o6','test_a5','5','250','250','0','0','50','50','50',$sShopId),

        ('test_i70','test_o7','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i71','test_o7','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i72','test_o7','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i73','test_o7','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i75','test_o7','test_a5','5','250','250','0','0','50','50','50',$sShopId),

        ('test_i80','test_o8','test_a0','1','1'  ,'1'  ,'0','0','1' ,'1' ,'1' ,$sShopId),
        ('test_i81','test_o8','test_a1','5','50' ,'50' ,'0','0','10','10','10',$sShopId),
        ('test_i82','test_o8','test_a2','5','100','100','0','0','20','20','20',$sShopId),
        ('test_i83','test_o8','test_a3','5','150','150','0','0','30','30','30',$sShopId),
        ('test_i84','test_o8','test_a4','5','200','200','0','0','40','40','40',$sShopId);";


        $this->addToDatabase($sInsertVouchers, 'oxvouchers');
        $this->addToDatabase($sInsertVoucherReleations, 'oxobject2discount');
        $this->addToDatabase($sInsertOrder, 'oxorder');
        $this->addToDatabase($sInsertOrderArticles, 'oxorderarticles');
    }

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $sDeleteSeries            = "DELETE FROM `oxvoucherseries`   WHERE `OXID` LIKE 'test_%';";
        $sDeleteVouchers          = "DELETE FROM `oxvouchers`        WHERE `OXID` LIKE 'test_%';";
        $sDeleteArticles          = "DELETE FROM `oxarticles`        WHERE `OXID` LIKE 'test_%';";
        $sDeleteCategories        = "DELETE FROM `oxcategories`      WHERE `OXID` LIKE 'test_%';";
        $sDeleteCategoryRelations = "DELETE FROM `oxobject2category` WHERE `OXID` LIKE 'test_%';";
        $sDeleteVoucherRelations  = "DELETE FROM `oxobject2discount` WHERE `OXID` LIKE 'test_%';";
        $sDeleteOrder             = "DELETE FROM `oxorder`           WHERE `OXID` LIKE 'test_%';";
        $sDeleteOrderArticles     = "DELETE FROM `oxorderarticles`   WHERE `OXID` LIKE 'test_%';";

        oxDb::getDb()->execute($sDeleteSeries);
        oxDb::getDb()->execute($sDeleteVouchers);
        oxDb::getDb()->execute($sDeleteArticles);
        oxDb::getDb()->execute($sDeleteCategories);
        oxDb::getDb()->execute($sDeleteCategoryRelations);
        oxDb::getDb()->execute($sDeleteVoucherRelations);
        oxDb::getDb()->execute($sDeleteOrder);
        oxDb::getDb()->execute($sDeleteOrderArticles);
    }

    /**
     * Test is product voucher.
     *
     * @return null
     */
    public function testIsProductVoucher()
    {
        // Regular
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_000');
        $this->assertFalse($oVoucher->UNITisProductVoucher());

        // Product
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_111');
        $this->assertTrue($oVoucher->UNITisProductVoucher());

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_222');
        $this->assertTrue($oVoucher->UNITisProductVoucher());

        // Category
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_333');
        $this->assertFalse($oVoucher->UNITisProductVoucher());

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_444');
        $this->assertFalse($oVoucher->UNITisProductVoucher());
    }

    /**
     * Test is category voucher.
     *
     * @return null
     */
    public function testIsCategoryVoucher()
    {
        // Regular
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_000');
        $this->assertFalse($oVoucher->UNITisCategoryVoucher());

        // Product
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_111');
        $this->assertFalse($oVoucher->UNITisCategoryVoucher());

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_222');
        $this->assertFalse($oVoucher->UNITisCategoryVoucher());

        // Category
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_333');
        $this->assertTrue($oVoucher->UNITisCategoryVoucher());

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_444');
        $this->assertTrue($oVoucher->UNITisCategoryVoucher());
    }

    /**
     * Test get regular voucher discount value.
     *
     * @return null
     */
    public function testGetDiscountValue_Regular()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_000');

        $dDiscount = $oVoucher->getDiscountValue(100);

        $this->assertEquals(50, $dDiscount);
    }

    /**
     * Test get product voucher discount value with no assigned articles.
     *
     * @return null
     */
    public function testGetProductDiscountValue_ThrowNoArticleException()
    {
        $oVoucher = $this->getMock(\OxidEsales\Eshop\Application\Model\Voucher::class, array('isAdmin'));
        $oVoucher->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oVoucher->load('test_111');

        // there are no items in basket matching this discount, expecting exception
        $this->expectException('oxVoucherException');
        $this->expectExceptionMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
        $oVoucher->getDiscountValue(100);
    }

    /**
     * Test get category voucher discount value with no assigned articles.
     *
     * @return null
     */
    public function testGetCategoryDiscountValue_ThrowNoArticleException()
    {
        $oVoucher = $this->getMock(\OxidEsales\Eshop\Application\Model\Voucher::class, array('isAdmin'));
        $oVoucher->expects($this->any())->method('isAdmin')->will($this->returnValue(false));
        $oVoucher->load('test_333');

        // there are no items in basket matching this discount, expecting exception
        $this->expectException('oxVoucherException');
        $this->expectExceptionMessage('ERROR_MESSAGE_VOUCHER_NOVOUCHER');
        $oVoucher->getDiscountValue(100);
    }

    /**
     * Test get product voucher discount value.
     *
     * @return null
     */
    public function testGetProductDiscountValue_DoNotThrowNoArticleException()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_111');

        $oVoucher->setAdminMode(true);

        // there are no items in basket matching this discount, expecting exception
        try {
            $oVoucher->getDiscountValue(100);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->fail('exception thrown');
        }
    }

    /**
     * Test get category voucher discount value.
     *
     * @return null
     */
    public function testGetCategoryDiscountValue_DoNotThrowNoArticleException()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_333');

        $oVoucher->setAdminMode(true);

        // there are no items in basket matching this discount, expecting exception
        try {
            $oVoucher->getDiscountValue(100);
        } catch (\OxidEsales\EshopCommunity\Core\Exception\VoucherException $oEx) {
            $this->fail('exception thrown');
        }
    }


    /**
     * Test get series discount.
     *
     * @return null
     */
    public function testGetSeriesDiscount()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_222');

        $oSeries = $oVoucher->getSerie();
        $oDiscount = $oVoucher->UNITgetSerieDiscount();

        $sDiscountType = $oSeries->oxvoucherseries__oxdiscounttype->value == 'percent' ? '%' : 'abs';

        $this->assertEquals($oSeries->getId(), $oDiscount->getId());
        $this->assertEquals($oSeries->oxvoucherseries__oxshopid->value, $oDiscount->oxdiscount__oxshopid->value);
        $this->assertEquals(true, $oDiscount->oxdiscount__oxactive->value);
        $this->assertEquals($oSeries->oxvoucherseries__oxbegindate->value, $oDiscount->oxdiscount__oxactivefrom->value);
        $this->assertEquals($oSeries->oxvoucherseries__oxenddate->value, $oDiscount->oxdiscount__oxactiveto->value);
        $this->assertEquals($oSeries->oxvoucherseries__oxserienr->value, $oDiscount->oxdiscount__oxtitle->value);
        $this->assertEquals(1, $oDiscount->oxdiscount__oxamount->value);
        $this->assertEquals(MAX_64BIT_INTEGER, $oDiscount->oxdiscount__oxamountto->value);
        $this->assertEquals(0, $oDiscount->oxdiscount__oxprice->value);
        $this->assertEquals(MAX_64BIT_INTEGER, $oDiscount->oxdiscount__oxpriceto->value);
        $this->assertEquals($sDiscountType, $oDiscount->oxdiscount__oxaddsumtype->value);
        $this->assertEquals($oSeries->oxvoucherseries__oxdiscount->value, $oDiscount->oxdiscount__oxaddsum->value);
        $this->assertEquals(null, $oDiscount->oxdiscount__oxitmartid->value);
        $this->assertEquals(null, $oDiscount->oxdiscount__oxitmamount->value);
        $this->assertEquals(null, $oDiscount->oxdiscount__oxitmmultiple->value);

        $this->assertEquals(20, $oDiscount->getAbsValue(100));
    }

    /**
     * Test session basket case S1.
     *
     * @return null
     */
    public function testSessionBasketCase_s1()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_111');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '10',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '10',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 100;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('111');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S2.
     *
     * @return null
     */
    public function testSessionBasketCase_s2()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_222');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '20',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '20',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 10;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('222');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S3.
     *
     * @return null
     */
    public function testSessionBasketCase_s3()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 0); // XX
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_333');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '30',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '30',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 30;
        $iTotalBasket = 551;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('333');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S4.
     *
     * @return null
     */
    public function testSessionBasketCase_s4()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 0); // XX

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_444');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '40',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a4',
                'price'    => '40',
                'discount' => '40',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 40;
        $iTotalBasket = 501;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('444');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S5.
     *
     * @return null
     */
    public function testSessionBasketCase_s5()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_555');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '1',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '2',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 15;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('555');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S6.
     *
     * @return null
     */
    public function testSessionBasketCase_s6()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_666');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '2',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '4',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 2;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('666');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S7.
     *
     * @return null
     */
    public function testSessionBasketCase_s7()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 0); // XX
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_777');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '9',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '15',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 120;
        $iTotalBasket = 551;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('777');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case S8.
     *
     * @return null
     */
    public function testSessionBasketCase_s8()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 0); // XX

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_888');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '12',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a4',
                'price'    => '40',
                'discount' => '16',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 140;
        $iTotalBasket = 501;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));

        $oBasket->addVoucher('888');
        $oBasket->calculateBasket();

        $this->assertEquals($iExpDiscount, $oBasket->getVoucherDiscValue());
    }

    /**
     * Test session basket case X1.
     *
     * @return null
     */
    public function testSessionBasketCase_x1()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_100');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '1000',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 250;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test session basket case X2.
     *
     * @return null
     */
    public function testSessionBasketCase_x2()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_200');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a4',
                'price'    => '40',
                'discount' => '1000',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '1000',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 450;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test session basket case X3.
     *
     * @return null
     */
    public function testSessionBasketCase_x3()
    {
        $oBasket = oxNew('oxBasket');
        $oBasket->addToBasket('test_a0', 1);
        $oBasket->addToBasket('test_a1', 5);
        $oBasket->addToBasket('test_a2', 5);
        $oBasket->addToBasket('test_a3', 5);
        $oBasket->addToBasket('test_a4', 5);
        $oBasket->addToBasket('test_a5', 5);

        $this->getSession()->setBasket($oBasket);

        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_300');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '1000',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetSessionBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 50;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S1.
     *
     * @return null
     */
    public function testOrderBasketCase_s1()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_111111');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '10',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '10',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 100;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S2.
     *
     * @return null
     */
    public function testOrderBasketCase_s2()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_222222');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '20',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '20',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 10;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S3.
     *
     * @return null
     */
    public function testOrderBasketCase_s3()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_333333');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '30',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '30',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 30;
        $iTotalBasket = 551;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S4.
     *
     * @return null
     */
    public function testOrderBasketCase_s4()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_444444');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '40',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a4',
                'price'    => '40',
                'discount' => '40',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 40;
        $iTotalBasket = 501;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S5.
     *
     * @return null
     */
    public function testOrderBasketCase_s5()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_555555');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '1',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '2',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 15;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S6.
     *
     * @return null
     */
    public function testOrderBasketCase_s6()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_666666');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a1',
                'price'    => '10',
                'discount' => '2',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a2',
                'price'    => '20',
                'discount' => '4',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 2;
        $iTotalBasket = 751;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetProductDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S7.
     *
     * @return null
     */
    public function testOrderBasketCase_s7()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_777777');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '9',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a5',
                'price'    => '50',
                'discount' => '15',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 120;
        $iTotalBasket = 551;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }

    /**
     * Test order basket case S8.
     *
     * @return null
     */
    public function testOrderBasketCase_s8()
    {
        $oVoucher = oxNew('oxVoucher');
        $oVoucher->load('test_888888');

        $aExpItems = array(
            array(
                'oxid'     => 'test_a3',
                'price'    => '30',
                'discount' => '12',
                'amount'   => '5'
            ),
            array(
                'oxid'     => 'test_a4',
                'price'    => '40',
                'discount' => '16',
                'amount'   => '5'
            )
        );

        $this->assertEquals($aExpItems, $oVoucher->UNITgetOrderBasketItems());
        $this->assertEquals($aExpItems, $oVoucher->UNITgetBasketItems());

        $iExpDiscount = 140;
        $iTotalBasket = 501;

        $this->assertEquals($iExpDiscount, $oVoucher->UNITgetCategoryDiscoutValue($iTotalBasket));
        $this->assertEquals($iExpDiscount, $oVoucher->getDiscountValue($iTotalBasket));
    }
}
