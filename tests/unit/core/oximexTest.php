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

class Unit_Core_oxImexTest extends OxidTestCase
{

    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown()
    {
        $this->cleanUpTable('oxorder');
        $this->cleanUpTable('oxorderarticles');
        parent::tearDown();
    }

    public function test_exportLexwareArticles()
    {
        $myConfig = oxRegistry::getConfig();
        //$sFile = $myConfig->sShopDir.'/tmp/test.xpr';
        $sFile = oxregistry::get("oxConfigFile")->getVar("sCompileDir") . '/test.xpr';
        @unlink($sFile);
        $oImex = new oxImex();
        if (!$oImex->exportLexwareArticles(0, 1000, $sFile)) {
            $this->fail("error exporting lexware");
        }
        $sContents = file_get_contents($sFile);
        @unlink($sFile);
        // we have full list [hopefully] of exported articles
        $aContents = explode("\n", str_replace("\r", '', $sContents));
        // check header
        $this->assertEquals(
            '"Artikelnummer";"Bezeichnung";"Einheit";"Gewicht";"Matchcode";"Preis pro Anzahl";'
            . '"Warengruppe";"Warengr.-Kurzbez.";"Warengr.-Steuersatz";"Warengr.-Konto Inland";'
            . '"Warengr.-Konto Ausland";"Warengr.-Konto EG";"Preis 1";"Preis 2";"Preis 3";'
            . '"Preis I/1";"Preis I/2";"Preis I/3";"Preis II/1";"Preis II/2";"Preis II/3";"Preis III/1";'
            . '"Preis III/2";"Preis III/3";"B/N";"Lagerartikel";"EK 1";"Währung EK1";"EK 2";'
            . '"Währung EK2";"Staffelmenge 1";"Staffelmenge 2";"Staffelmenge 3";"Lieferantennummer 1";'
            . '"Lieferantennummer 2";"Bestellmenge Lf.1";"Bestellmenge Lf.2";"Bestellnr. Lf.1";'
            . '"Bestellnr. Lf.2";"Lieferzeit Lf.1";"Lieferzeit Lf.2";"Lagerbestand";"Mindestbestand";'
            . '"Lagerort";"Bestellte Menge";"Stückliste";"Internet";"Text"', $aContents[0]
        );
        $blFound = false;
        foreach ($aContents as $content) {
            if (strpos($content, '2000;Wanduhr ROBOT') === 0) {
                $this->assertEquals(
                    '2000;Wanduhr ROBOT ;Stueck;0;2000;1,000;;;;;;;29.00;;;;;;;;;;;;;;0.00;;;;;;;;;'
                    . ';;;;;;2;;;;;1; Wanduhr im coolen ROBOTER Look! Durchmesser: 40 cm Material: Glas '
                    . 'Bezugshinweis: bei Interesse können Sie dieses Produkt bei www.desaster.com erwerben.;', $content
                );
                $blFound = true;
                break;
            }
        }
        $this->assertEquals(true, $blFound);
    }

    public function testInterFormSimple()
    {
        $oImex = new oxImex();
        $this->assertEquals("abra!@#$%^&*()_\"\"cadabra'  \t", $oImex->InterFormSimple("abra!@#$%^&*()_\"cadabra'\r\n \t"));
    }

    public function testInterForm()
    {
        $oImex = new oxImex();
        $this->assertEquals("abra!@#ü &amp; $%^*''_'cadabra' ", $oImex->InterForm("abra<br />!@#&uuml; & $%^*()_\"cadabra'\r\n "));
        $this->assertEquals("abra&amp;cadabra", $oImex->InterForm("abra&cadabra"));
        $o = new stdClass;
        $o->fldtype = "text";
        $this->assertEquals("abra<br />!@#ü &amp; $%^*''_'cadabra' \t", $oImex->InterForm("abra<br />!@#&uuml; & $%^*()_\"cadabra'\r\n \t", $o));
    }

