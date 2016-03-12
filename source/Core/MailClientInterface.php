<?php
namespace OxidEsales\Eshop\Core;

interface MailClientInterface
{
    public function send(MailContainerReaderInterface $container);
    public function getErrorInfo();
}