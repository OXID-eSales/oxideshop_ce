<?php
namespace OxidEsales\Eshop\Core;


interface MailContainerReaderInterface
{
    public function getBody();
    public function getAltBody();
    public function getSubject();
    public function getRecipient();
    public function getReplyTo();
    public function getFromAddress();
    public function isHtml();
    public function getAddress();
    public function getEmbeddedImages();
    public function getAttachments();
}