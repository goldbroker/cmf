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
use Symfony\Component\HttpFoundation\Response;

class ImageTest extends BaseTestCase
{
    private $testDataDir;

    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\MediaBundle\Fixtures\App\Kernel::class;
    }

    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures(array(
            'Tests\Symfony\Cmf\Bundle\MediaBundle\Resources\DataFixtures\Phpcr\LoadMediaData',
        ));
        $this->testDataDir = $this->getContainer()->get('kernel')->getProjectDir().'/Resources/data';
    }

    public function testPage()
    {
        // Clear cache
        $cacheManager = $this->getContainer()->get('liip_imagine.cache.manager');
        $cacheManager->remove('test/media/cmf-logo.png');

        $this->assertFalse($cacheManager->isStored('test/media/cmf-logo.png', 'image_upload_thumbnail'));

        // get crawler
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());

        // image(s) display
        $this->assertGreaterThanOrEqual(4, $crawler->filter('.images li img')->count());

        // cmf_media_image form tests
        $this->assertEquals(0, $crawler->filter('.cmf_media_image.new img')->count());
        $this->assertEquals(1, $crawler->filter('.cmf_media_image.edit.default img')->count());
        $this->assertEquals(1, $crawler->filter('.cmf_media_image.edit.imagine img')->count());

        // cmf_media_display_url
        $defaultImageLink = $crawler->filter('.images li img.default')->first()->attr('src');
        $this->client->request('get', $defaultImageLink);
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode(), 'default image test');

        // imagine_filter
        $imagineImageLink = $crawler->filter('.images li img.imagine')->first()->attr('src');
        $this->client->request('get', $imagineImageLink);

        $this->assertTrue($this->client->getResponse()->isRedirection(), 'imagine image test');
        $this->assertEquals(301, $this->client->getResponse()->getStatusCode(), 'imagine image test');
        $this->assertEquals('http://localhost/media/cache/image_upload_thumbnail/test/media/cmf-logo.png', $this->client->getResponse()->getTargetUrl());

        $this->assertTrue($cacheManager->isStored('test/media/cmf-logo.png', 'image_upload_thumbnail'));
    }

    public function testUpload()
    {
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $cntImagesLinks = $crawler->filter('.images li img')->count();

        $buttonCrawlerNode = $crawler->filter('form.standard')->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['image']->upload($this->testDataDir.'/testimage.png');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($cntImagesLinks + 2, $crawler->filter('.images li img')->count());
    }

    public function testEditorUpload()
    {
        $auth = [
            'PHP_AUTH_USER' => 'admin',
            'PHP_AUTH_PW' => 'adminpass',
        ];
        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'), $auth);
        $cntImagesLinks = $crawler->filter('.images li img')->count();

        $buttonCrawlerNode = $crawler->filter('form.editor.default')->selectButton('submit');
        $form = $buttonCrawlerNode->form();
        $form['image']->upload($this->testDataDir.'/testimage.png');

        $this->client->submit($form);
        $crawler = $this->client->followRedirect();
        /** @var Response $resp */
        $resp = $this->client->getResponse();

        $this->assertEquals(200, $resp->getStatusCode());
        // check that the content is not empty, this could be caused by the stream cursor that is not at the beginning
        // when doctrine persist a file object
        $this->assertNotEmpty($resp->getContent());
        $this->assertEquals('image/png', $resp->headers->get('Content-Type')); // check that the response is an image

        $crawler = $this->client->request('get', $this->getContainer()->get('router')->generate('phpcr_image_test'));
        $resp = $this->client->getResponse();
        $this->assertEquals(200, $resp->getStatusCode());
        $this->assertEquals($cntImagesLinks + 2, $crawler->filter('.images li img')->count());
    }
}
