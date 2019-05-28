<?php

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\ProjectDIConfig\Dao;

use OxidEsales\EshopCommunity\Internal\Common\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Internal\Review\Dao\ReviewDaoInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\TestContainerFactory;
use OxidEsales\EshopCommunity\Tests\Unit\Internal\BasicContextStub;
use PHPUnit\Framework\TestCase;

class DataObjectsEnhancmentTest extends TestCase
{
    /** @var ReviewDaoInterface */
    private $reviewDao;

    public function setUp()
    {

        $basicContext = new BasicContextStub();
        $basicContext->setConfigurableProjectFilePath(__DIR__ . DIRECTORY_SEPARATOR . 'data' . DIRECTORY_SEPARATOR . 'test_project.yaml');
        $containerFactory = new TestContainerFactory();
        $container = $containerFactory->create($basicContext);

        $container->compile();

        $this->reviewDao = $container->get(ReviewDaoInterface::class);

        /** @var QueryBuilderFactoryInterface $queryBuilderFactory */
        $queryBuilderFactory = $container->get(QueryBuilderFactoryInterface::class);
        $queryBuilder = $queryBuilderFactory->create();
        $this->connection = $queryBuilder->getConnection();
        $this->connection->prepare('ALTER TABLE oxreviews ADD COLUMN ADDITIONALTIMESTAMP DATETIME')->execute();
        $this->connection->prepare('ALTER TABLE oxreviews ADD COLUMN ADDITIONALINT INT')->execute();
    }

    public function testUpdateReviewDao()
    {
        $reviews = $this->reviewDao->getReviewsByUserId('oxdefaultadmin');
        $reviews[0]->setAdditionaltimestamp(date("2019-01-01 01:59:59"));
        $reviews[0]->setAdditionalint(7);

        $this->reviewDao->save($reviews[0]);

        $reviews = $this->reviewDao->getReviewsByUserId('oxdefaultadmin');
        $this->assertEquals(date("2019-01-01 01:59:59"), $reviews[0]->getAdditionaltimestamp());
        $this->assertEquals(7, $reviews[0]->getAdditionalint());
    }

    public function testSaveReviewDao()
    {
        $review = $this->reviewDao->create();
        $review->setRating(5);
        $review->setText("Some review text");
        $review->setType("whatever");
        $review->setUserId("user1");
        $review->setObjectId("some OXID");
        $review->setAdditionaltimestamp("2019-01-01 01:59:59");
        $review->setAdditionalint(7);

        $id = $this->reviewDao->save($review);

        $reviews = $this->reviewDao->getReviewsByUserId('user1');
        $this->assertEquals(sizeof($reviews), 1);
        $this->assertEquals($id, $reviews[0]->getId());
        $this->assertEquals("2019-01-01 01:59:59", $reviews[0]->getAdditionaltimestamp());
        $this->assertEquals(7, $reviews[0]->getAdditionalint());
    }

    public function tearDown()
    {
        $this->connection->prepare('ALTER TABLE oxreviews DROP COLUMN ADDITIONALTIMESTAMP')->execute();
        $this->connection->prepare('ALTER TABLE oxreviews DROP COLUMN ADDITIONALINT')->execute();
        foreach ($this->reviewDao->getReviewsByUserId('user1') as $review) {
            $this->reviewDao->delete($review);
        }
    }
}
