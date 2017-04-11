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

/**
 * Testing oxPdf class.
 */
class Unit_Core_oxpdfTest extends OxidTestCase
{

    /**
     * oxPdf::PutLink() test case
     *
     * @return null
     */
    public function testPutLink()
    {
        $oPdf = $this->getMock("oxPdf", array("SetTextColor", "SetStyle", "Write"));
        $oPdf->expects($this->at(0))->method('SetTextColor')->with($this->equalTo(0), $this->equalTo(0), $this->equalTo(255));
        $oPdf->expects($this->at(1))->method('SetStyle')->with($this->equalTo('U'), $this->equalTo(true));
        $oPdf->expects($this->at(2))->method('Write')->with($this->equalTo(5), $this->equalTo("testText"), $this->equalTo("testUrl"));
        $oPdf->expects($this->at(3))->method('SetStyle')->with($this->equalTo('U'), $this->equalTo(false));
        $oPdf->expects($this->at(0))->method('SetTextColor')->with($this->equalTo(0));
        $oPdf->PutLink("testUrl", "testText");
    }

    /**
     * oxPdf::SetStyle() test case
     *
     * @return null
     */
    public function testSetStyle()
    {
        $oPdf = $this->getMock("oxPdf", array("SetFont"));
        $oPdf->expects($this->once())->method('SetFont')->with($this->equalTo(''));
        $oPdf->SetStyle("testTag", true);
    }

    /**
     * oxPdf::CloseTag() test case
     *
     * @return null
     */
    public function testCloseTag()
    {
        $oPdf = $this->getMock("oxPdf", array("SetStyle"));
        $oPdf->expects($this->once())->method('SetStyle')->with($this->equalTo('B'), $this->equalTo(false));
        $oPdf->CloseTag("B");
        $oPdf->CloseTag("A");
    }

    /**
     * oxPdf::OpenTag() test case
     *
     * @return null
     */
    public function testOpenTag()
    {
        $oPdf = $this->getMock("oxPdf", array("SetStyle", "Ln"));
        $oPdf->expects($this->once())->method('SetStyle')->with($this->equalTo('B'), $this->equalTo(true));
        $oPdf->expects($this->once())->method('Ln')->with($this->equalTo(5));
        $oPdf->OpenTag("B", "");
        $oPdf->OpenTag("A", "");
        $oPdf->OpenTag("BR", "");
    }

    /**
     * oxPdf::WriteHTML() test case
     *
     * @return null
     */
    public function testWriteHTML()
    {
        $sHtml = '<a href="aaa" style="bbb"></a><div></div>';
        $oPdf = $this->getMock("oxPdf", array("PutLink", "Write", "CloseTag", "OpenTag"));
        $oPdf->expects($this->never())->method('PutLink');
        $oPdf->expects($this->atLeastOnce())->method('Write');
        $oPdf->expects($this->atLeastOnce())->method('CloseTag');
        $oPdf->expects($this->atLeastOnce())->method('OpenTag');
        $oPdf->Text(0, 0, "text");
        $oPdf->SetFont("Arial");
        $oPdf->WriteHTML($sHtml);
    }
}
