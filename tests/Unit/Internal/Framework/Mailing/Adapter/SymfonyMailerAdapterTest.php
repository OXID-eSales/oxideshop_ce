<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Mailing\Adapter;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\EshopCommunity\Internal\Transition\Adapter\Email\SymfonyMailerAdapter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mime\Email as SymfonyEmail;

class SymfonyMailerAdapterTest extends TestCase
{
    public function testConvertBasicEmail(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->setSubject('Test Subject');
        $legacyEmail->setBody('<p>Test Body</p>');
        $legacyEmail->setAltBody('Test Body');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $this->assertEquals('Test Subject', $symfonyEmail->getSubject());
        $this->assertEquals('<p>Test Body</p>', $symfonyEmail->getHtmlBody());
        $this->assertEquals('Test Body', $symfonyEmail->getTextBody());
    }

    public function testConvertWithMultipleRecipients(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->setRecipient('second@example.com', 'Second User');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $to = $symfonyEmail->getTo();
        $this->assertCount(2, $to);
        $this->assertEquals('test@example.com', $to[0]->getAddress());
        $this->assertEquals('second@example.com', $to[1]->getAddress());
    }

    public function testConvertWithFromName(): void
    {
        $legacyEmail = $this->createBasicEmail();

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $from = $symfonyEmail->getFrom();
        $this->assertCount(1, $from);
        $this->assertEquals('sender@example.com', $from[0]->getAddress());
        $this->assertEquals('Sender Name', $from[0]->getName());
    }

    public function testConvertWithReplyTo(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->setReplyTo('reply@example.com', 'Reply User');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $replyTo = $symfonyEmail->getReplyTo();
        $this->assertCount(1, $replyTo);
        $this->assertEquals('reply@example.com', $replyTo[0]->getAddress());
        $this->assertEquals('Reply User', $replyTo[0]->getName());
    }

    public function testConvertWithCcAndBcc(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->addCC('cc@example.com', 'CC User');
        $legacyEmail->addBCC('bcc@example.com', 'BCC User');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $cc = $symfonyEmail->getCc();
        $bcc = $symfonyEmail->getBcc();

        $this->assertCount(1, $cc);
        $this->assertEquals('cc@example.com', $cc[0]->getAddress());
        $this->assertEquals('CC User', $cc[0]->getName());

        $this->assertCount(1, $bcc);
        $this->assertEquals('bcc@example.com', $bcc[0]->getAddress());
        $this->assertEquals('BCC User', $bcc[0]->getName());
    }

    public function testConvertPlainTextEmail(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->isHTML(false);
        $legacyEmail->setBody('Plain text body');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $this->assertNull($symfonyEmail->getHtmlBody());
        $this->assertEquals('Plain text body', $symfonyEmail->getTextBody());
    }

    public function testConvertHtmlEmailWithAltBody(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->isHTML(true);
        $legacyEmail->setBody('<p>HTML body</p>');
        $legacyEmail->setAltBody('Plain text alternative');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $this->assertEquals('<p>HTML body</p>', $symfonyEmail->getHtmlBody());
        $this->assertEquals('Plain text alternative', $symfonyEmail->getTextBody());
    }

    public function testConvertWithStringAttachment(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->addStringAttachment('string content', 'file.txt', 'base64', 'text/plain');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $attachments = $symfonyEmail->getAttachments();
        $this->assertCount(1, $attachments);
    }

    public function testConvertWithCustomHeaders(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->addCustomHeader('X-Custom-Header', 'CustomValue');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $headers = $symfonyEmail->getHeaders();

        $this->assertTrue($headers->has('X-Custom-Header'));
        $this->assertEquals('CustomValue', $headers->get('X-Custom-Header')->getBodyAsString());
    }

    public function testConvertWithPriority(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->Priority = 1;

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $this->assertEquals(SymfonyEmail::PRIORITY_HIGHEST, $symfonyEmail->getPriority());
    }

    public function testConvertWithLowPriority(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->Priority = 5;

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $this->assertEquals(SymfonyEmail::PRIORITY_LOWEST, $symfonyEmail->getPriority());
    }

    public function testConvertWithSender(): void
    {
        $legacyEmail = $this->createBasicEmail();
        $legacyEmail->Sender = 'bounce@example.com';

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $returnPath = $symfonyEmail->getReturnPath();
        $this->assertNotNull($returnPath);
        $this->assertEquals('bounce@example.com', $returnPath->getAddress());
    }

