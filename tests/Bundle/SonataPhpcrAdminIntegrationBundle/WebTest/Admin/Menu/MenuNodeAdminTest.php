<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin\Menu;

use Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\DataFixtures\Phpcr\LoadMenuData;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class MenuNodeAdminTest extends BaseTestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\Kernel::class;
    }

    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures([LoadMenuData::class]);
    }

    public function testEdit()
    {
        $this->client->request('GET', '/admin/cmf/menu/menunode/test/menus/test-menu/item-1/edit');

        $this->assertResponseSuccess($this->client->getResponse());
    }

    public function testDelete()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menunode/test/menus/test-menu/item-2/delete');
        $this->assertResponseSuccess($this->client->getResponse());

        $button = $crawler->selectButton('Yes, delete');
        $form = $button->form();
        $crawler = $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode());

        $documentManager = $this->client->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $menuItem = $documentManager->find(null, '/test/menus/test-menu/item-2');
        $this->assertNull($menuItem);
    }
}
