<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */
namespace OxidEsales\EshopCommunity\Tests\Unit\Core;

use OxidEsales\Eshop\Core\OnlineServerEmailBuilder;
use \oxRegistry;

/**
 * Class Unit_Core_OnlineServerEmailBuilderTest
 */
class OnlineServerEmailBuilderTest extends \OxidTestCase
{
    public function testBuildIfParametersWereSetCorrectly()
    {
        $sBody = '_testXML';
        $oExpirationEmailBuilder = oxNew(OnlineServerEmailBuilder::class);
        $oExpirationEmail = $oExpirationEmailBuilder->build($sBody);
        $aRecipient = $oExpirationEmail->getRecipient();

        $this->assertSame($sBody, $oExpirationEmail->getBody(), 'Email content is not as it should be.');
        $this->assertSame('olc@oxid-esales.com', $aRecipient[0][0], 'Recipient email address is wrong.');
        $this->assertSame(oxRegistry::getLang()->translateString('SUBJECT_UNABLE_TO_SEND_VIA_CURL', null, true), $oExpirationEmail->getSubject(), 'Subject is wrong.');
    }
}
