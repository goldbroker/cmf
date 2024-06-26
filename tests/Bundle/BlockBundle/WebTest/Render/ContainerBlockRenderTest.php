<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\BlockBundle\WebTest\Render;

use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

/**
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class ContainerBlockRenderTest extends BaseTestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\BlockBundle\Fixtures\App\Kernel::class;
    }

    /**
     * {@inheritdoc}
     */
    public function setUp(): void
    {
        $this->client = $this->createClient();
        $this->db('PHPCR')->loadFixtures([
            'Tests\Symfony\Cmf\Bundle\BlockBundle\Fixtures\App\DataFixtures\Phpcr\LoadBlockData',
        ]);
    }

    public function testRenderContainerTwig()
    {
        $crawler = $this->client->request('GET', '/render-container-test');

        $res = $this->client->getResponse();
        $this->assertEquals(200, $res->getStatusCode());

        $this->assertCount(1, $crawler->filter('html:contains("block-child-1-title")'));
        $this->assertCount(1, $crawler->filter('html:contains("block-child-1-body")'));
    }
}
