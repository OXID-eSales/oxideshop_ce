<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Model\Vouchers;

use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

class VoucherTest extends IntegrationTestCase
{
    public function testSameReusedVoucherNumberOfEndedSerieCanBeCorrectlySelected(): void
    {
        $serie1 = oxNew('oxvoucherserie');
        $serie1->assign([
            'oxserienr' => 'Reused finished serie',
            'oxenddate' => date('Y-m-d H:i:s', time() - 3600 * 3)
        ]);
        $serie1->save();

        $serie2 = oxNew('oxvoucherserie');
        $serie2->assign([
            'oxserienr' => 'Reused active serie',
            'oxenddate' => date('Y-m-d H:i:s', time() + 3600 * 3)
        ]);
        $serie2->save();

        $reusedVoucherNumber = uniqid();

        $voucher1 = oxNew("oxvoucher");
        $voucher1->assign([
            'OXVOUCHERNR' => $reusedVoucherNumber,
            'OXVOUCHERSERIEID' => $serie1->getId()
        ]);
        $voucher1->save();

        $voucher2 = oxNew("oxvoucher");
        $voucher2->assign([
            'OXVOUCHERNR' => $reusedVoucherNumber,
            'OXVOUCHERSERIEID' => $serie2->getId()
        ]);
        $voucher2->save();

        $voucherTry = oxNew("oxvoucher");
        $voucherTry->getVoucherByNr($reusedVoucherNumber);

        $this->assertSame($serie2->getId(), $voucherTry->getFieldData('OXVOUCHERSERIEID'));
    }
}