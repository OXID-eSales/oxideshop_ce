<?php
namespace OxidEsales\Eshop\Core;

class MailContainer implements MailContainerSetterInterface, MailContainerReaderInterface
{
    public function setBody($body)
    {
        return $this;
    }

    public function getBody()
    {
        return '';
    }

    public function setAltBody($body)
    {
        return $this;
    }

    public function getAltBody()
    {
        return '';
    }

    public function setSubject($subject)
    {
        // A. HTML entities in subjects must be replaced
        $subject = str_replace(array('&amp;', '&quot;', '&#039;', '&lt;', '&gt;'), array('&', '"', "'", '<', '>'), $subject);

        return $this;
    }

    public function getSubject()
    {
    }

    public function setRecipient($address, $name = null)
    {
        $address = idn_to_ascii($address);

        return $this;
    }

    public function getRecipient()
    {
    }

    public function setReplyTo($address, $name = null)
    {
        return $this;
    }

    public function getReplyTo()
    {
    }

    public function setFromAddress($sFromAddress, $sFromName = null)
    {
        return $this;
    }

    public function getFromAddress()
    {
    }

    public function isHtml($isHtml)
    {
        return $this;
    }

    public function addAddress($address, $name = '')
    {
        return $this;
    }

    public function getAddress()
    {
    }


    /**
     * Adds an embedded attachment (check phpmail documentation for more details)
     *
     * @param string $sFullPath Path to the attachment.
     * @param string $sCid      Content ID of the attachment. Use this to identify the Id for accessing the image in an HTML form.
     * @param string $sAttFile  Overrides the attachment name.
     * @param string $sEncoding File encoding (see $Encoding).
     * @param string $sType     File extension (MIME) type.
     *
     * @return bool
     */
    public function addEmbeddedImage($sFullPath, $sCid, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream')
    {
    }

    public function getEmbeddedImages()
    {
    }

    /**
     * Adds an attachment to mail from a path on the filesystem
     *
     * @param string $sAttPath  path to the attachment
     * @param string $sAttFile  attachment name
     * @param string $sEncoding attachment encoding
     * @param string $sType     attachment type
     *
     * @return bool
     */
    public function addAttachment($sAttPath, $sAttFile = '', $sEncoding = 'base64', $sType = 'application/octet-stream')
    {
        //$this->_aAttachments[] = array($sAttPath, $sAttFile, $sEncoding, $sType);
    }

    public function getAttachments()
    {
        //$this->_aAttachments[] = array($sAttPath, $sAttFile, $sEncoding, $sType);
    }

}