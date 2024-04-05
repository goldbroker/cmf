<?php

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin;

use Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\DataFixtures\Phpcr\LoadContentData;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Maximilian Berghoff <Maximilian.Berghoff@mayflower.de>
 */
abstract class TestCase extends BaseTestCase
{
    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures([
            LoadContentData::class,
        ]);
    }

    public function testAdminDashboard()
    {
        $this->client->request('GET', '/admin/dashboard');

        $this->assertResponseSuccess($this->client->getResponse());
    }

    abstract public function testItemEditView();

    abstract public function testItemCreate();
}
