<?php

/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

declare(strict_types=1);

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Framework\Mailing\Adapter;

use OxidEsales\Eshop\Core\Email;
use OxidEsales\EshopCommunity\Internal\Framework\Mailing\Adapter\SymfonyMailerAdapter;
use PHPUnit\Framework\TestCase;

class SymfonyMailerAdapterTest extends TestCase
{
    public function testConvertBasicEmail(): void
    {
        $legacyEmail = new Email();
        $legacyEmail->setRecipient('test@example.com', 'Test User');
        $legacyEmail->setFrom('sender@example.com', 'Sender Name');
        $legacyEmail->setSubject('Test Subject');
        $legacyEmail->setBody('<p>Test Body</p>');
        $legacyEmail->setAltBody('Test Body');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $this->assertEquals('Test Subject', $symfonyEmail->getSubject());
        $this->assertEquals('<p>Test Body</p>', $symfonyEmail->getHtmlBody());
        $this->assertEquals('Test Body', $symfonyEmail->getTextBody());
    }

    public function testConvertWithCcAndBcc(): void
    {
        $legacyEmail = new Email();
        $legacyEmail->setRecipient('test@example.com', 'Test');
        $legacyEmail->setFrom('sender@example.com');
        $legacyEmail->setSubject('Test');
        $legacyEmail->setBody('Body');
        $legacyEmail->addCC('cc@example.com', 'CC User');
        $legacyEmail->addBCC('bcc@example.com', 'BCC User');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $cc = $symfonyEmail->getCc();
        $bcc = $symfonyEmail->getBcc();

        $this->assertEquals('cc@example.com', $cc[0]->getAddress());
        $this->assertEquals('bcc@example.com', $bcc[0]->getAddress());
    }

    public function testConvertWithAttachments(): void
    {
        $testFile = tempnam(sys_get_temp_dir(), 'test');
        file_put_contents($testFile, 'test content');

        $legacyEmail = new Email();
        $legacyEmail->setRecipient('test@example.com', 'Test');
        $legacyEmail->setFrom('sender@example.com');
        $legacyEmail->setSubject('Test');
        $legacyEmail->setBody('Body');
        $legacyEmail->addAttachment($testFile, 'file.txt');
        $legacyEmail->addEmbeddedImage($testFile, 'cid1', 'image.txt');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $attachments = $symfonyEmail->getAttachments();

        $this->assertCount(2, $attachments);

        unlink($testFile);
    }

    public function testConvertWithCustomHeaders(): void
    {
        $legacyEmail = new Email();
        $legacyEmail->setRecipient('test@example.com', 'Test');
        $legacyEmail->setFrom('sender@example.com');
        $legacyEmail->setSubject('Test');
        $legacyEmail->setBody('Body');
        $legacyEmail->addCustomHeader('X-Custom-Header', 'CustomValue');

        $adapter = new SymfonyMailerAdapter();
        $symfonyEmail = $adapter->convertToSymfonyEmail($legacyEmail);

        $headers = $symfonyEmail->getHeaders();

        $this->assertTrue($headers->has('X-Custom-Header'));
        $this->assertEquals('CustomValue', $headers->get('X-Custom-Header')->getBodyAsString());
    }
}
