<?php
namespace OxidEsales\Eshop\Core;

/**
 * Class MailContainer
 */
class MailContainer implements MailContainerSetterInterface, MailContainerReaderInterface
{
    private $body;
    private $altBody;
    private $subject;

    public function setBody($body)
    {
        $this->body = $body;
        return $this;
    }

    public function getBody()
    {
        return $this->body;
    }

    public function setAltBody($body)
    {
        $this->altBody = $body;
        return $this;
    }

    public function getAltBody()
    {
        return $this->altBody;
    }

    public function setSubject($subject)
    {
        // A. HTML entities in subjects must be replaced
        $this->subject = str_replace(array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), array('&', '"', "'", '<', '>'), $subject);
        return $this;
    }

    public function getSubject()
    {
        return $this->subject;
    }

    private $recipients = [];
    public function setRecipient($address, $name = null)
    {
        $address = idn_to_ascii($address);
        $this->recipients[$address] = $name;

        return $this;
    }

    public function getRecipient()
    {
        return $this->recipients;
    }

    private $replyTo = [];
    public function setReplyTo($address, $name = null)
    {
        $address = idn_to_ascii($address);
        $this->replyTo[$address] = $name;
        return $this;
    }

    public function getReplyTo()
    {
        return $this->replyTo;
    }

    private $fromAddress;
    public function setFromAddress($address, $name = null)
    {
        $address = idn_to_ascii($address);
        $this->fromAddress[$address] = $name;
        return $this;
    }

    public function getFromAddress()
    {
        return $this->fromAddress;
    }

    private $isHtml = false;
    public function isHtml()
    {
        return $this->isHtml;
    }

    public function setHtmlMode($html)
    {
        $this->html = $html;
        return $this;
    }

    private $addresses = [];
    public function addAddress($address, $name = '')
    {
        $this->addresses[$address] = $name;
        return $this;
    }

    public function getAddress()
    {
        return $this->addresses;
    }

    private $images = [];
    public function addEmbeddedImage($sFullPath, $sCid, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream')
    {
        $image = new \stdclass();

        $image->fullPath = $sFullPath;
        $image->cid = $sCid;
        $image->attFile = $sAttFile;
        $image->encoding = $sEncoding;
        $image->type = $sType;

        $this->images[] = $image;
    }

    public function getEmbeddedImages()
    {
        return $this->images;
    }

    private $attachments = [];
    public function addAttachment($sAttPath, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream')
    {
        $attachment = new \stdclass();
        $attachment->attPath = $sAttPath;
        $attachment->attFile = $sAttFile;
        $attachment->encoding = $sEncoding;
        $attachment->type = $sType;

        $this->attachments[] = $attachment;
    }

    public function getAttachments()
    {
        return $this->attachments;
    }
}
