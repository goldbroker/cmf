<?php

/*
 * This file is part of the Symfony CMF package.
 *
 * (c) 2011-2017 Symfony CMF
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Symfony\Cmf\Bundle\ResourceBundle\Unit\DependencyInjection\Repository\Factory;

use Puli\Repository\FilesystemRepository;
use Symfony\Cmf\Bundle\ResourceBundle\DependencyInjection\Repository\Factory\FilesystemFactory;
use Symfony\Component\OptionsResolver\Exception\MissingOptionsException;

class FilesystemFactoryTest extends FactoryTestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped('Puli Filesystem is not available.');
    }

    /**
     * It should add a repository to the container.
     * It should configure the base dir.
     */
    public function testCreate()
    {
        $container = $this->buildContainer(
            $this->resolveOptions([
                'base_dir' => __DIR__,
                'symlink' => true,
            ])
        );

        $this->assertInstanceOf(FilesystemRepository::class, $container->get('repository'));
        $repository = $container->get('repository');
        $resource = $repository->get('/'.basename(__FILE__));

        $this->assertEquals(__FILE__, $resource->getFilesystemPath());
    }

    /**
     * It should throw an exception if the base dir is not .configured.
     */
    public function testBasedir()
    {
        $this->expectException(MissingOptionsException::class);
        $this->expectExceptionMessage('The required option "base_dir" is missing.');

        $this->buildContainer(
            $this->resolveOptions([
            ])
        );
    }

    protected function getFactory()
    {
        return new FilesystemFactory();
    }
}
