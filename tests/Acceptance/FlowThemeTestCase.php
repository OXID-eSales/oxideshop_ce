<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance;

/**
 * Class FlowThemeTestCase
 *
 * @package OxidEsales\EshopCommunity\Tests\Acceptance
 */
abstract class FlowThemeTestCase extends AcceptanceTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->activateTheme('flow');
    }

    /**
     * The method loginInFrontend is designed for the azure theme.
     *
     * @param string  $userName     User name (email).
     * @param string  $userPass     User password.
     * @param boolean $waitForLogin If needed to wait until user get logged in.
     *
     */
    public function loginInFrontend($userName, $userPass, $waitForLogin = true)
    {
        $this->click("//div[contains(@class, 'showLogin')]/button");
        $this->waitForItemAppear("loginBox");

        $this->type("loginEmail", $userName);
        $this->type("loginPasword", $userPass);

        $this->clickAndWait("//div[@id='loginBox']/button");

        if ($waitForLogin) {
            $this->waitForTextDisappear('%LOGIN%');
        }
    }
}