    public function testCidMappingNormalizesCidWithoutAtSymbol(): void
    {
        $cid = 'abc123def456';
        $body = '<p>Image: <img src="cid:' . $cid . '"></p>';

        $legacyEmail = $this->createStub(Email::class);
        $legacyEmail->method('getRecipient')->willReturn([['test@example.com', 'Test']]);
        $legacyEmail->method('getFrom')->willReturn('from@example.com');
        $legacyEmail->method('getFromName')->willReturn('From Name');
        $legacyEmail->method('getSubject')->willReturn('Test Subject');
        $legacyEmail->method('getBody')->willReturn($body);
        $legacyEmail->method('getAltBody')->willReturn('');
        $legacyEmail->method('getCharset')->willReturn('UTF-8');
        $legacyEmail->method('getReplyTo')->willReturn([]);
        $legacyEmail->method('getCc')->willReturn([]);
        $legacyEmail->method('getBcc')->willReturn([]);
        $legacyEmail->method('getCustomHeaders')->willReturn([]);
        $legacyEmail->method('getAttachments')->willReturn([
            [
                0 => 'image data',
                1 => 'image.png',
                2 => 'image.png',
                3 => 'base64',
                4 => 'image/png',
                5 => true,
                6 => 'inline',
                7 => $cid,
            ]
        ]);
        $legacyEmail->ContentType = 'text/html';

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $attachments = $symfonyEmail->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertEquals($cid . '@generated', $attachments[0]->getContentId());
        $this->assertStringContainsString('cid:' . $cid . '@generated', $symfonyEmail->getHtmlBody());
    }

    public function testCidMappingPreservesCidWithAtSymbol(): void
    {
        $cid = 'abc123@example.com';
        $body = '<p>Image: <img src="cid:' . $cid . '"></p>';

        $legacyEmail = $this->createStub(Email::class);
        $legacyEmail->method('getRecipient')->willReturn([['test@example.com', 'Test']]);
        $legacyEmail->method('getFrom')->willReturn('from@example.com');
        $legacyEmail->method('getFromName')->willReturn('From Name');
        $legacyEmail->method('getSubject')->willReturn('Test Subject');
        $legacyEmail->method('getBody')->willReturn($body);
        $legacyEmail->method('getAltBody')->willReturn('');
        $legacyEmail->method('getCharset')->willReturn('UTF-8');
        $legacyEmail->method('getReplyTo')->willReturn([]);
        $legacyEmail->method('getCc')->willReturn([]);
        $legacyEmail->method('getBcc')->willReturn([]);
        $legacyEmail->method('getCustomHeaders')->willReturn([]);
        $legacyEmail->method('getAttachments')->willReturn([
            [
                0 => 'image data',
                1 => 'image.png',
                2 => 'image.png',
                3 => 'base64',
                4 => 'image/png',
                5 => true,
                6 => 'inline',
                7 => $cid,
            ]
        ]);
        $legacyEmail->ContentType = 'text/html';

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $attachments = $symfonyEmail->getAttachments();
        $this->assertCount(1, $attachments);
        $this->assertEquals($cid, $attachments[0]->getContentId());
        $this->assertStringContainsString('cid:' . $cid, $symfonyEmail->getHtmlBody());
    }

    public function testCidMappingWithMultipleInlineImages(): void
    {
        $cid1 = 'image1abc';
        $cid2 = 'image2@domain.com';
        $body = '<p><img src="cid:' . $cid1 . '"><img src="cid:' . $cid2 . '"></p>';

        $legacyEmail = $this->createStub(Email::class);
        $legacyEmail->method('getRecipient')->willReturn([['test@example.com', 'Test']]);
        $legacyEmail->method('getFrom')->willReturn('from@example.com');
        $legacyEmail->method('getFromName')->willReturn('From Name');
        $legacyEmail->method('getSubject')->willReturn('Test');
        $legacyEmail->method('getBody')->willReturn($body);
        $legacyEmail->method('getAltBody')->willReturn('');
        $legacyEmail->method('getCharset')->willReturn('UTF-8');
        $legacyEmail->method('getReplyTo')->willReturn([]);
        $legacyEmail->method('getCc')->willReturn([]);
        $legacyEmail->method('getBcc')->willReturn([]);
        $legacyEmail->method('getCustomHeaders')->willReturn([]);
        $legacyEmail->method('getAttachments')->willReturn([
            [
                0 => 'image1 data',
                1 => 'image1.png',
                2 => 'image1.png',
                3 => 'base64',
                4 => 'image/png',
                5 => true,
                6 => 'inline',
                7 => $cid1,
            ],
            [
                0 => 'image2 data',
                1 => 'image2.png',
                2 => 'image2.png',
                3 => 'base64',
                4 => 'image/png',
                5 => true,
                6 => 'inline',
                7 => $cid2,
            ]
        ]);
        $legacyEmail->ContentType = 'text/html';

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $htmlBody = $symfonyEmail->getHtmlBody();

        $this->assertStringContainsString('cid:' . $cid1 . '@generated', $htmlBody);
        $this->assertStringContainsString('cid:' . $cid2, $htmlBody);
        $this->assertStringNotContainsString('cid:' . $cid2 . '@generated', $htmlBody);
    }

    private function createBasicEmail(): Email
    {
        $legacyEmail = new Email();
        $legacyEmail->setRecipient('test@example.com', 'Test User');
        $legacyEmail->setFrom('sender@example.com', 'Sender Name');
        $legacyEmail->setSubject('Test');
        $legacyEmail->setBody('Body');

        return $legacyEmail;
    }
}
