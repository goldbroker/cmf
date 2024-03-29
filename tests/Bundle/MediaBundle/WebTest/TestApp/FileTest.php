<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\MediaBundle\WebTest\TestApp;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class FileTest extends BaseTestCase
{
    private $testDataDir;

    public function setUp(): void
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Tests\Symfony\Cmf\Bundle\MediaBundle\Resources\DataFixtures\Phpcr\LoadMediaData',
        ));
        $this->testDataDir = $this->getContainer()->get('kernel')->getRootDir().'/Resources/data';
        $this->client = $this->createClient();
    }

    public function testPage()
    {
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_file_test'));
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        // 2 files and 2 images
        $this->assertGreaterThanOrEqual(4, $crawler->filter('.downloads li a')->count());
    }

    public function testUpload()
    {
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_file_test'));
        $cntDownloadLinks = $crawler->filter('.downloads li a')->count();

        $buttonCrawlerNode = $crawler->filter('form.standard')->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['file']->upload($this->testDataDir.'/testfile.txt');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($cntDownloadLinks + 1, $crawler->filter('.downloads li a')->count());
    }

    public function testEditorUpload()
    {
        $this->client = $this->createClient(array(), array(
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'adminpass',
        ));
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_file_test'));
        $cntDownloadLinks = $crawler->filter('.downloads li a')->count();

        $buttonCrawlerNode = $crawler->filter('form.editor.default')->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['file']->upload($this->testDataDir.'/testfile.txt');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($cntDownloadLinks + 1, $crawler->filter('.downloads li a')->count());
    }

    public function testDownload()
    {
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_file_test'));

        // find first download link
        $link = $crawler->filter('.downloads li a')->eq(0)->link();
        $this->client->click($link);
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertInstanceOf('Symfony\Component\HttpFoundation\BinaryFileResponse', $resp);
    }
}
