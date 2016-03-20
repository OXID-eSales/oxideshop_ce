<?php
namespace OxidEsales\Eshop\Core;

/**
 * Class MailClient
 */
class MailClient implements MailClientInterface
{
    public function __construct()
    {
        $config = new MailClientConfig();

        $this->mailer = new \PHPMailer(true);

        $this->mailer->isHtml(true);
        $this->mailer->set('CharSet', $config->getCharset());

        $protocol = $config->getProtocol();
        $this->mailer->set('mail', $protocol);

        if ($protocol === 'smtp') {
            $this->mailer->set('Host', $config->getSmtpHost());
            if ($config->requiresAuthorization()) {
                $this->mailer->set('SMTPAuth', true);
                $this->mailer->set('Username', $config->getSmtpUser());
                $this->mailer->set('Password', $config->getSmtpPassword());
            }

            if ($config->isSecureConnection()) {
                $this->mailer->set('SMTPSecure', $config->getSecureChannel());
            }
        }

        $this->mailer->set('WordWrap', 100);
        $this->mailer->set('SMTPDebug', $config->isDebugEnabled());
    }

    /**
     * Outputs email fields thought email output processor, includes images, and initiate email sending
     * If fails to send mail via SMTP, tries to send via mail(). On failing to send, sends mail to
     * shop administrator about failing mail sending
     *
     * @return bool
     */
    public function send(MailContainerReaderInterface $container)
    {
        $this->mailer->isHTML($container->isHtml());

        foreach ($container->getAddress() as $email => $name) {
            $this->mailer->addAddress($email, $name);
        }

        $this->mailer->set('Subject', $container->getSubject());
        $this->mailer->set('Body', $container->getBody());
        $this->mailer->set('AltBody', $container->getAltBody());

        foreach ($container->getReplyTo() as $email => $name) {
            $this->mailer->addReplyTo($email, $name);
        }

        foreach ($container->getFromAddress() as $email => $name) {
            $this->mailer->setFrom($email, $name);
        }

        foreach ($container->getEmbeddedImages() as $image) {
            $this->mailer->addEmbeddedImage($image->fullPath, $image->cid, $image->attFile, $image->encoding, $image->type);
        }

        foreach ($container->getAttachments() as $attachement) {
            $this->mailer->addAttachment($attachement->path, $attachement->file, $attachement->encoding, $attachement->type);
        }

        $sent = $this->mailer->send();

        // try to send mail via SMTP
        if (false === $sent && $this->mailer->Mailer === 'smtp') {
            $this->mailer->set('Mailer', 'mail');
            $sent = $this->mailer->send();
        }

        return $sent;
    }

    /**
     * Gets mailing error info.
     *
     * @return string
     */
    public function getErrorInfo()
    {
        return $this->mailer->ErrorInfo;
    }
}
