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

class MenuAdminTest extends BaseTestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\Kernel::class;
    }

    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures([LoadMenuData::class]);
        $this->documentManager = $this->client->getContainer()->get('doctrine_phpcr.odm.document_manager');
    }

    public function testMenuList()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/list');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);
        $this->assertCount(1, $crawler->filter('html:contains("test-menu")'), $res->getContent());
    }

    public function testMenuEdit()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/test/menus/test-menu/edit');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);
        $this->assertCount(1, $crawler->filter('input[value="test-menu"]'), $res->getContent());
    }

    public function testMenuShow()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/test/menus/test-menu/show');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);
        $this->assertCount(2, $crawler->filter('td:contains("test-menu")'), $res->getContent());
    }

    public function testMenuCreate()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/create');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);

        $button = $crawler->selectButton('Create');
        $form = $button->form();
        $node = $form->getFormNode();
        $actionUrl = $node->getAttribute('action');
        $uniqId = substr(strstr($actionUrl, '='), 1);

        $form[$uniqId.'[name]'] = 'foo-test';
        $form[$uniqId.'[label]'] = 'Foo Test';

        $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode(), $res->getContent());
    }

    public function testMenuDelete()
    {
        $crawler = $this->client->request('GET', '/admin/cmf/menu/menu/test/menus/test-menu/delete');
        $res = $this->client->getResponse();
        $this->assertResponseSuccess($res);

        $button = $crawler->selectButton('Yes, delete');
        $form = $button->form();
        $crawler = $this->client->submit($form);
        $res = $this->client->getResponse();

        // If we have a 302 redirect, then all is well
        $this->assertEquals(302, $res->getStatusCode());

        $documentManager = $this->client->getContainer()->get('doctrine_phpcr.odm.document_manager');
        $menu = $documentManager->find(null, '/test/menus/test-menu');
        $this->assertNull($menu);
    }
}
