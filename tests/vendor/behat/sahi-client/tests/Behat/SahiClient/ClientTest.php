<?php

namespace Test\Behat\SahiClient;

use Behat\SahiClient\Client;
require_once 'AbstractConnectionTest.php';

class ClientTest extends AbstractConnectionTest
{
    private $api;

    public function setUp()
    {
        $connection = $this->getConnectionMock();
        $this->api  = new Client($connection);
    }

    public function testNavigateTo()
    {
        $this->assertActionStep(
            sprintf('_sahi._navigateTo("%s")', 'http://sahi.co.in'),
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in')
        );

        $this->assertActionStep(
            sprintf('_sahi._navigateTo("%s", true)', 'http://sahi.co.in'),
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in', true)
        );

        $this->assertActionStep(
            sprintf('_sahi._navigateTo("%s", false)', 'http://sahi.co.in'),
            array($this->api, 'navigateTo'),
            array('http://sahi.co.in', false)
        );
    }

    public function testSetSpeed()
    {
        $this->assertActionCommand(
            'setSpeed', array('speed' => 12),
            array($this->api, 'setSpeed'),
            array(12)
        );
    }

    public function testFindByClassName()
    {
        $accessor = $this->api->findByClassName('', '');

        $this->assertInstanceOf('Behat\SahiClient\Accessor\ByClassNameAccessor', $accessor);
    }
}
