<?php
namespace OxidEsales\Eshop\Core\Event;

class NewsletterSubscribed extends AbstractEvent
{
    const NAME = 'NewsletterSubscribed';

    private $user;
    public function __construct($user)
    {
        $this->user = $user;
    }

    public function getUser()
    {
        return $this->user;
    }
}