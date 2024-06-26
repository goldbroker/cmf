<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin\Seo\Extension;

use Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin\TestCase;

/**
 * This test will cover all behavior with the provides admin extension.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class SeoContentAdminExtensionTest extends TestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\Kernel::class;
    }

    public function setUp(): void
    {
        $this->client = $this->createClient();
    }

    public function testAdminExtensionExists()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/seo/seoawarecontent/list');

        $this->assertResponseSuccess($this->client->getResponse());
        $this->assertCount(1, $crawler->filter('html:contains("content-1")'));
    }

    public function testItemEditView()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/seo/seoawarecontent/test/content/content-1/edit');

        $this->assertResponseSuccess($this->client->getResponse());

        $this->assertCount(1, $crawler->filter('html:contains("SEO")'));
        $this->assertCount(1, $crawler->filter('html:contains("Title")'));
        $this->assertCount(1, $crawler->filter('html:contains("Original URL")'));
        $this->assertCount(1, $crawler->filter('html:contains("description")'));
        $this->assertCount(1, $crawler->filter('html:contains("keywords")'));
    }

    public function testItemCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/seo/seoawarecontent/create');

        $this->assertResponseSuccess($this->client->getResponse());

        $this->assertCount(1, $crawler->filter('html:contains("SEO")'));
        $this->assertCount(1, $crawler->filter('html:contains("Title")'));
        $this->assertCount(1, $crawler->filter('html:contains("Original URL")'));
        $this->assertCount(1, $crawler->filter('html:contains("description")'));
        $this->assertCount(1, $crawler->filter('html:contains("keywords")'));
    }
}
