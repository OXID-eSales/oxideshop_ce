<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Codeception\Support;

use OxidEsales\EshopCommunity\Tests\Codeception\Support\_generated\AcceptanceSetupTesterActions;
use Codeception\Actor;
use OxidEsales\Codeception\ShopSetup\SystemRequirementsStep;

/**
 * Inherited Methods
 * @method void wantTo($text)
 * @method void wantToTest($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause($vars = [])
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceSetupTester extends Actor
{
    use AcceptanceSetupTesterActions;

    public function openShopSetup(): SystemRequirementsStep
    {
        $I = $this;
        $I->amOnPage(SystemRequirementsStep::$setupStartingUrl);

        return new SystemRequirementsStep($I);
    }
}
