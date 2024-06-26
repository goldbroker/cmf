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

class ImageRepositoryTest extends BaseTestCase
{
    public static function getKernelClass(): string
    {
        return \Tests\Symfony\Cmf\Bundle\MediaBundle\Fixtures\App\Kernel::class;
    }

    public function setUp(): void
    {
        $this->db('PHPCR')->loadFixtures(array(
            'Tests\Symfony\Cmf\Bundle\MediaBundle\Resources\DataFixtures\Phpcr\LoadMediaData',
        ));
    }

    public function imageSearchProvider(): array
    {
        return array(
            array('/',             2, 'logo', 'cmf-logo.png'),
            array('/test/content', 1, 'logo', 'cmf-logo-2.png'),
            array('/test/content', 0, 'cantfindme', ''),
        );
    }

    /**
     * Test the ImageRepository search.
     *
     * @param string $rootPath
     * @param int    $resultsCount
     * @param string $term
     * @param string $name
     *
     * @dataProvider imageSearchProvider
     */
    public function testSearch($rootPath, $resultsCount, $term, $name)
    {
        $imageClass = 'Symfony\Cmf\Bundle\MediaBundle\Doctrine\Phpcr\Image';
        $dm = $this->getContainer()->get('doctrine_phpcr.odm.default_document_manager');
        $repo = $dm->getRepository($imageClass);
        $repo->setRootPath($rootPath);
        $results = $repo->searchImages($term);
        $this->assertEquals($resultsCount, $results->count());
        if ($resultsCount > 0) {
            $this->assertInstanceOf($imageClass, $results->first());
            $this->assertEquals($name, $results->first()->getName());
        }
    }
}
