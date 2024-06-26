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
 * @author Nicolas Bastien <nbastien@prestaconcept.net>
 */
class MenuBlockAdminTest extends AbstractBlockAdminTestCase
{
    /**
     * {@inheritdoc}
     */
    public function testBlockList()
    {
        $this->makeListAssertions(
            '/admin/cmf/block/menublock/list',
            ['menu-block-1', 'menu-block-2']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockEdit()
    {
        $this->makeEditAssertions(
            '/admin/cmf/block/menublock/test/blocks/menu-block-1/edit',
            ['menu-block-1']
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockCreate()
    {
        $this->makeCreateAssertions(
            '/admin/cmf/block/menublock/create',
            [
                'parentDocument' => '/test/blocks',
                'name' => 'foo-test-container',
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockDelete()
    {
        $this->makeDeleteAssertions('/admin/cmf/block/menublock/test/blocks/menu-block-1/delete');
    }

    /**
     * {@inheritdoc}
     */
    public function testBlockShow()
    {
        $this->makeShowAssertions(
            '/admin/cmf/block/menublock/test/blocks/menu-block-1/show',
            ['menu-block-1']
        );
    }
}
