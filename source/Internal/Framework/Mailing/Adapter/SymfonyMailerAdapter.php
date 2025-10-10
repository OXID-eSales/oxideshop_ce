<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Framework\Mailing\Adapter;

use OxidEsales\EshopCommunity\Core\Email;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Symfony\Component\Mime\Address;

class SymfonyMailerAdapter implements EmailAdapterInterface
{
    public function convertToSymfonyEmail(Email $legacyEmail): SymfonyEmail
    {
        $email = new SymfonyEmail();

        $recipients = $legacyEmail->getRecipient();
        if (is_array($recipients)) {
            foreach ($recipients as $recipient) {
                $email->addTo(new Address($recipient[0], $recipient[1] ?? ''));
            }
        }

        if ($legacyEmail->getFrom()) {
            $email->from(new Address(
                $legacyEmail->getFrom(),
                $legacyEmail->getFromName() ?? ''
            ));
        }

        if ($legacyEmail->getSubject()) {
            $email->subject($legacyEmail->getSubject());
        }

        if ($legacyEmail->getBody()) {
            $email->html($legacyEmail->getBody());
        }

        if ($legacyEmail->getAltBody()) {
            $email->text($legacyEmail->getAltBody());
        }

        $replyTo = $legacyEmail->getReplyTo();
        if (is_array($replyTo)) {
            foreach ($replyTo as $replyToEntry) {
                $email->addReplyTo(new Address($replyToEntry[0], $replyToEntry[1] ?? ''));
            }
        }

        foreach ($legacyEmail->getCc() as $ccEntry) {
            $email->addCc(new Address($ccEntry[0], $ccEntry[1] ?? ''));
        }

        foreach ($legacyEmail->getBcc() as $bccEntry) {
            $email->addBcc(new Address($bccEntry[0], $bccEntry[1] ?? ''));
        }

        foreach ($legacyEmail->getAttachments() as $attachment) {
            if ($attachment[6] === 'inline') {
                $email->embedFromPath($attachment[0], $attachment[2], $attachment[4]);
            } else {
                $email->attachFromPath($attachment[0], $attachment[2], $attachment[4]);
            }
        }

        foreach ($legacyEmail->getCustomHeaders() as $header) {
            $email->getHeaders()->addTextHeader($header[0], $header[1]);
        }

        return $email;
    }
}
