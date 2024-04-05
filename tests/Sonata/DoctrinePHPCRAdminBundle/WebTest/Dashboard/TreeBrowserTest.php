<?php

declare(strict_types=1);

/*
 * This file is part of the Sonata Project package.
 *
 * (c) Thomas Rabaix <thomas.rabaix@sonata-project.org>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Sonata\DoctrinePHPCRAdminBundle\WebTest\Dashboard;

use Tests\Sonata\DoctrinePHPCRAdminBundle\Fixtures\App\DataFixtures\Phpcr\LoadTreeData;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
class TreeBrowserTest extends BaseTestCase
{
    protected function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures([LoadTreeData::class]);
    }

    public function testTreeOnDashboardLoadsWithNoErrors(): void
    {
        $crawler = $this->client->request('GET', '/admin/dashboard');
        $res = $this->client->getResponse();

        $this->assertResponseSuccess($res);

        static::assertCount(1, $crawler->filter('div#tree'));
    }
}
