<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Model;

use OxidEsales\Eshop\Application\Model\Review;
use OxidEsales\Eshop\Core\Field;
use OxidEsales\EshopCommunity\Tests\DatabaseTrait;
use OxidEsales\EshopCommunity\Tests\FieldTestingTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class ReviewTest extends IntegrationTestCase
{
    use DatabaseTrait;

    public function setUp(): void
    {
        parent::setUp();
        $this->beginTransaction();
    }

    public function tearDown(): void
    {
        $this->rollBackTransaction();
        parent::tearDown();
    }

    public function testLoadListEscapesHtmlAndAddsLineBreakHtmlTags(): void
    {
        $reviewType = 'oxrecommlist';
        $objectId = uniqid('id-', true);
        $text = "<script>alert();

new\nlineCharacter
carriage\rreturnCharacter";

        for ($i = 0; $i < 2; $i++) {
            $review = oxNew(Review::class);
            $review->oxreviews__oxobjectid = new Field($objectId);
            $review->oxreviews__oxtype = new Field($reviewType);
            $review->oxreviews__oxlang = new Field(0);
            $review->oxreviews__oxtext = new Field($text);
            $review->save();
        }

        $list = (oxNew(Review::class))->loadList($reviewType, $objectId, true, 0);

        foreach ($list as $review) {
            $this->assertEquals($text, $review->getFieldData('oxtext'));
        }
    }
}
