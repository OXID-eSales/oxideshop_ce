<?php

namespace Behat\SahiClient;

use Buzz;

use Behat\SahiClient\Exception;

/*
 * This file is part of the Behat\SahiClient.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Sahi Connection Driver.
 *
 * @author Konstantin Kudryashov <ever.zet@gmail.com>
 */
class Connection
{
    /**
     * Sahi SID
     *
     * @var     integer
     */
    private     $sid;
    /**
     * Is custom SID provided to connection
     *
     * @var     Boolean
     */
    private     $customSidProvided = false;
    /**
     * Sahi proxy hostname
     *
     * @var     string
     */
    private     $host;
    /**
     * Sahi proxy port number
     *
     * @var     integer
     */
    private     $port;
    /**
     * Time limit to connection.
     *
     * @var     integer
     */
    private     $limit;

    /**
     * HTTP Browser instance.
     *
     * @var     BrowserInterface
     */
    protected   $browser;

    /**
     * Initialize Sahi Driver.
     *
     * @param   string      $sid        Sahi SID
     * @param   string      $host       Sahi proxy host
     * @param   integer     $port       Sahi proxy port
     * @param   BuzzBrowser $browser    HTTP browser instance
     * @param   integer     $limit      Time limit to connection
     */
    public function __construct($sid = null, $host = 'localhost', $port = 9999, Buzz\Browser $browser = null, $limit = 600)
    {
        $this->limit = $limit;

        if (null !== $sid) {
            $this->customSidProvided = true;
        }

        $this->sid  = $sid;
        $this->host = $host;
        $this->port = $port;

        if (null === $browser) {
            $client = new Buzz\Client\Curl();
            $this->browser = new Buzz\Browser($client);
        } else {
            $this->browser = $browser;
        }
    }

    /**
     * Checks that connection used custom SID.
     *
     * @return  Boolean
     */
    public function isCustomSidProvided()
    {
        return $this->customSidProvided;
    }

    /**
     * Returns current connection SID.
     *
     * @return  string
     */
    public function getSid()
    {
        return $this->sid;
    }

    /**
     * Starts browser.
     *
     * @param   string  $browserName    (firefox, ie, safari, chrome, opera)
     */
    public function start($browserName)
    {
        if (!$this->customSidProvided) {
            $this->sid = uniqid();
            $this->executeCommand('launchPreconfiguredBrowser', array('browserType' => $browserName));
        }
    }

    /**
     * Stop browser.
     */
    public function stop()
    {
        if (!$this->customSidProvided) {
            $this->executeCommand('kill');

            // sometimes, firefox is not fast enought at closing
            // and next instance can't be created since previous
            // still running. So - wait 1 second.
            sleep(1);
        }
    }

    /**
     * Checks whether Sahi proxy were started.
     *
     * @return  Boolean
     */
    public function isProxyStarted()
    {
        return 200 === $this->post(
            sprintf('http://%s:%d/_s_/spr/blank.htm', $this->host, $this->port)
        )->getStatusCode();
    }

    /**
     * Checks whether connection is ready.
     *
     * @return  Boolean
     */
    public function isReady()
    {
        return 'true' === $this->executeCommand('isReady');
    }

    /**
     * Return HTTP Browser instance.
     *
     * @return  Browser
     */
    public function getBrowser()
    {
        return $this->browser;
    }

    /**
     * Execute Sahi command & returns its response.
     *
     * @param   string  $command        Sahi command
     * @param   array   $parameters     parameters
     *
     * @return  string                  command response
     */
    public function executeCommand($command, array $parameters = array())
    {
        $content = $this->post(
            sprintf('http://%s:%d/_s_/dyn/Driver_%s', $this->host, $this->port, $command),
            array_merge($parameters, array('sahisid' => $this->sid))
        )->getContent();

        if (false !== strpos($content, 'SAHI_ERROR')) {
            throw new Exception\ConnectionException('Sahi proxy error');
        }

        return $content;
    }

    /**
     * Execute Sahi step.
     *
     * @param   string  $step       step command
     * @param   integer $limit      time limit (value of 10 === 1 second)
     *
     * @throws  BrowserException    if step execution has errors
     */
    public function executeStep($step, $limit = null)
    {
        $this->executeCommand('setStep', array('step' => $step));

        $limit = $limit ?: $this->limit;
        $check = 'false';
        while ('true' !== $check) {
            usleep(100000);
            if (--$limit <= 0) {
                throw new Exception\ConnectionException(
                    'Command execution time limit reached: `' . $step . '`'
                );
            }

            $check = $this->executeCommand('doneStep');
            if (0 === mb_strpos($check, 'error:')) {
                throw new Exception\ConnectionException($check);
            }
        }
    }

    /**
     * Evaluates JS expression on the browser and returns it's value.
     *
     * @param   string  $expression JS expression
     * @param   integer $limit      time limit (value of 10 === 1 second)
     *
     * @return  string|null
     */
    public function evaluateJavascript($expression, $limit = null)
    {
        $key = '___lastValue___' . uniqid();
        $this->executeStep(
            sprintf("_sahi.setServerVarPlain(%s, %s)", "'" . $key . "'", $expression), $limit
        );

        $resp = $this->executeCommand('getVariable', array('key' => $key));

        return 'null' === $resp ? null : $resp;
    }

    /**
     * Execute JS expression on the browser.
     *
     * @param   string  $expression JS expression
     */
    public function executeJavascript($expression, $limit = null)
    {
        $this->executeStep(sprintf("_sahi._call(%s)", $expression), $limit);
    }

    /**
     * Send POST request to specified URL.
     *
     * @param   string  $url    URL
     * @param   array   $query  POST query parameters
     *
     * @return  string          response
     */
    private function post($url, array $query = array())
    {
        return $this->browser->post($url, array(), $this->prepareQueryString($query));
    }

    /**
     * Convert array parameters to POST parameters.
     *
     * @param   array   $query  parameters
     *
     * @return  string          query string (key1=val1&key2=val2)
     */
    private function prepareQueryString(array $query)
    {
        $items = array();
        foreach ($query as $key => $val) {
            $items[] = $key . '=' . urlencode($val);
        }

        return implode('&', $items);
    }
}
