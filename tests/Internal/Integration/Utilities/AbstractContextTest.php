<?php

/**
 * Created by PhpStorm.
 * User: michael
 * Date: 23.08.17
 * Time: 13:07
 */

namespace OxidEsales\EshopCommunity\Tests\Internal\Integration\Utilities;

use OxidEsales\Eshop\Core\Config;
use OxidEsales\Eshop\Core\Language;
use OxidEsales\EshopCommunity\Internal\Utilities\Context;
use OxidEsales\EshopCommunity\Tests\Internal\Integration\Database\AbstractDaoTests;

abstract class AbstractContextTest extends AbstractDaoTests
{

    /** @var  Context $context */
    protected $context;

    public function setUp()
    {

        parent::setUp();

        /** @var Config $config */
        $config = $this->getMockBuilder(\OxidEsales\Eshop\Core\Config::class)->getMock();
        /** @var Language $language */
        $language = $this->getMockBuilder(\OxidEsales\Eshop\Core\Language::class)->getMock();
        $this->context = new Context($config, $language, $this->getDoctrineConnection());
    }
}