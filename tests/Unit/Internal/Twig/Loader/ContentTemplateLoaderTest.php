<?php
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Twig\Loader;

use OxidEsales\EshopCommunity\Application\Model\Content;
use OxidEsales\EshopCommunity\Internal\Adapter\TemplateLogic\ContentFactory;
use OxidEsales\EshopCommunity\Internal\Twig\Loader\ContentTemplateLoader;
use OxidEsales\EshopCommunity\Internal\Twig\TemplateLoaderNameParser;
use PHPUnit\Framework\MockObject\MockBuilder;
use PHPUnit\Framework\TestCase;

/**
 * Class ContentTemplateLoaderTest
 *
 * @author Tomasz Kowalewski (t.kowalewski@createit.pl)
 */
class ContentTemplateLoaderTest extends TestCase
{

    /** @var ContentTemplateLoader */
    private $contentTemplateLoader;

    /** @var MockBuilder */
    private $contentMockBuilder;

    /**
     * {@inheritdoc}
     */
    public function setUp()
    {
        $this->contentMockBuilder = $this->getMockBuilder(Content::class)->setMethods(['getLanguage']);

        $validContentMock = $this->prepareContentMock(
            0,
            ['oxactive' => true, 'oxcontent' => "Template code (DE)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $englishContentMock = $this->prepareContentMock(
            1,
            ['oxactive' => true, 'oxcontent' => "Template code (EN)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $fieldContentMock = $this->prepareContentMock(
            0,
            ['oxactive' => true, 'customfield' => "Template code (custom field)", 'oxtimestamp' => '2018-10-09 09:32:06']
        );

        $notFreshContentMock = $this->prepareContentMock(
            0,
            ['oxactive' => true, 'oxtimestamp' => '2018-10-09 09:40:25']
        );

        $notValidContentMock = $this->prepareContentMock(0, ['oxactive' => false]);

        $contentFactoryMock = $this
            ->getMockBuilder(ContentFactory::class)
            ->setMethods(['getContent'])
            ->getMock();

        $contentFactoryMock
            ->method('getContent')
            ->will(
                $this->returnValueMap(
                    [
                        ['ident', 'valid', $validContentMock],
                        ['oxid', 'english', $englishContentMock],
                        ['ident', 'field', $fieldContentMock],
                        ['oxid', 'notFresh', $notFreshContentMock],
                        ['ident', 'notValid', $notValidContentMock]
                    ]
                )
            );

        /** @var ContentFactory $contentFactoryMock */
        $this->contentTemplateLoader = new ContentTemplateLoader(new TemplateLoaderNameParser(), $contentFactoryMock);
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function testGetSourceContext()
    {
        $this->assertEquals(
            "Template code (DE)",
            $this->contentTemplateLoader->getSourceContext('content::ident::valid')->getCode()
        );

        $this->assertEquals(
            "Template code (EN)",
            $this->contentTemplateLoader->getSourceContext('content::oxid::english')->getCode()
        );

        $this->assertEquals(
            "Template code (custom field)",
            $this->contentTemplateLoader->getSourceContext('content::ident::field?field=customfield')->getCode()
        );
    }

    /**
     * testExists
     */
    public function testExists()
    {
        $this->assertTrue($this->contentTemplateLoader->exists('content::ident::valid'));
        $this->assertTrue($this->contentTemplateLoader->exists('content::oxid::english'));
        $this->assertTrue($this->contentTemplateLoader->exists('content::ident::field?field=customfield'));
        $this->assertTrue($this->contentTemplateLoader->exists('content::oxid::notFresh'));

        $this->assertFalse($this->contentTemplateLoader->exists('invalidName'));
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function testIsFresh()
    {
        $time = strtotime('2018-10-09 09:37:16');
        $this->assertTrue($this->contentTemplateLoader->isFresh('content::ident::valid', $time));
        $this->assertTrue($this->contentTemplateLoader->isFresh('content::oxid::english', $time));
        $this->assertTrue($this->contentTemplateLoader->isFresh('content::ident::field?field=customfield', $time));

        $this->assertFalse($this->contentTemplateLoader->isFresh('content::oxid::notFresh', $time));
    }

    /**
     * @throws \Twig\Error\LoaderError
     */
    public function testGetCacheKey()
    {
        $this->assertEquals(
            'content::ident::valid(0)',
            $this->contentTemplateLoader->getCacheKey('content::ident::valid')
        );

        $this->assertEquals(
            'content::oxid::english(1)',
            $this->contentTemplateLoader->getCacheKey('content::oxid::english')
        );

        $this->assertEquals(
            'content::ident::field?field=customfield(0)',
            $this->contentTemplateLoader->getCacheKey('content::ident::field?field=customfield')
        );
    }

    /**
     * @param int   $language
     * @param array $fields
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function prepareContentMock($language, array $fields)
    {
        $mock = $this->contentMockBuilder->getMock();
        $mock->method('getLanguage')->willReturn($language);

        foreach ($fields as $field => $value) {
            $fieldName = 'oxcontents__' . $field;
            $mock->$fieldName = (object) ['value' => $value];
        }

        return $mock;
    }
}
