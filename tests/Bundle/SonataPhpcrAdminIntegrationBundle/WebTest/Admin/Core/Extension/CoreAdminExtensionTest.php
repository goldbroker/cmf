<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin\Core\Extension;

use Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin\TestCase;

/**
 * This test will cover all behavior with the provides admin extension.
 *
 * @author Maximilian Berghoff <Maximilian.Berghoff@gmx.de>
 */
class CoreAdminExtensionTest extends TestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\Kernel::class;
    }

    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures([
            'Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\DataFixtures\Phpcr\LoadCoreData',
        ]);
    }

    public function testItemEditView()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/core/extensions/test/core/with-extensions/edit');

        $this->assertResponseSuccess($this->client->getResponse());

        $this->assertCount(1, $crawler->filter('html:contains("Publishable")'));
        $this->assertCount(1, $crawler->filter('html:contains("Publish from")'));
        $this->assertCount(1, $crawler->filter('html:contains("Publish until")'));
    }

    public function testItemCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/core/extensions/create');

        $this->assertResponseSuccess($this->client->getResponse());

        $this->assertCount(1, $crawler->filter('html:contains("Publishable")'));
        $this->assertCount(1, $crawler->filter('html:contains("Publish from")'));
        $this->assertCount(1, $crawler->filter('html:contains("Publish until")'));
    }
}
