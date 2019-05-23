<?php
namespace OxidEsales\EshopCommunity\Tests\Codeception\Group;

use \Codeception\Event\TestEvent;
use Codeception\Util\Fixtures;

/**
 * Group class is Codeception Extension which is allowed to handle to all internal events.
 * This class itself can be used to listen events for test execution of one particular group.
 * It may be especially useful to create fixtures data, prepare server, etc.
 *
 * INSTALLATION:
 *
 * To use this group extension, include it to "extensions" option of global Codeception config.
 */

class Frontend extends \Codeception\Platform\Group
{
    public static $group = 'frontend';

    public function _before(TestEvent $e)
    {
        $I = $this->getModule('\OxidEsales\Codeception\Module\Database');
        $I->updateConfigInDatabase('blPerfNoBasketSaving', true, 'bool');
        $productData = Fixtures::get('oxarticles');
        $db = $this->getModule('Db');
        foreach ($productData as $product) {
            $db->haveInDatabase('oxarticles', $product);
        }
        $productDescriptionData = Fixtures::get('oxartextends');
        foreach ($productDescriptionData as $description) {
            $db->haveInDatabase('oxartextends', $description);
        }
        $categoryData = Fixtures::get('oxcategories');
        foreach ($categoryData as $category) {
            $db->haveInDatabase('oxcategories', $category);
        }
        $categoryRelationData = Fixtures::get('oxobject2category');
        foreach ($categoryRelationData as $categoryRelation) {
            $db->haveInDatabase('oxobject2category', $categoryRelation);
        }
    }

    public function _after(TestEvent $e)
    {
    }
}
