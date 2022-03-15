<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Models;

use OxidEsales\Eshop\Application\Model\SeoEncoderVendor;
use OxidEsales\Eshop\Application\Model\Vendor;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\TestingLibrary\UnitTestCase;

class VendorTest extends UnitTestCase
{
    /**
     * Tear down the fixture.
     *
     * @return null
     */
    protected function tearDown(): void
    {
        //this method removes all rows where column 'oxid' start with an underscore
        $this->cleanUpTable('oxvendor', 'oxid');

        parent::tearDown();
    }

    public function testDelete(): void
    {
        $seoEncoderVendorMock = $this->createPartialMock(SeoEncoderVendor::class, ['onDeleteVendor']);
        Registry::set(SeoEncoderVendor::class, $seoEncoderVendorMock);

        $id = '_testId';
        $vendor = oxNew(Vendor::class);
        $vendor->setId($id);
        $vendor->assign(['oxactive' => 1, 'oxtitle' => 'bla']);
        $vendor->save();

        $seoEncoderVendorMock->expects($this->once())->method('onDeleteVendor')->with(
            $this->callback(function($vendor) use ($id) {
                return $vendor->getId() == $id;
            })
        );
        unset($vendor);

        $newVendor = oxNew(Vendor::class);
        $newVendor->delete($id);
    }
}