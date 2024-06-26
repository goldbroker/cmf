<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\WebTest\Admin\Block;

use Symfony\Cmf\Bundle\BlockBundle\Block\PhpcrBlockLoader;
use Symfony\Cmf\Component\Testing\Functional\BaseTestCase;

class PHPCRBlockLoaderTest extends BaseTestCase
{
    public function setUp(): void
    {
        $this->db('PHPCR')->loadFixtures([
            'Tests\Symfony\Cmf\Bundle\SonataPhpcrAdminIntegrationBundle\Fixtures\App\DataFixtures\Phpcr\LoadBlockData',
        ]);
        $this->client = $this->createClient();
    }

    public function testGetUnpublished()
    {
        /** @var $service PhpcrBlockLoader */
        $service = $this->client->getContainer()->get('cmf.block.service');
        $this->assertInstanceOf('Symfony\Cmf\Bundle\BlockBundle\Doctrine\Phpcr\SimpleBlock', $service->load(['name' => '/test/blocks/block-1']));
        // this block is not published, should be empty
        $this->assertInstanceOf('Sonata\BlockBundle\Model\EmptyBlock', $service->load(['name' => '/test/blocks/block-2']));
    }
}
