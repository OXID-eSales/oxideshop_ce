<?php

namespace Test\Behat\SahiClient;

use Buzz\Client\ClientInterface;
use Buzz\Message\MessageInterface;
use Buzz\Message\RequestInterface;

class ClientQueue implements ClientInterface
{
    protected $queue = array();

    public function getQueue()
    {
        return $this->queue;
    }

    public function setQueue(array $queue)
    {
        foreach ($queue as $response) {
            $this->sendToQueue($response);
        }
    }

    /**
     * Sends a response into the queue.
     *
     * @param MessageInterface $response A response
     */
    public function sendToQueue(MessageInterface $response)
    {
        $this->queue[] = $response;
    }

    /**
     * Receives a response from the queue.
     *
     * @return MessageInterface|null
     */
    public function receiveFromQueue()
    {
        if (count($this->queue)) {
            return array_pop($this->queue);
        }
    }

    /**
     * @see ClientInterface
     */
    public function send(RequestInterface $request, MessageInterface $response)
    {
        if (!$queued = $this->receiveFromQueue()) {
            throw new \LogicException('There are no queued responses.');
        }

        $response->setHeaders($queued->getHeaders());
        $response->setContent($queued->getContent());
    }
}
