<?php
namespace OxidEsales\Eshop\Core\Event;

class UserCreated extends AbstractEvent
{
    const NAME = 'UserCreated';

    private $user;
    private $sendConfirmationEmail;
    public function __construct($user, $sendConfirmationEmail)
    {
        $this->user = $user;
        $this->sendConfirmationEmail = $sendConfirmationEmail;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function sendConfirmationEmail()
    {
        return $this->sendConfirmationEmail;
    }
}