    public function testInternPrice()
    {
        $oImex = new oxImex();
        $this->assertEquals("5.00", $oImex->InternPrice("5,5"));
        $this->assertEquals("1.23", $oImex->InternPrice("1.233"));
        $this->assertEquals("1.21", $oImex->InternPrice("1.205"));
        $this->assertEquals("0.00", $oImex->InternPrice("zxc"));
    }

    public function testExportLexwareOrdersEmptyOrderList()
    {
        $oImex = new oxImex();
        $this->assertNull($oImex->exportLexwareOrders(9991, 9991));

    }

    public function testExportLexwareOrders()
    {
        $myConfig = oxRegistry::getConfig();

        $oOrder = new oxOrder();
        $oOrder->setId('_testOrder');
        $oOrder->oxorder__oxshopid = new oxField($myConfig->getBaseShopId());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2007-02-21 00:00:00');
        $oOrder->oxorder__oxordernr = new oxField('9991');
        $oOrder->oxorder__oxbillnr = new oxField('15');
        $oOrder->oxorder__oxbillcompany = new oxField('billcomp');
        $oOrder->oxorder__oxbillemail = new oxField('billemail');
        $oOrder->oxorder__oxbillfname = new oxField('billfname');
        $oOrder->oxorder__oxbilllname = new oxField('billlname');
        $oOrder->oxorder__oxbillstreet = new oxField('billstreet');
        $oOrder->oxorder__oxbillstreetnr = new oxField('billstnr');
        $oOrder->oxorder__oxbilladdinfo = new oxField('billaddinfo');
        $oOrder->oxorder__oxbillustid = new oxField('billustid');
        $oOrder->oxorder__oxbillcity = new oxField('billcity');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('billzip');
        $oOrder->oxorder__oxbillfon = new oxField('billfon');
        $oOrder->oxorder__oxbillfax = new oxField('billfax');
        $oOrder->oxorder__oxbillsal = new oxField('MR');
        $oOrder->oxorder__oxpaymentid = new oxField('oxempty');
        $oOrder->oxorder__oxdelcost = new oxField('1');
        $oOrder->oxorder__oxdelvat = new oxField('2');
        $oOrder->oxorder__oxpaycost = new oxField('3');
        $oOrder->oxorder__oxpayvat = new oxField('4');
        $oOrder->oxorder__oxwrapcost = new oxField('5');
        $oOrder->oxorder__oxwrapvat = new oxField('6');

        $oOrder->oxorder__oxdelcompany = new oxField('delcomp');
        $oOrder->oxorder__oxdelfname = new oxField('delfname');
        $oOrder->oxorder__oxdellname = new oxField('dellname');
        $oOrder->oxorder__oxdelstreet = new oxField('delstreet');
        $oOrder->oxorder__oxdelstreetnr = new oxField('delstnr');
        $oOrder->oxorder__oxdelzip = new oxField('delzip');
        $oOrder->oxorder__oxdelcity = new oxField('delcity');
        $oOrder->oxorder__oxdelcountry = new oxField('a7c40f631fc920687.20179984');

        $oOrder->save();

        // one test order article
        $oOrderArt = new oxOrderArticle();
        $oOrderArt->setId('_testOrderArticle');
        $oOrderArt->oxorderarticles__oxorderid = new oxField('_testOrder');
        $oOrderArt->oxorderarticles__oxvat = new oxField(19);
        $oOrderArt->oxorderarticles__oxartnum = new oxField('1126');
        $oOrderArt->oxorderarticles__oxamount = new oxField(1);
        $oOrderArt->oxorderarticles__oxtitle = new oxField('Bar-Set ABSINTH');
        $oOrderArt->oxorderarticles__oxselvariant = new oxField('oxselvariant');
        $oOrderArt->oxorderarticles__oxnetprice = new oxField(28.57);
        $oOrderArt->oxorderarticles__oxbrutprice = new oxField(34);
        $oOrderArt->save();

        $myConfig = oxRegistry::getConfig();

        $oImex = new oxImex();
        $sResult = str_replace(array("\r", "   "), '', $oImex->exportLexwareOrders(9991, 9991));
        $this->assertEquals(
            "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<Bestellliste>\n<Bestellung zurückgestellt=\"Nein\" bearbeitet=\"Nein\" übertragen=\"Nein\">\n"
            . "<Bestellnummer>9991</Bestellnummer>\n<Rechnungsnummer>15</Rechnungsnummer>\n<Standardwaehrung>978</Standardwaehrung>\n<Bestelldatum>\n<Datum>21.02.2007</Datum>\n<Zeit>00:00:00</Zeit>\n</Bestelldatum>\n<Kunde>\n<Kundennummer></Kundennummer>\n"
            . "<Firmenname>billcomp</Firmenname>\n<Anrede>Herr</Anrede>\n<Vorname>billfname</Vorname>\n<Name>billlname</Name>\n<Strasse>billstreet billstnr</Strasse>\n"
            . "<PLZ>billzip</PLZ>\n<Ort>billcity</Ort>\n<Bundesland></Bundesland>\n<Land>Deutschland</Land>\n<Email>billemail</Email>\n<Telefon>billfon</Telefon>\n<Telefon2></Telefon2>\n"
            . "<Fax>billfax</Fax>\n<Lieferadresse>\n<Firmenname>delcomp</Firmenname>\n<Vorname>delfname</Vorname>\n<Name>dellname</Name>\n<Strasse>delstreet delstnr</Strasse>\n<PLZ>delzip</PLZ>\n<Ort>delcity</Ort>\n<Bundesland></Bundesland>\n"
            . "<Land></Land>\n</Lieferadresse>\n<Matchcode>billlname, billfname</Matchcode>\n<fSteuerbar>ja</fSteuerbar>\n</Kunde>\n<Artikelliste>\n<Artikel>\n<Artikelzusatzinfo><Nettostaffelpreis></Nettostaffelpreis></Artikelzusatzinfo>\n"
            . "<SteuersatzID></SteuersatzID>\n<Steuersatz>0.19</Steuersatz>\n<Artikelnummer>1126</Artikelnummer>\n<Anzahl>1</Anzahl>\n<Produktname>Bar-Set ABSINTH/oxselvariant</Produktname>\n"
            . "<Rabatt>0.00</Rabatt>\n<Preis>34.00</Preis>\n</Artikel>\n<GesamtRabatt>0.00</GesamtRabatt>\n<GesamtNetto>28.57</GesamtNetto>\n"
            . "<Lieferkosten>1.00</Lieferkosten>\n<Zahlungsartkosten>3.00</Zahlungsartkosten>\n<GesamtBrutto>34.00</GesamtBrutto>\n<Bemerkung></Bemerkung>\n</Artikelliste>\n<Zahlung>\n<Art></Art>\n</Zahlung>\n</Bestellung>\n</Bestellliste>\n", $sResult
        );
    }

