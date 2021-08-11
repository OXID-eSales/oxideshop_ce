<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Integration\Application\Controller\Admin;

use Doctrine\DBAL\Query\QueryBuilder;
use OxidEsales\Eshop\Application\Controller\Admin\PaymentRdfa;
use OxidEsales\EshopCommunity\Internal\Framework\Database\QueryBuilderFactoryInterface;
use OxidEsales\EshopCommunity\Tests\Integration\Internal\ContainerTrait;
use OxidEsales\TestingLibrary\UnitTestCase;

final class PaymentRDFaTest extends UnitTestCase
{
    use ContainerTrait;

    /** @var string */
    private $paymentId;
    /** @var string */
    private $descriptionInDefaultLanguage = 'description-in-default-language';
    /** @var string */
    private $descriptionInLanguage1 = 'description-in-lang-1';

    protected function setUp(): void
    {
        parent::setUp();
        $this->createPayment();
    }

    public function testRenderWithDefaultLanguage(): void
    {
        $this->setRequestParameter('oxid', $this->paymentId);
        /** @var PaymentRdfa $paymentRdfa */
        $paymentRdfa = oxNew(PaymentRdfa::class);

        $paymentRdfa->render();
        $paymentDescription = $paymentRdfa->getViewData()['edit']->getFieldData('OXDESC');

        $this->assertSame($this->descriptionInDefaultLanguage, $paymentDescription);
    }

    public function testRenderWithAvailableLanguage(): void
    {
        $availableLanguageId = 1;
        $this->setRequestParameter('oxid', $this->paymentId);
        /** @var PaymentRdfa $paymentRdfa */
        $paymentRdfa = oxNew(PaymentRdfa::class);
        $this->setProtectedClassProperty($paymentRdfa, '_iEditLang', $availableLanguageId);

        $paymentRdfa->render();
        $paymentDescription = $paymentRdfa->getViewData()['edit']->getFieldData('OXDESC');

        $this->assertSame($this->descriptionInLanguage1, $paymentDescription);
    }

    private function createPayment(): void
    {
        $this->paymentId = \OxidEsales\Eshop\Core\Registry::getUtilsObject()->generateUId();
        /** @var QueryBuilder $queryBuilder */
        $queryBuilder = $this->get(QueryBuilderFactoryInterface::class)->create();
        $queryBuilder->insert('oxpayments')
        ->values([
            'OXID' => "\"$this->paymentId\"",
            'OXACTIVE' => true,
            'OXDESC' => "\"$this->descriptionInDefaultLanguage\"",
            'OXDESC_1' => "\"$this->descriptionInLanguage1\"",
        ]);
        $queryBuilder->execute();
    }
}
