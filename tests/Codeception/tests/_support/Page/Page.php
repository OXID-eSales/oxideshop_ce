<?php
namespace Page;

class Page
{
    protected $user;

    public function __construct(\AcceptanceTester $I)
    {
        $this->user = $I;
    }
}
