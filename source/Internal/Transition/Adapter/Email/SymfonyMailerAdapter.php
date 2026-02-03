<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Internal\Transition\Adapter\Email;

use OxidEsales\EshopCommunity\Core\Email;
use PHPMailer\PHPMailer\PHPMailer;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email as SymfonyEmail;
use Symfony\Component\Mime\Part\DataPart;

class SymfonyMailerAdapter implements EmailAdapterInterface
{
    public function convertToSymfonyEmail(Email $legacyEmail): SymfonyEmail
    {
        $email = new SymfonyEmail();
        $cidMapping = $this->buildCidMapping($legacyEmail);

        $this->addRecipients($email, $legacyEmail);
        $this->addFrom($email, $legacyEmail);
        $this->addReplyTo($email, $legacyEmail);
        $this->addCc($email, $legacyEmail);
        $this->addBcc($email, $legacyEmail);
        $this->addSubject($email, $legacyEmail);
        $this->addBody($email, $legacyEmail, $cidMapping);
        $this->addAttachments($email, $legacyEmail, $cidMapping);
        $this->addCustomHeaders($email, $legacyEmail);
        $this->addPriority($email, $legacyEmail);
        $this->addSender($email, $legacyEmail);

        return $email;
    }

    private function buildCidMapping(Email $legacyEmail): array
    {
        $cidMapping = [];

        foreach ($legacyEmail->getAttachments() as $attachment) {
            $cid = $attachment[7] ?? null;
            if (is_string($cid) && $cid !== '') {
                $cidMapping[$cid] = str_contains($cid, '@') ? $cid : $cid . '@generated';
            }
        }

        return $cidMapping;
    }

    private function addRecipients(SymfonyEmail $email, Email $legacyEmail): void
    {
        $recipients = $legacyEmail->getRecipient();
        if (!is_array($recipients)) {
            return;
        }

        foreach ($recipients as $recipient) {
            $email->addTo(new Address($recipient[0], $recipient[1] ?? ''));
        }
    }

    private function addFrom(SymfonyEmail $email, Email $legacyEmail): void
    {
        if (!$legacyEmail->getFrom()) {
            return;
        }

        $email->from(new Address($legacyEmail->getFrom(), $legacyEmail->getFromName() ?? ''));
    }

    private function addReplyTo(SymfonyEmail $email, Email $legacyEmail): void
    {
        $replyTo = $legacyEmail->getReplyTo();
        if (!is_array($replyTo)) {
            return;
        }

        foreach ($replyTo as $entry) {
            $email->addReplyTo(new Address($entry[0], $entry[1] ?? ''));
        }
    }

    private function addCc(SymfonyEmail $email, Email $legacyEmail): void
    {
        foreach ($legacyEmail->getCc() as $entry) {
            $email->addCc(new Address($entry[0], $entry[1] ?? ''));
        }
    }

    private function addBcc(SymfonyEmail $email, Email $legacyEmail): void
    {
        foreach ($legacyEmail->getBcc() as $entry) {
            $email->addBcc(new Address($entry[0], $entry[1] ?? ''));
        }
    }

    private function addSubject(SymfonyEmail $email, Email $legacyEmail): void
    {
        $subject = $legacyEmail->getSubject();
        if ($subject) {
            $email->subject($subject);
        }
    }

    private function addBody(SymfonyEmail $email, Email $legacyEmail, array $cidMapping): void
    {
        $isHtml = ($legacyEmail->ContentType ?? PHPMailer::CONTENT_TYPE_PLAINTEXT)
            === PHPMailer::CONTENT_TYPE_TEXT_HTML;
        $charset = $legacyEmail->getCharset() ?: PHPMailer::CHARSET_UTF8;
        $body = $legacyEmail->getBody();

        if ($body) {
            if ($isHtml) {
                $email->html($this->updateCidReferences($body, $cidMapping), $charset);
            } else {
                $email->text($body, $charset);
            }
        }

        $altBody = $legacyEmail->getAltBody();
        if ($isHtml && $altBody) {
            $email->text($altBody, $charset);
        }
    }

    private function updateCidReferences(string $body, array $cidMapping): string
    {
        foreach ($cidMapping as $originalCid => $normalizedCid) {
            if ($originalCid !== $normalizedCid) {
                $body = str_replace('cid:' . $originalCid, 'cid:' . $normalizedCid, $body);
            }
        }

        return $body;
    }

    private function addAttachments(SymfonyEmail $email, Email $legacyEmail, array $cidMapping): void
    {
        foreach ($legacyEmail->getAttachments() as $attachment) {
            $this->addAttachment(
                $email,
                $attachment[0],
                $attachment[2] ?? '',
                $attachment[4] ?? null,
                $attachment[6] ?? 'attachment',
                $this->resolveAttachmentCid($attachment[7] ?? null, $cidMapping),
                (bool) ($attachment[5] ?? false)
            );
        }
    }

    private function resolveAttachmentCid(mixed $cid, array $cidMapping): ?string
    {
        if (!is_string($cid) || $cid === '') {
            return null;
        }

        return $cidMapping[$cid] ?? $cid;
    }

    private function addAttachment(
        SymfonyEmail $email,
        string $content,
        string $name,
        ?string $mimeType,
        string $disposition,
        ?string $cid,
        bool $isString
    ): void {
        if ($disposition === 'inline' && $cid !== null) {
            $part = $isString
                ? (new DataPart($content, $name, $mimeType))->asInline()
                : DataPart::fromPath($content, $name, $mimeType)->asInline();
            $part->setContentId($cid);
            $email->addPart($part);
            return;
        }

        if ($isString) {
            $email->attach($content, $name, $mimeType);
        } else {
            $email->attachFromPath($content, $name, $mimeType);
        }
    }

    private function addCustomHeaders(SymfonyEmail $email, Email $legacyEmail): void
    {
        foreach ($legacyEmail->getCustomHeaders() as $header) {
            $email->getHeaders()->addTextHeader($header[0], $header[1]);
        }
    }

    private function addPriority(SymfonyEmail $email, Email $legacyEmail): void
    {
        $priority = $legacyEmail->Priority ?? null;
        if ($priority === null) {
            return;
        }

        $email->priority(match ($priority) {
            1 => SymfonyEmail::PRIORITY_HIGHEST,
            2 => SymfonyEmail::PRIORITY_HIGH,
            4 => SymfonyEmail::PRIORITY_LOW,
            5 => SymfonyEmail::PRIORITY_LOWEST,
            default => SymfonyEmail::PRIORITY_NORMAL,
        });
    }

    private function addSender(SymfonyEmail $email, Email $legacyEmail): void
    {
        $sender = $legacyEmail->Sender ?? null;
        if ($sender) {
            $email->returnPath($sender);
        }
    }
}
