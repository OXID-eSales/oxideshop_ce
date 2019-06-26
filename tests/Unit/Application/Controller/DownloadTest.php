<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Application\Controller;

use \exception;
use \oxTestModules;

/**
 * Tests for Download class
 */
class DownloadTest extends \OxidTestCase
{


    /**
     * Test get article list.
     *
     * @return null
     */
    public function testRender()
    {
        $this->setRequestParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, array('load', 'processOrderFile', 'getFileId'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        $oOrderFile->expects($this->any())->method('getFileId')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'download', 'exist'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('exist')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('download');
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oDownloads = oxNew('Download');

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
            $oDownloads = oxNew('Download');
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
        $this->setRequestParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, array('load', 'processOrderFile'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'download'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        oxTestModules::addModuleObject('oxFile', $oFile);
        try {
            $oDownloads = oxNew('Download');
            ;
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
        $this->setRequestParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, array('load', 'processOrderFile'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        try {
            $oDownloads = oxNew('Download');
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
        $this->setRequestParameter('sorderfileid', "_testOrderFile");

        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, array('load', 'processOrderFile', 'getFileId'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue('_fileId'));
        $oOrderFile->expects($this->any())->method('getFileId')->will($this->returnValue('_fileId'));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'download', 'exist'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('exist')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('download')->will($this->throwException(oxNew('oxException')));
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oException = oxNew('oxExceptionToDisplay');
        $oException->setMessage("ERROR_MESSAGE_FILE_DOWNLOAD_FAILED");
        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        try {
            $oDownloads = oxNew('Download');
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

        $this->setRequestParameter('sorderfileid', "_testOrderFile");

        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, array('load', 'processOrderFile', 'getFileId'));
        $oOrderFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oOrderFile->expects($this->any())->method('getFileId')->will($this->returnValue('_fileId'));
        $oOrderFile->expects($this->any())->method('processOrderFile')->will($this->returnValue(false));
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, array('load', 'download', 'exist'));
        $oFile->expects($this->any())->method('load')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('exist')->will($this->returnValue(true));
        $oFile->expects($this->any())->method('download');
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oException = oxNew('oxExceptionToDisplay');
        $oException->setMessage("ERROR_MESSAGE_FILE_DOESNOT_EXIST");
        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, array('addErrorToDisplay'));
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oDownloads = oxNew('Download');
        $oDownloads->render();
    }
}
