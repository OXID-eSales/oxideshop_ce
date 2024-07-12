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
class DownloadTest extends \PHPUnit\Framework\TestCase
{


    /**
     * Test get article list.
     */
    public function testRender()
    {
        $this->setRequestParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, ['load', 'processOrderFile', 'getFileId']);
        $oOrderFile->method('load')->willReturn(true);
        $oOrderFile->method('processOrderFile')->willReturn('_fileId');
        $oOrderFile->method('getFileId')->willReturn('_fileId');
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'download', 'exist']);
        $oFile->method('load')->willReturn(true);
        $oFile->method('exist')->willReturn(true);
        $oFile->method('download');
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oDownloads = oxNew('Download');

        $oDownloads->render();
    }

    /**
     * Test get article list.
     */
    public function testRenderWrongLink()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");

        try {
            $oDownloads = oxNew('Download');
            $oDownloads->render();
        } catch (Exception $exception) {
            $this->assertSame(123, $exception->getCode(), 'Error executing "testRenderWrongLink" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     */
    public function testRenderDownloadFailed()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");
        oxTestModules::addFunction("oxFile", "download", "{ throw new exception( 'testDownload', 123 );}");
        $this->setRequestParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, ['load', 'processOrderFile']);
        $oOrderFile->method('load')->willReturn(true);
        $oOrderFile->method('processOrderFile')->willReturn('_fileId');
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'download']);
        $oFile->method('load')->willReturn(true);
        oxTestModules::addModuleObject('oxFile', $oFile);
        try {
            $oDownloads = oxNew('Download');
            ;
            $oDownloads->render();
        } catch (Exception $exception) {
            $this->assertSame(123, $exception->getCode(), 'Error executing "testRenderWrongLink" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     */
    public function testRenderFileDoesnotExists()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");
        $this->setRequestParameter('sorderfileid', "_testOrderFile");
        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, ['load', 'processOrderFile']);
        $oOrderFile->method('load')->willReturn(true);
        $oOrderFile->method('processOrderFile')->willReturn('_fileId');
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        try {
            $oDownloads = oxNew('Download');
            $oDownloads->render();
        } catch (Exception $exception) {
            $this->assertSame(123, $exception->getCode(), 'Error executing "testRenderWrongLink" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     */
    public function testRenderFailedDownloadingFile()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ throw new exception( 'testDownload', 123 );}");
        $this->setRequestParameter('sorderfileid', "_testOrderFile");

        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, ['load', 'processOrderFile', 'getFileId']);
        $oOrderFile->method('load')->willReturn(true);
        $oOrderFile->method('processOrderFile')->willReturn('_fileId');
        $oOrderFile->method('getFileId')->willReturn('_fileId');
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'download', 'exist']);
        $oFile->method('load')->willReturn(true);
        $oFile->method('exist')->willReturn(true);
        $oFile->method('download')->willThrowException(oxNew('oxException'));
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oException = oxNew('oxExceptionToDisplay');
        $oException->setMessage("ERROR_MESSAGE_FILE_DOWNLOAD_FAILED");

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['addErrorToDisplay']);
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        try {
            $oDownloads = oxNew('Download');
            $oDownloads->render();
        } catch (Exception $exception) {
            $this->assertSame(123, $exception->getCode(), 'Error executing "ERROR_MESSAGE_FILE_DOWNLOAD_FAILED" test');

            return;
        }

        $this->fail('Redirect was not called');
    }

    /**
     * Test get article list.
     */
    public function testRenderDownloadExpired()
    {
        oxTestModules::addFunction("oxUtils", "redirect", "{ return;}");

        $this->setRequestParameter('sorderfileid', "_testOrderFile");

        $oOrderFile = $this->getMock(\OxidEsales\Eshop\Application\Model\OrderFile::class, ['load', 'processOrderFile', 'getFileId']);
        $oOrderFile->method('load')->willReturn(true);
        $oOrderFile->method('getFileId')->willReturn('_fileId');
        $oOrderFile->method('processOrderFile')->willReturn(false);
        oxTestModules::addModuleObject('oxOrderFile', $oOrderFile);

        $oFile = $this->getMock(\OxidEsales\Eshop\Application\Model\File::class, ['load', 'download', 'exist']);
        $oFile->method('load')->willReturn(true);
        $oFile->method('exist')->willReturn(true);
        $oFile->method('download');
        oxTestModules::addModuleObject('oxFile', $oFile);

        $oException = oxNew('oxExceptionToDisplay');
        $oException->setMessage("ERROR_MESSAGE_FILE_DOESNOT_EXIST");

        $oUtilsView = $this->getMock(\OxidEsales\Eshop\Core\UtilsView::class, ['addErrorToDisplay']);
        $oUtilsView->expects($this->once())->method('addErrorToDisplay')->with($oException, false);
        oxTestModules::addModuleObject('oxUtilsView', $oUtilsView);

        $oDownloads = oxNew('Download');
        $oDownloads->render();
    }
}