    public function testExportLexwareOrdersDiffCurrency()
    {
        $myConfig = oxRegistry::getConfig();

        $oOrder = new oxOrder();
        $oOrder->setId('_testOrder');
        $oOrder->oxorder__oxshopid = new oxField($myConfig->getBaseShopId());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2007-02-21 00:00:00');
        $oOrder->oxorder__oxordernr = new oxField('9991');
        $oOrder->oxorder__oxbillnr = new oxField('15');
        $oOrder->oxorder__oxbillcompany = new oxField('billcomp');
        $oOrder->oxorder__oxbillemail = new oxField('billemail');
        $oOrder->oxorder__oxbillfname = new oxField('billfname');
        $oOrder->oxorder__oxbilllname = new oxField('billlname');
        $oOrder->oxorder__oxbillstreet = new oxField('billstreet');
        $oOrder->oxorder__oxbillstreetnr = new oxField('billstnr');
        $oOrder->oxorder__oxbilladdinfo = new oxField('billaddinfo');
        $oOrder->oxorder__oxbillustid = new oxField('billustid');
        $oOrder->oxorder__oxbillcity = new oxField('billcity');
        $oOrder->oxorder__oxbillcountryid = new oxField('a7c40f631fc920687.20179984');
        $oOrder->oxorder__oxbillzip = new oxField('billzip');
        $oOrder->oxorder__oxbillfon = new oxField('billfon');
        $oOrder->oxorder__oxbillfax = new oxField('billfax');
        $oOrder->oxorder__oxbillsal = new oxField('MR');
        $oOrder->oxorder__oxpaymentid = new oxField('oxempty');
        $oOrder->oxorder__oxdelcost = new oxField('1');
        $oOrder->oxorder__oxdelvat = new oxField('2');
        $oOrder->oxorder__oxpaycost = new oxField('3');
        $oOrder->oxorder__oxpayvat = new oxField('4');
        $oOrder->oxorder__oxwrapcost = new oxField('5');
        $oOrder->oxorder__oxwrapvat = new oxField('6');

        $oOrder->oxorder__oxdelcompany = new oxField('delcomp');
        $oOrder->oxorder__oxdelfname = new oxField('delfname');
        $oOrder->oxorder__oxdellname = new oxField('dellname');
        $oOrder->oxorder__oxdelstreet = new oxField('delstreet');
        $oOrder->oxorder__oxdelstreetnr = new oxField('delstnr');
        $oOrder->oxorder__oxdelzip = new oxField('delzip');
        $oOrder->oxorder__oxdelcity = new oxField('delcity');
        $oOrder->oxorder__oxdelcountry = new oxField('a7c40f631fc920687.20179984');

        $oOrder->oxorder__oxcurrate = new oxField(2.15);

        $oOrder->save();

        // one test order article
        $oOrderArt = new oxOrderArticle();
        $oOrderArt->setId('_testOrderArticle');
        $oOrderArt->oxorderarticles__oxorderid = new oxField('_testOrder');
        $oOrderArt->oxorderarticles__oxvat = new oxField(19);
        $oOrderArt->oxorderarticles__oxartnum = new oxField('1126');
        $oOrderArt->oxorderarticles__oxamount = new oxField(1);
        $oOrderArt->oxorderarticles__oxtitle = new oxField('Bar-Set ABSINTH');
        $oOrderArt->oxorderarticles__oxselvariant = new oxField('oxselvariant');
        $oOrderArt->oxorderarticles__oxnetprice = new oxField(28.57);
        $oOrderArt->oxorderarticles__oxbrutprice = new oxField(34);
        $oOrderArt->save();

        $myConfig = oxRegistry::getConfig();

        $oImex = new oxImex();
        $sResult = str_replace(array("\r", "   "), '', $oImex->exportLexwareOrders(9991, 9991));
        $this->assertEquals(
            "<?xml version=\"1.0\" encoding=\"ISO-8859-15\"?>\n<Bestellliste>\n<Bestellung zurückgestellt=\"Nein\" bearbeitet=\"Nein\" übertragen=\"Nein\">\n"
            . "<Bestellnummer>9991</Bestellnummer>\n<Rechnungsnummer>15</Rechnungsnummer>\n<Standardwaehrung>978</Standardwaehrung>\n<Bestelldatum>\n<Datum>21.02.2007</Datum>\n<Zeit>00:00:00</Zeit>\n</Bestelldatum>\n<Kunde>\n<Kundennummer></Kundennummer>\n"
            . "<Firmenname>billcomp</Firmenname>\n<Anrede>Herr</Anrede>\n<Vorname>billfname</Vorname>\n<Name>billlname</Name>\n<Strasse>billstreet billstnr</Strasse>\n"
            . "<PLZ>billzip</PLZ>\n<Ort>billcity</Ort>\n<Bundesland></Bundesland>\n<Land>Deutschland</Land>\n<Email>billemail</Email>\n<Telefon>billfon</Telefon>\n<Telefon2></Telefon2>\n"
            . "<Fax>billfax</Fax>\n<Lieferadresse>\n<Firmenname>delcomp</Firmenname>\n<Vorname>delfname</Vorname>\n<Name>dellname</Name>\n<Strasse>delstreet delstnr</Strasse>\n<PLZ>delzip</PLZ>\n<Ort>delcity</Ort>\n<Bundesland></Bundesland>\n"
            . "<Land></Land>\n</Lieferadresse>\n<Matchcode>billlname, billfname</Matchcode>\n<fSteuerbar>ja</fSteuerbar>\n</Kunde>\n<Artikelliste>\n<Artikel>\n<Artikelzusatzinfo><Nettostaffelpreis></Nettostaffelpreis></Artikelzusatzinfo>\n"
            . "<SteuersatzID></SteuersatzID>\n<Steuersatz>0.19</Steuersatz>\n<Artikelnummer>1126</Artikelnummer>\n<Anzahl>1</Anzahl>\n<Produktname>Bar-Set ABSINTH/oxselvariant</Produktname>\n"
            . "<Rabatt>0.00</Rabatt>\n<Preis>15.81</Preis>\n</Artikel>\n<GesamtRabatt>0.00</GesamtRabatt>\n<GesamtNetto>13.29</GesamtNetto>\n"
            . "<Lieferkosten>0.47</Lieferkosten>\n<Zahlungsartkosten>1.40</Zahlungsartkosten>\n<GesamtBrutto>15.81</GesamtBrutto>\n<Bemerkung></Bemerkung>\n</Artikelliste>\n<Zahlung>\n<Art></Art>\n</Zahlung>\n</Bestellung>\n</Bestellliste>\n", $sResult
        );
    }

