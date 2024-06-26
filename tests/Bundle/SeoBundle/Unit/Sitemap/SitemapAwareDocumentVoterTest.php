<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SeoBundle\Unit\Sitemap;

use Symfony\Cmf\Bundle\SeoBundle\Sitemap\SitemapAwareDocumentVoter;
use Symfony\Cmf\Bundle\SeoBundle\Sitemap\VoterInterface;
use Symfony\Cmf\Bundle\SeoBundle\SitemapAwareInterface;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class SitemapAwareDocumentVoterTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var VoterInterface
     */
    protected $voter;

    protected $sitemapAwareDocument;

    public function setUp(): void
    {
        $this->voter = new SitemapAwareDocumentVoter();
        $this->sitemapAwareDocument = $this->createMock(SitemapAwareInterface::class);
    }

    public function testSitemapAwareDocumentShouldReturnTrueWhenDocumentIsExposed()
    {
        $this->sitemapAwareDocument
            ->expects($this->once())
            ->method('isVisibleInSitemap')
            ->will($this->returnValue(true));
        $this->assertTrue($this->voter->exposeOnSitemap($this->sitemapAwareDocument, 'some-sitemap'));
    }

    public function testSitemapAwareDocumentShouldReturnFalseWhenDocumentIsNotExposed()
    {
        $this->sitemapAwareDocument
            ->expects($this->once())
            ->method('isVisibleInSitemap')
            ->will($this->returnValue(false));
        $this->assertFalse($this->voter->exposeOnSitemap($this->sitemapAwareDocument, 'some-sitemap'));
    }

    public function testInvalidDocumentShouldReturnTrueToBeAwareForTheOtherVoters()
    {
        $this->assertTrue($this->voter->exposeOnSitemap(new \stdClass(), 'some-sitemap'));
    }
}
