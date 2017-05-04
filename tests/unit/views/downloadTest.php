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
 * Tests for Download class
 */
class Unit_Views_downloadTest extends OxidTestCase
{


    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParam('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock('oxOrderFile', array('load', 'processOrderFile', 'getFileId'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        $oOrderFile->expects($this->any())->method('getFileId')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock('oxFile', array('load', 'download', 'exist'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('exist')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('download');
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oDownloads = new Download();

        $oDownloads->render();
    }

    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderWrongLink()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");

        try {
            $oDownloads = new Download();
            $oDownloads->render();
        } catch (Exception $oEx) {
            $this->assertEquals(123, $oEx->getCode(), 'Error executing "testRenderWrongLink" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderDownloadFailed()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");
        oxTestModules::addFunction("oxFile", "download", "{ throw new exception( 'testDownload', 123 );}");
        $this->setRequestParam('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock('oxOrderFile', array('load', 'processOrderFile'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock('oxFile', array('load', 'download'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxFile', $oFile);
        try {
            $oDownloads = new Download();;
            $oDownloads->render();
        } catch (Exception $oEx) {
            $this->assertEquals(123, $oEx->getCode(), 'Error executing "testRenderWrongLink" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderFileDoesnotExists()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");
        $this->setRequestParam('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock('oxOrderFile', array('load', 'processOrderFile'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        try {
            $oDownloads = new Download();
            $oDownloads->render();
        } catch (Exception $oEx) {
            $this->assertEquals(123, $oEx->getCode(), 'Error executing "testRenderWrongLink" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderFailedDownloadingFile()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");
        $this->setRequestParam('sorderfileid', "_testOrderFile");

        $oOrderFile = $this->getMock('oxOrderFile', array('load', 'processOrderFile', 'getFileId'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        $oOrderFile->expects($this->any())->method('getFileId')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock('oxFile', array('load', 'download', 'exist'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('exist')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('download')->will($this->throwException(new oxException));
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oException = new oxExceptionToDisplay();
        $oException->setMessage("ERROR_MESSAGE_FILE_DOWNLOAD_FAILED");
        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        try {
            $oDownloads = new Download();
            $oDownloads->render();
        } catch (Exception $oEx) {
            $this->assertEquals(123, $oEx->getCode(), 'Error executing "ERROR_MESSAGE_FILE_DOWNLOAD_FAILED" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRenderDownloadExpired()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ return;}");

        $this->setRequestParam('sorderfileid', "_testOrderFile");

        $oOrderFile = $this->getMock('oxOrderFile', array('load', 'processOrderFile', 'getFileId'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('getFileId')->will($this->returnValue('_fileId'));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock('oxFile', array('load', 'download', 'exist'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('exist')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('download');
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oException = new oxExceptionToDisplay();
        $oException->setMessage("ERROR_MESSAGE_FILE_DOESNOT_EXIST");
        $oUtilsView = $this->getMock('oxUtilsView', array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oDownloads = new Download();
        $oDownloads->render();
    }

}