    public function testExportLexwareOrders_setsCorrectCharset()
    {
        $myConfig = oxRegistry::getConfig();

        $oOrder = new oxOrder();
        $oOrder->setId('_testOrder');
        $oOrder->oxorder__oxshopid = new oxField($myConfig->getBaseShopId());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2007-02-21 00:00:00');
        $oOrder->oxorder__oxordernr = new oxField('9991');
        $oOrder->save();

        $oImex = $this->getMock('oxImex', array('_getCharset'));
        $oImex->expects($this->any())->method('_getCharset')->will($this->returnValue('UTF-8'));

        $sResult = $oImex->exportLexwareOrders(9991, 9991);
        $this->assertTrue((strpos($sResult, "<?xml version=\"1.0\" encoding=\"UTF-8\"?>")) === 0);
    }

    /*
     * Testing if shop is in utf-8 mode, generated xml attributes with special chars
     * are converted to utf-8
     */
    public function testExportLexwareOrders_convertsAttributesSpecChars()
    {
        $myConfig = oxRegistry::getConfig();

        $oOrder = new oxOrder();
        $oOrder->setId('_testOrder');
        $oOrder->oxorder__oxshopid = new oxField($myConfig->getBaseShopId());
        $oOrder->oxorder__oxuserid = new oxField('oxdefaultadmin');
        $oOrder->oxorder__oxorderdate = new oxField('2007-02-21 00:00:00');
        $oOrder->oxorder__oxordernr = new oxField('9991');
        $oOrder->save();

        $oImex = $this->getMock('oxImex', array('_getCharset', '_convertStr'));
        $oImex->expects($this->any())->method('_getCharset')->will($this->returnValue('UTF-8'));
        $oImex->expects($this->at(1))->method('_convertStr')->with($this->equalTo("zur\xfcckgestellt")); // \xfc = ü
        $oImex->expects($this->at(2))->method('_convertStr')->with($this->equalTo("\xfcbertragen")); // \xfc = ü

        $sResult = $oImex->exportLexwareOrders(9991, 9991);
    }

    /*
     * Test converting string from ISO-8859-15 to selected charset
     */
    public function testConvertStr()
    {
        $oImex = $this->getMock('oxImex', array('_getCharset'));
        $oImex->expects($this->any())->method('_getCharset')->will($this->returnValue('UTF-8'));

        $this->assertEquals(iconv('ISO-8859-15', 'UTF-8', "zur\xfcckgestellt"), $oImex->UNITconvertStr("zur\xfcckgestellt"));
    }
}
