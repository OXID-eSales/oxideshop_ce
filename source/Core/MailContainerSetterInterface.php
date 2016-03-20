<?php
namespace OxidEsales\Eshop\Core;

interface MailContainerSetterInterface
{
    public function setBody($body);
    public function setAltBody($body);
    public function setSubject($subject);
    public function setRecipient($address, $name = null);
    public function setReplyTo($address, $name = null);
    public function setFromAddress($sFromAddress, $sFromName = null);
    public function addAddress($address, $name = '');
    public function addEmbeddedImage($sFullPath, $sCid, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream');
    public function addAttachment($sAttPath, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream');
}