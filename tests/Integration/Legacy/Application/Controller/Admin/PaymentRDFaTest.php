<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Legacy\Application\Controller\Admin;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Application\Controller\Admin\PaymentRdfa;
use OxidEsales\Eshop\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\ContainerTrait;
use OxidEsales\EshopCommunity\Tests\Integration\IntegrationTestCase;

final class PaymentRDFaTest extends IntegrationTestCase
{
    use ContainerTrait;

    private string $paymentId;

    private string $descriptionInDefaultLanguage = 'description-in-default-language';

    private string $descriptionInLanguage1 = 'description-in-lang-1';

    public function setUp(): void
    {
        parent::setUp();
        $this->createPayment();
    }

    public function testRenderWithDefaultLanguage(): void
    {
        $_POST['oxid'] = $this->paymentId;

        /** @var PaymentRdfa $paymentRdfa */
        $paymentRdfa = oxNew(PaymentRdfa::class);

        $paymentRdfa->render();
        $paymentDescription = $paymentRdfa->getViewData()['edit']
            ->getFieldData('OXDESC');

        $this->assertSame($this->descriptionInDefaultLanguage, $paymentDescription);
    }

    private function createPayment(): void
    {
        $this->paymentId = Registry::getUtilsObject()->generateUId();
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->insert('oxpayments')
            ->values([
                'OXID' => "\"{$this->paymentId}\"",
                'OXACTIVE' => true,
                'OXDESC' => "\"{$this->descriptionInDefaultLanguage}\"",
            ]);
        $queryBuilder->execute();
    }
}
