<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Acceptance\Admin;

use OxidEsales\EshopCommunity\Tests\Acceptance\AdminTestCase;

/** Private sales related tests. */
class PrivateSalesAdminTest extends AdminTestCase
{
    /**
     * Basket exclusion: situation 1
     *
     * @group privateSalesAdmin
     */
    public function testPrivateSalesDefaults()
    {
        //enabling basket exclusion
        $this->loginAdmin("Master Settings", "Core Settings");
        $this->openTab("Settings");
        $this->click("link=Private Sales");
        $this->assertEquals("Disable", $this->getSelectedLabel("confstrs[blBasketExcludeEnabled]"));
        $this->assertEquals("Disable", $this->getSelectedLabel("basketreserved"));
        $this->assertElementNotVisible("confstrs[iPsBasketReservationTimeout]");

        $this->select("confstrs[blBasketExcludeEnabled]", "label=Enable");

        $this->select("basketreserved", "label=Enable");
        $this->waitForItemAppear("confstrs[iPsBasketReservationTimeout]");
        $this->type("confstrs[iPsBasketReservationTimeout]", "20");

        $this->clickAndWait("save");

        $this->click("link=Private Sales");
        $this->assertEquals("Enable", $this->getSelectedLabel("confstrs[blBasketExcludeEnabled]"));
        $this->assertEquals("Enable", $this->getSelectedLabel("basketreserved"));
        $this->assertEquals("20", $this->getValue("confstrs[iPsBasketReservationTimeout]"));

        $this->click("link=Invitations");
        $this->assertEquals("Disable", $this->getSelectedLabel("invitations"));
        $this->assertFalse($this->isVisible("confstrs[dPointsForInvitation]"));
        $this->assertFalse($this->isVisible("confstrs[dPointsForRegistration]"));
        $this->assertEquals("Disable", $this->getSelectedLabel("confstrs[blPsLoginEnabled]"));

        $this->select("invitations", "label=Enable");
        $this->waitForItemAppear("confstrs[dPointsForInvitation]");
        $this->waitForItemAppear("confstrs[dPointsForRegistration]");
        $this->type("confstrs[dPointsForInvitation]", "5");
        $this->type("confstrs[dPointsForRegistration]", "5");

        $this->select("confstrs[blPsLoginEnabled]", "label=Enable");

        $this->clickAndWait("save");

        $this->click("link=Invitations");
        $this->assertEquals("Enable", $this->getSelectedLabel("invitations"));
        $this->assertEquals("5", $this->getValue("confstrs[dPointsForInvitation]"));
        $this->assertEquals("5", $this->getValue("confstrs[dPointsForRegistration]"));
        $this->assertEquals("Enable", $this->getSelectedLabel("confstrs[blPsLoginEnabled]"));
    }

    /**
     * Tests whether correct information is set to CMS pages
     *
     * @group privateSalesAdmin
     */
    public function testCMSPage()
    {
        $this->loginAdmin("Customer Info", "CMS Pages", true);
        $this->type("where[oxcontents][oxloadid]", "agb");
        $this->clickAndWait("submitit");
        $this->openListItem("link=oxagb");
        $this->type("editval[oxcontents__oxtermversion]", "2");
        $this->clickAndWait("saveContent");
    }
}
