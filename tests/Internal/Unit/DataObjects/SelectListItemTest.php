<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 26.09.17
 * Time: 14:09
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Unit\DataObjects;

use OxidEsales\EshopCommunity\Internal\DataObject\SelectListItem;

class SelectListItemTest extends \PHPUnit_Framework_TestCase
{


    /**
     * @dataProvider typeProvider
     */
    public function testModifyPrice($priceDelta, $deltaType, $input, $output) {

        $item = new SelectListItem('A1', 'Some Key', $priceDelta, $deltaType);

        $this->assertEquals($output, $item->modifyPrice($input));

    }

    public function typeProvider() {

        return [
            [-10, SelectListItem::DELTA_TYPE_PERCENT, 20, 18],
            [-10, SelectListItem::DELTA_TYPE_PERCENT, 0, 0],
            [-10, SelectListItem::DELTA_TYPE_ABSOLUTE, 20, 10],
            [-10, SelectListItem::DELTA_TYPE_ABSOLUTE, 0, -10]
        ];

    }

}