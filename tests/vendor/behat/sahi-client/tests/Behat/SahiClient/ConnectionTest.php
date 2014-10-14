<?php

namespace Test\Behat\SahiClient;

use Buzz\Browser;
use Buzz\Message;
use Buzz\Listener;
use Behat\SahiClient\Connection;
require_once 'ClientQueue.php';
require_once 'ExtendedJournal.php';

class ConnectionTest extends \PHPUnit_Framework_TestCase
{
    private $browser;

    public function setUp()
    {
        $this->browser = new Browser(new ClientQueue());
        $this->browser->setListener(new Listener\HistoryListener(new ExtendedJournal()));
    }

    public function testExecuteCommand()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'all works fine'));

        $response   = $con->executeCommand('setSpeed', array('speed' => 2000, 'milli' => 'true'));
        $request    = $this->browser->getListener()->getJournal()->getLastRequest();

        $this->assertSame($this->browser, $con->getBrowser());
        $this->assertEquals('all works fine', $response);
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setSpeed', $request->getUrl());
        $this->assertEquals('speed=2000&milli=true&sahisid='.$sid, $request->getContent());
    }

    public function testExecuteStep()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeStep('_sahi._clearLastAlert()');

        $this->assertEquals(2, count($this->browser->getListener()->getJournal()));

        $request    = $this->browser->getListener()->getJournal()->getFirst()->getRequest();
        $response   = $this->browser->getListener()->getJournal()->getFirst()->getResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . urlencode('_sahi._clearLastAlert()'), $request->getContent());
    }

    /**
     * @expectedException   Behat\SahiClient\Exception\ConnectionException
     */
    public function testExecuteStepFail()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'error: incorrect'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeStep('_sahi._clearLastAlert()');
    }

    public function testExecuteJavascript()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', '25'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertEquals(25, $con->evaluateJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(3, count($this->browser->getListener()->getJournal()));

        $request    = $this->browser->getListener()->getJournal()->getFirst()->getRequest();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . urlencode('_sahi.setServerVarPlain('), $request->getContent());
        $this->assertContains(urlencode('_sahi._lastConfirm()'), $request->getContent());

        $request    = $this->browser->getListener()->getJournal()->getLastRequest();
        $response   = $this->browser->getListener()->getJournal()->getLastResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('25' , $response->getContent());
    }

    public function testLongExecuteJavascript()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', '22'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'false'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'false'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertEquals(22, $con->evaluateJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(5, count($this->browser->getListener()->getJournal()));

        $request    = $this->browser->getListener()->getJournal()->getFirst()->getRequest();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_setStep', $request->getUrl());
        $this->assertContains('step=' . urlencode('_sahi.setServerVarPlain('), $request->getContent());
        $this->assertContains(urlencode('_sahi._lastConfirm()'), $request->getContent());

        $request    = $this->browser->getListener()->getJournal()->getLastRequest();
        $response   = $this->browser->getListener()->getJournal()->getLastResponse();
        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('22' , $response->getContent());
    }

    /**
     * @expectedException   Behat\SahiClient\Exception\ConnectionException
     */
    public function tesExecuteJavascriptError()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'error: incorrect'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $con->executeJavascript('_sahi._lastConfirm()');
    }

    public function testExecuteJavascriptNull()
    {
        $con = $this->createConnection($sid = uniqid(), $this->browser, true);

        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'null'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
        $this->browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));

        $this->assertNull($con->evaluateJavascript('_sahi._lastConfirm()'));
        $this->assertEquals(3, count($this->browser->getListener()->getJournal()));

        $request    = $this->browser->getListener()->getJournal()->getLastRequest();
        $response   = $this->browser->getListener()->getJournal()->getLastResponse();

        $this->assertEquals('http://localhost:9999/_s_/dyn/Driver_getVariable', $request->getUrl());
        $this->assertContains('key=___lastValue___', $request->getContent());
        $this->assertEquals('null', $response->getContent());
    }

    /**
     * Create new Response.
     *
     * @param   string  $status     response status description
     * @param   string  $content    content
     *
     * @return  Response
     */
    protected function createResponse($status, $content = null)
    {
        $response = new Message\Response();
        $response->addHeader($status);

        if (null !== $content) {
            $response->setContent($content);
        }

        return $response;
    }

    /**
     * Create Sahi API Connection with custom SID.
     *
     * @param   string  $sid        sahi id
     * @param   boolean $correct    add correct responses to browser Queue for browser creation
     *
     * @return  Driver
     */
    protected function createConnection($sid, Browser $browser, $correct = false)
    {
        if ($correct) {
            $browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK', 'true'));
            $browser->getClient()->sendToQueue($this->createResponse('1.0 200 OK'));
        }

        $connection = new Connection($sid, 'localhost', 9999, $browser);

        if ($correct) {
            $browser->getListener()->getJournal()->clear();
        }

        return $connection;
    }
}
