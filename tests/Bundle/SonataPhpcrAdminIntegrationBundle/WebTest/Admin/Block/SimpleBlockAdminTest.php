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

/**
 * @author David Buchmann <david@liip.ch>
 */
class SimpleBlockAdminTest extends AbstractBlockAdminTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testBlockList()
    {
        $this->makeListAssertions(
            '/admin/cmf/block/simpleblock/list',
            ['block-1', 'block-1-title', 'block-2']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/simpleblock/test/blocks/block-1/edit',
            ['block-1', 'block-1-title']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/simpleblock/create',
            [
                'parentDocument' => '/test/blocks',
                'name' => 'foo-test',
                'title' => 'Foo Test',
                'body' => 'Block body foo bar.',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockDelete()
    {
        $this->makeDeleteAssertions('/admin/cmf/block/simpleblock/test/blocks/block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/simpleblock/test/blocks/block-1/show',
            ['block-1']
        );
    }
}
