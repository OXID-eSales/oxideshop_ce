<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Core\Internal\ServiceFactory;

use OxidEsales\EshopCommunity\Internal\Facade\ProductRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewAndRatingFacadeInterface;
use OxidEsales\EshopCommunity\Internal\Facade\UserReviewFacadeInterface;
use OxidEsales\EshopCommunity\Internal\ServiceFactory\FacadeServiceFactory;

class FacadeServiceFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetInstance()
    {
        $facadeServiceFactory = FacadeServiceFactory::getInstance();
        $this->assertInstanceOf(
            FacadeServiceFactory::class,
            $facadeServiceFactory
        );
    }

    public function testGetUserReviewAndRatingFacade()
    {
        $facadeServiceFactory = FacadeServiceFactory::getInstance();

        $this->assertInstanceOf(
            UserReviewAndRatingFacadeInterface::class,
            $facadeServiceFactory->getUserReviewAndRatingFacade()
        );
    }

    public function testGetProductRatingFacade()
    {
        $facadeServiceFactory = FacadeServiceFactory::getInstance();

        $this->assertInstanceOf(
            ProductRatingFacadeInterface::class,
            $facadeServiceFactory->getProductRatingFacade()
        );
    }

    public function testGetUserRatingFacade()
    {
        $facadeServiceFactory = FacadeServiceFactory::getInstance();

        $this->assertInstanceOf(
            UserRatingFacadeInterface::class,
            $facadeServiceFactory->getUserRatingFacade()
        );
    }

    public function testGetUserReviewFacade()
    {
        $facadeServiceFactory = FacadeServiceFactory::getInstance();

        $this->assertInstanceOf(
            UserReviewFacadeInterface::class,
            $facadeServiceFactory->getUserReviewFacade()
        );
    }
}
