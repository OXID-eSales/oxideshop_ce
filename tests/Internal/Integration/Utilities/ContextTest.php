<?php
/**
 * Created by PhpStorm.
 * User: michael
 * Date: 23.08.17
 * Time: 12:55
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Utilities;

use \OxidEsales\EshopCommunity\Internal\Utilities\Context;

class ContextTest extends AbstractContextTest
{

    public function testUsesCategoryVat()
    {

        $this->assertTrue($this->context->shopUsesCategoryVat());
    }

    public function getFixtureFile()
    {
        return dirname(__FILE__) . '/../Fixtures/OxCategoriesVatTestFixture.xml';
    }
}